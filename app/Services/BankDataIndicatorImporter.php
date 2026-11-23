<?php

namespace App\Services;

use App\Models\Indikator;
use App\Models\IndikatorRealisasi;
use App\Models\IndikatorTarget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BankDataIndicatorImporter
{
    private const IKU = [
        'Indeks Pembangunan Manusia',
        'Laju Pertumbuhan Ekonomi',
        'Rasio Gini',
        'Rasio Kewirausahaan',
        'Indeks Ketahanan Pangan',
        'Indeks Infrastruktur',
        'Indeks Kualitas Lingkungan Hidup Daerah',
        'Usia Harapan Hidup',
        'Cakupan Jaminan Kesehatan Nasional (JKN)',
        'Indeks Ketimpangan Gender',
        'Indeks Reformasi Birokrasi',
        'Indeks Akses Keuangan Daerah',
    ];

    private const MAKRO = [
        'Laju Pertumbuhan Ekonomi',
        'PDRB Per Kapita',
        'Kontribusi PDRB Kabupaten/Kota',
        'Tingkat Kemiskinan',
        'Rasio Gini',
        'Indeks Pembangunan Manusia',
        'Penurunan Intesitas Emisi GRK (CO2)',
        'Indeks Kualitas Lingkungan Hidup Daerah',
        'Tingkat Pengangguran Terbuka',
    ];

    /**
     * Impor hanya baris dengan ASPEK terisi. Area IKK di bawah Bank Data tidak
     * pernah dibaca atau diubah oleh proses ini.
     *
     * @return array{indikator:int,iku:int,makro:int,realisasi:int,target:int,aspek:array<string,int>}
     */
    public function import(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName('Sheet1') ?? $spreadsheet->getActiveSheet();
        $rows = $sheet->rangeToArray('A1:P'.$sheet->getHighestDataRow(), null, true, true, false);

        if (count($rows) < 5) {
            throw new \RuntimeException('Bank Data tidak memiliki struktur baris yang diharapkan.');
        }

        $years = [];
        foreach (range(4, 14) as $columnIndex) {
            $year = (int) ($rows[1][$columnIndex] ?? 0);
            if ($year >= 2000 && $year <= 2100) {
                $years[$columnIndex] = $year;
            }
        }

        if (! isset($years[4], $years[14])) {
            throw new \RuntimeException('Header tahun Bank Data pada kolom Capaian/Target tidak ditemukan.');
        }

        $ikuNames = collect(self::IKU)->mapWithKeys(fn (string $name) => [$this->key($name) => true]);
        $macroNames = collect(self::MAKRO)->mapWithKeys(fn (string $name) => [$this->key($name) => true]);
        $indicatorRows = collect(array_slice($rows, 3, null, true))
            ->filter(function (array $row): bool {
                $number = trim((string) ($row[0] ?? ''));

                return trim((string) ($row[1] ?? '')) !== ''
                    && trim((string) ($row[2] ?? '')) !== ''
                    && preg_match('/^\d+(?:\.\d+)?$/', $number) === 1;
            })
            ->map(fn (array $row, int $zeroBasedIndex) => [
                'excel_row' => $zeroBasedIndex + 1,
                'row' => $row,
            ])
            ->values();

        if ($indicatorRows->count() !== 119) {
            throw new \RuntimeException("Validasi gagal: ditemukan {$indicatorRows->count()} baris ber-ASPEK, seharusnya 119.");
        }

        $sourceKeys = $indicatorRows->map(fn (array $item) => $this->key($item['row'][2] ?? ''));
        if ($sourceKeys->unique()->count() !== 119) {
            throw new \RuntimeException('Validasi gagal: terdapat nama indikator ber-ASPEK yang duplikat di Bank Data.');
        }
        $missingIku = $ikuNames->keys()->diff($sourceKeys);
        $missingMacro = $macroNames->keys()->diff($sourceKeys);
        if ($missingIku->isNotEmpty() || $missingMacro->isNotEmpty()) {
            throw new \RuntimeException('Validasi gagal: daftar IKU atau indikator makro tidak lengkap di Bank Data.');
        }

        $existing = Indikator::whereIn('jenis', ['IKD', 'IKU'])
            ->get()
            ->keyBy(fn (Indikator $indicator) => $this->key($indicator->nama));

        $summary = [
            'indikator' => 0,
            'iku' => 0,
            'makro' => 0,
            'realisasi' => 0,
            'target' => 0,
            'aspek' => [],
        ];

        DB::transaction(function () use ($indicatorRows, $years, $ikuNames, $macroNames, &$existing, &$summary): void {
            $order = 0;

            foreach ($indicatorRows as $item) {
                $excelRow = $item['excel_row'];
                $row = $item['row'];
                $aspect = trim((string) ($row[1] ?? ''));
                $name = trim((string) ($row[2] ?? ''));

                $order++;
                $nameKey = $this->key($name);
                $isIku = $ikuNames->has($nameKey);
                $isMacro = $macroNames->has($nameKey);
                $notes = trim((string) ($row[15] ?? ''));
                $source = $this->sourceFromNotes($notes);

                $indicator = Indikator::where('bank_data_row', $excelRow)->first()
                    ?? $existing->get($nameKey)
                    ?? new Indikator();

                $indicator->fill([
                    'kode' => sprintf('IKD-BD-%03d', $order),
                    'jenis' => 'IKD',
                    'aspek' => $aspect,
                    'is_iku' => $isIku,
                    'is_makro' => $isMacro,
                    'bank_data_row' => $excelRow,
                    'urutan' => $order,
                    'nama' => $name,
                    'satuan' => trim((string) ($row[3] ?? '')) ?: null,
                    'sumber_data' => $source,
                    'keterangan' => $notes ?: null,
                ]);
                $indicator->updated_at = now();
                $indicator->save();

                $existing->put($nameKey, $indicator);
                $summary['indikator']++;
                $summary['iku'] += $isIku ? 1 : 0;
                $summary['makro'] += $isMacro ? 1 : 0;
                $summary['aspek'][$aspect] = ($summary['aspek'][$aspect] ?? 0) + 1;

                foreach ($years as $columnIndex => $year) {
                    $value = $this->cellText($row[$columnIndex] ?? null);

                    if ($year <= 2024) {
                        $identity = [
                            'indikator_id' => $indicator->id,
                            'tahun' => $year,
                            'triwulan' => 4,
                        ];

                        if ($value === null) {
                            IndikatorRealisasi::where($identity)->delete();
                        } else {
                            // Basis data lama tidak memiliki unique key komposit; bersihkan
                            // duplikat historis agar satu tahun hanya punya satu capaian TW IV.
                            IndikatorRealisasi::where($identity)->delete();
                            IndikatorRealisasi::create(array_merge($identity, [
                                'realisasi' => $value,
                                'status' => 'diverifikasi',
                            ]));
                            $summary['realisasi']++;
                        }
                    } else {
                        $identity = ['indikator_id' => $indicator->id, 'tahun' => $year];

                        if ($value === null) {
                            IndikatorTarget::where($identity)->delete();
                        } else {
                            IndikatorTarget::where($identity)->delete();
                            IndikatorTarget::create(array_merge($identity, ['target' => $value]));
                            $summary['target']++;
                        }
                    }
                }
            }

            if ($summary['indikator'] !== 119 || $summary['iku'] !== 12 || $summary['makro'] !== 9) {
                throw new \RuntimeException('Sinkronisasi dibatalkan karena hasil klasifikasi IKD, IKU, atau indikator makro tidak konsisten.');
            }
        });

        return $summary;
    }

    private function key(?string $value): string
    {
        return Str::of((string) $value)
            ->lower()
            ->replaceMatches('/\*+$/', '')
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->toString();
    }

    private function cellText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);

        return $text === '' ? null : Str::limit($text, 100, '');
    }

    private function sourceFromNotes(string $notes): ?string
    {
        if ($notes === '') {
            return null;
        }

        $firstLine = trim((string) preg_split('/\R/u', $notes)[0]);
        $firstLine = preg_replace('/^\*?\s*sumber\s*:\s*/iu', '', $firstLine) ?? $firstLine;

        return Str::limit(ltrim($firstLine, "* \t"), 255, '');
    }
}
