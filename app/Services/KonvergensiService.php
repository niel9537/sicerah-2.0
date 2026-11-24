<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class KonvergensiService
{
    public const BASE = 'https://konvergensi.bangda.kemendagri.go.id/emonev';
    public const PERIODS = [2026 => 8, 2025 => 7];
    public const DISTRICT = '1225';
    public const DISTRICT_COLUMNS = [
        'Kabupaten', 'Jumlah Desa', 'Data Sasaran (Desa Terisi)', 'Data Pendukung (Desa Terisi)',
        'Data Capaian Layanan (Desa Terisi)', 'Data Stunting (Desa Terisi)',
        'Target RPJMD (Target Terisi)', 'Target RKPD (Target Terisi)',
        'Identifikasi Kendala (Indikator Terisi)', 'Pramusrenbang (Pelaksanaan)',
        'Usulan Prioritas (Jumlah Kegiatan)', 'Tagging Berjalan (Jumlah Kegiatan)',
        'Tagging Rencana (Jumlah Kegiatan)', 'Data Sasaran Semester 1 (Desa Terisi)',
        'Data Pendukung Semester 1 (Desa Terisi)', 'Data Capaian Layanan Semester 1 (Desa Terisi)',
        'Data Sasaran Semester 2 (Desa Terisi)', 'Data Pendukung Semester 2 (Desa Terisi)',
        'Data Capaian Layanan Semester 2 (Desa Terisi)', 'Monitoring RKPD (Jumlah Kegiatan)',
        'Reviu Tahun Berjalan (Jumlah Kegiatan)', 'Regulasi (Jumlah Regulasi)', 'Progres (%)',
    ];
    public const SUBDISTRICT_COLUMNS = [
        'Kecamatan', 'Jumlah Desa', 'Data Sasaran (Desa Terisi)', 'Data Pendukung (Desa Terisi)',
        'Data Capaian Layanan (Desa Terisi)', 'Identifikasi Kendala (Indikator Terisi)',
        'Pramusrenbang (Pelaksanaan)', 'Usulan Prioritas (Jumlah Kegiatan)',
        'Tagging Berjalan (Jumlah Kegiatan)', 'Tagging Rencana (Jumlah Kegiatan)',
        'Data Sasaran Semester 1 (Desa Terisi)', 'Data Pendukung Semester 1 (Desa Terisi)',
        'Data Capaian Layanan Semester 1 (Desa Terisi)', 'Data Sasaran Semester 2 (Desa Terisi)',
        'Data Pendukung Semester 2 (Desa Terisi)', 'Data Capaian Layanan Semester 2 (Desa Terisi)',
        'Monitoring RKPD (Jumlah Kegiatan)', 'Reviu Tahun Berjalan (Jumlah Kegiatan)', 'Progres (%)',
    ];

    public function dashboard(int $year): array
    {
        if (!isset(self::PERIODS[$year])) throw new RuntimeException('Tahun data tidak tersedia.');

        $store = Cache::store(config('services.konvergensi.cache_store', 'file'));
        try {
            $data = $this->liveDashboard($year);
            $store->put('konvergensi:last-good:'.self::DISTRICT.':'.$year, $data, now()->addDays(90));
            return $data + ['is_fallback' => false];
        } catch (RuntimeException $exception) {
            $data = $store->get('konvergensi:last-good:'.self::DISTRICT.':'.$year);
            if (!$data) {
                $directory = config('services.konvergensi.snapshot_path', resource_path('data/konvergensi'));
                $file = $directory.'/'.$year.'.json';
                $data = is_file($file) ? json_decode(file_get_contents($file), true) : null;
            }
            if (!is_array($data) || ($data['year'] ?? null) !== $year || empty($data['district']['cells']) || empty($data['retrieved_at'])) throw $exception;
            return $data + ['is_fallback' => true];
        }
    }

    private function liveDashboard(int $year): array
    {

        return Cache::store(config('services.konvergensi.cache_store', 'file'))
            ->remember('konvergensi:v1:'.self::DISTRICT.':'.$year, now()->addHour(), function () use ($year) {
                $period = self::PERIODS[$year];
                $districts = $this->fetch('progreskab/12/'.$period, ['idprov' => '12']);
                $district = collect($districts)->first(fn ($row) => is_array($row) && (string) ($row['idkab'] ?? '') === self::DISTRICT);
                if (!$district) throw new RuntimeException('Data Kabupaten Nias Barat belum tersedia pada sumber untuk tahun ini.');
                $district = $this->row($district, count(self::DISTRICT_COLUMNS));
                $subdistricts = $this->fetch('progreskec/'.self::DISTRICT.'/'.$period, ['idkab' => self::DISTRICT]);
                $rows = [];
                foreach ($subdistricts as $row) {
                    // Never display another district even if the upstream response includes it.
                    if (!is_array($row) || !preg_match('/^1225\d{3}$/', (string) ($row['idkec'] ?? ''))) continue;
                    $rows[] = $this->row($row, count(self::SUBDISTRICT_COLUMNS));
                }
                return ['district' => $district, 'subdistricts' => $rows, 'year' => $year,
                    'source' => self::BASE.'/dashboard/'.$period, 'retrieved_at' => now()->toIso8601String()];
            });
    }

    private function fetch(string $path, array $query): array
    {
        try {
            $response = Http::acceptJson()->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
                ->connectTimeout(3)->timeout(8)->withOptions([
                    'allow_redirects' => false,
                    'verify' => config('services.konvergensi.ca_bundle') ?: true,
                ])
                ->get(self::BASE.'/'.$path, $query);
            $data = $response->json();
            if (!$response->successful() || !is_array($data) || !array_is_list($data)) throw new RuntimeException();
            return $data;
        } catch (Throwable $exception) {
            throw new RuntimeException('Layanan Konvergensi Kemendagri sedang tidak dapat diakses. Silakan coba kembali nanti.', 0, $exception);
        }
    }

    private function row(array $row, int $columns): array
    {
        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8"><table>'.($row['tabel'] ?? '').'</table>', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
            $xpath = new DOMXPath($document);
            foreach ($xpath->query('//script|//style') as $node) $node->parentNode->removeChild($node);
            $cells = [];
            foreach ($xpath->query('//tr/td') as $cell) $cells[] = trim(preg_replace('/\s+/u', ' ', $cell->textContent));
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        if (count($cells) !== $columns) throw new RuntimeException('Struktur tabel sumber berubah. Data belum dapat ditampilkan dengan aman.');
        // The final table cell preserves decimals; the separate upstream chart field is rounded.
        $progress = str_replace(',', '.', $cells[$columns - 1]);
        return ['cells' => $cells, 'progress' => is_numeric($progress) && (float) $progress >= 0 && (float) $progress <= 100 ? (float) $progress : null];
    }
}
