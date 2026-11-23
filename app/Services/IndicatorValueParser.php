<?php

namespace App\Services;

/**
 * Mengubah nilai indikator yang beragam menjadi bentuk terstruktur tanpa
 * kehilangan teks aslinya. Rentang digambarkan memakai titik tengah, sambil
 * tetap menyimpan batas bawah dan batas atas untuk tooltip/audit.
 */
class IndicatorValueParser
{
    /**
     * @return array{raw:?string,type:string,numeric:?float,min:?float,max:?float,graph:?float}
     */
    public function parse(mixed $value): array
    {
        if ($value === null) {
            return $this->emptyResult(null);
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return $this->emptyResult('');
        }
        if (in_array($raw, ['-', '–', '—'], true)) {
            return $this->emptyResult($raw);
        }

        $text = str_replace(["\u{00A0}", "\u{202F}"], ' ', $raw);
        $number = '[+-]?\d(?:[\d.,\s]*\d)?';
        $separator = '(?:-|\x{2013}|\x{2014}|s\.?\s*\/?\s*d\.?|sampai|hingga)';

        if (preg_match('/^\s*('.$number.')\s*'.$separator.'\s*('.$number.')\s*(?:%|\*+)?\s*$/iu', $text, $matches)) {
            $first = $this->normalizeNumber($matches[1]);
            $second = $this->normalizeNumber($matches[2]);

            if ($first !== null && $second !== null) {
                $min = min($first, $second);
                $max = max($first, $second);

                return [
                    'raw' => $raw,
                    'type' => 'range',
                    'numeric' => null,
                    'min' => $min,
                    'max' => $max,
                    'graph' => ($first + $second) / 2,
                ];
            }
        }

        if (preg_match('/^\s*(?:[<>~\x{2264}\x{2265}]\s*)?('.$number.')\s*(?:%|\*+)?\s*$/u', $text, $matches)) {
            $numeric = $this->normalizeNumber($matches[1]);

            if ($numeric !== null) {
                return [
                    'raw' => $raw,
                    'type' => 'number',
                    'numeric' => $numeric,
                    'min' => $numeric,
                    'max' => $numeric,
                    'graph' => $numeric,
                ];
            }
        }

        // Beberapa sel Bank Data menambahkan grade pada angka (mis. "77,35 (B)")
        // atau menyisakan ekspresi Excel sebagai teks ("89+M119"). Ambil angka
        // utama untuk grafik, tetapi raw tetap utuh agar anomali sumber dapat diaudit.
        if (preg_match('/^\s*('.$number.')\s*(?:\([\pL\pN ._-]{1,24}\)|\+[A-Z]{1,4}\d+)\s*(?:%|\*+)?\s*$/u', $text, $matches)) {
            $numeric = $this->normalizeNumber($matches[1]);

            if ($numeric !== null) {
                return [
                    'raw' => $raw,
                    'type' => 'number',
                    'numeric' => $numeric,
                    'min' => $numeric,
                    'max' => $numeric,
                    'graph' => $numeric,
                ];
            }
        }

        return [
            'raw' => $raw,
            'type' => 'text',
            'numeric' => null,
            'min' => null,
            'max' => null,
            'graph' => null,
        ];
    }

    public function numericValue(mixed $value): ?float
    {
        return $this->parse($value)['graph'];
    }

    private function normalizeNumber(string $value): ?float
    {
        $value = preg_replace('/[\s\x{00A0}\x{202F}]/u', '', trim($value));
        if ($value === null || $value === '') {
            return null;
        }

        $dotCount = substr_count($value, '.');
        $commaCount = substr_count($value, ',');

        if ($dotCount > 0 && $commaCount > 0) {
            $lastDot = strrpos($value, '.');
            $lastComma = strrpos($value, ',');
            $decimalSeparator = $lastComma > $lastDot ? ',' : '.';
            $thousandSeparator = $decimalSeparator === ',' ? '.' : ',';
            $value = str_replace($thousandSeparator, '', $value);
            $value = str_replace($decimalSeparator, '.', $value);
        } elseif ($commaCount > 0) {
            $value = $this->normalizeSingleSeparator($value, ',');
        } elseif ($dotCount > 1) {
            $value = $this->normalizeSingleSeparator($value, '.');
        }

        if (! is_numeric($value)) {
            return null;
        }

        $number = (float) $value;

        return is_finite($number) ? $number : null;
    }

    private function normalizeSingleSeparator(string $value, string $separator): string
    {
        $parts = explode($separator, $value);

        if (count($parts) === 2) {
            return $separator === ',' ? implode('.', $parts) : $value;
        }

        $last = array_pop($parts);
        $middleParts = array_slice($parts, 1);
        $allThousands = $last !== null && strlen($last) === 3
            && array_reduce($middleParts, fn (bool $valid, string $part) => $valid && strlen($part) === 3, true);

        if ($allThousands) {
            return implode('', array_merge($parts, [$last]));
        }

        return implode('', $parts).'.'.$last;
    }

    /**
     * @return array{raw:?string,type:string,numeric:?float,min:?float,max:?float,graph:?float}
     */
    private function emptyResult(?string $raw): array
    {
        return [
            'raw' => $raw,
            'type' => 'empty',
            'numeric' => null,
            'min' => null,
            'max' => null,
            'graph' => null,
        ];
    }
}
