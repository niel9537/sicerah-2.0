<?php

namespace App\Services;

use App\Models\Indikator;
use App\Models\IndikatorTarget;
use App\Models\Opd;
use App\Models\RpjmdIndicatorSource;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RpjmdIndicatorImporter
{
    public const FILES = ['IKD Daerah.xlsx' => 'IKD', 'IKK Daerah.xlsx' => 'IKK', 'IKU DAERAH.xlsx' => 'IKU', 'IKD Daerah berdasarkan Perangkat Daerah.xlsx' => 'IKD', 'IKK Daerah berdasarkan Perangkat Daerah.xlsx' => 'IKK'];

    public function key(string $value): string
    {
        return mb_strtolower(preg_replace('/\s+/u', ' ', trim($value)));
    }

    public function parse(string $path, string $jenis): array
    {
        $book = IOFactory::load($path);
        try {
            $rows = $book->getSheet(0)->toArray(null, false, false, false);
            if (($rows[4][1] ?? '') !== 'INDIKATOR') {
                throw new \RuntimeException('Header INDIKATOR tidak sesuai: '.basename($path));
            }
            $years = [];
            foreach ($rows[5] as $column => $year) {
                if (preg_match('/^20\d{2}$/', trim((string) $year))) {
                    $years[$column] = (int) $year;
                }
            }
            if (array_values($years) !== range(2025, 2030)) {
                throw new \RuntimeException('Target tahun harus 2025–2030: '.basename($path));
            }
            $result = [];
            $opd = $group = null;
            $unit = $jenis === 'IKK' ? 3 : 2;
            foreach (array_slice($rows, 7, null, true) as $index => $row) {
                $name = trim((string) ($row[1] ?? ''));
                $first = trim((string) ($row[0] ?? ''));
                $heading = $name ?: $first;
                if (preg_match('/^\d\.\d{2}\.\d\.\d{2}\.\d\.\d{2}\.\d{2}\.\d{4}\s*-\s*/', $heading)) {
                    $opd = $heading;
                    continue;
                }
                if ($name === '') {
                    continue;
                }
                if (! preg_match('/^\d+\.?$/', $first) || trim((string) ($row[$unit] ?? '')) === '') {
                    $group = $name;
                    continue;
                }
                $targets = [];
                foreach ($years as $column => $year) {
                    $targets[$year] = trim((string) ($row[$column] ?? ''));
                }
                $result[] = [
                    'file' => basename($path), 'baris' => $index + 1, 'jenis' => $jenis,
                    'nama' => $name, 'satuan' => trim((string) $row[$unit]),
                    'perangkat_daerah' => $opd, 'kelompok' => $group,
                    'arah' => $jenis === 'IKK' ? trim((string) $row[2]) : null,
                    'baseline_2024' => $jenis === 'IKU' ? null : trim((string) $row[$unit + 1]),
                    'targets' => $targets, 'keterangan' => trim((string) end($row)),
                ];
            }
            if (! $result) {
                throw new \RuntimeException('Tidak ada indikator: '.basename($path));
            }
            return $result;
        } finally {
            $book->disconnectWorksheets();
        }
    }

    public function import(string $directory): array
    {
        $records = [];
        foreach (self::FILES as $file => $jenis) {
            $records = array_merge($records, $this->parse($directory.DIRECTORY_SEPARATOR.$file, $jenis));
        }
        return DB::transaction(function () use ($records) {
            $existing = Indikator::all()->groupBy(fn ($i) => $i->jenis.'|'.$this->key($i->nama));
            $counts = ['IKD' => 0, 'IKK' => 0, 'IKU' => 0, 'sumber' => 0, 'duplikat_lama' => [], 'opd_belum_dipetakan' => []];
            $seen = [];
            $opds = Opd::all();
            foreach ($records as $record) {
                $key = $record['jenis'].'|'.$this->key($record['nama']);
                $matches = $existing->get($key, collect());
                if ($matches->count() > 1) {
                    $counts['duplikat_lama'][$key] = $matches->pluck('id')->all();
                }
                // Utamakan indikator Bank Data yang sudah terhubung; jangan hapus riwayat duplikat lama.
                $indicator = $matches->sortBy(fn ($i) => [$i->bank_data_row === null ? 1 : 0, $i->id])->first()
                    ?? new Indikator(['jenis' => $record['jenis'], 'nama' => $record['nama']]);
                if (! isset($seen[$key])) {
                    $indicator->satuan = $record['satuan'];
                    $indicator->save();
                    $existing->put($key, collect([$indicator]));
                    // File daerah adalah acuan target; versi per OPD tetap disimpan terpisah.
                    foreach ($record['targets'] as $year => $target) {
                        if ($target !== '') {
                            IndikatorTarget::updateOrCreate(['indikator_id' => $indicator->id, 'tahun' => $year], ['target' => $target]);
                        }
                    }
                    $seen[$key] = true;
                    $counts[$record['jenis']]++;
                }
                if ($record['perangkat_daerah']) {
                    [$code, $name] = explode(' - ', $record['perangkat_daerah'], 2);
                    $opd = $opds->firstWhere('kode', $code)
                        ?? $opds->first(fn ($item) => $this->key((string) $item->nama) === $this->key($name));
                    if ($opd && ! $indicator->opd_id) {
                        $indicator->update(['opd_id' => $opd->id]);
                    } elseif (! $opd) {
                        $counts['opd_belum_dipetakan'][$code] = $name;
                    }
                }
                RpjmdIndicatorSource::updateOrCreate(['file' => $record['file'], 'baris' => $record['baris']], $record + ['indikator_id' => $indicator->id]);
                $counts['sumber']++;
            }
            return $counts;
        });
    }
}
