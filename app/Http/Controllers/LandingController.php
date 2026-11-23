<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiRenja;
use App\Models\Indikator;
use App\Models\JdihDokumen;
use App\Models\Lkpj;
use App\Models\Opd;
use App\Models\RealisasiTriwulanan;
use App\Models\RpjmdIndicatorSource;
use App\Models\SubKegiatan;
use App\Services\IndicatorDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(Request $request, IndicatorDataService $indicatorData): View
    {
        $tahunRange = range(2024, 2030);
        $tahun = (int) $request->integer('tahun', min(max((int) now()->year, 2024), 2030));
        if (! in_array($tahun, $tahunRange, true)) {
            $tahun = min(max((int) now()->year, 2024), 2030);
        }

        // Ambil seluruh 1.065 indikator resmi dari dokumen RPJMD 2025–2030
        $sources = RpjmdIndicatorSource::with([
            'indikator.realisasis' => fn ($query) => $query->whereBetween('tahun', [2024, 2030]),
        ])
            ->orderBy('file')
            ->orderBy('baris')
            ->get();

        $indikatorRpjmd = $sources
            ->map(fn (RpjmdIndicatorSource $source) => $indicatorData->presentRpjmd($source, $tahunRange, $tahun))
            ->values();

        $indikatorDaerah = $indikatorRpjmd;
        $capaianIku = $indikatorRpjmd->where('jenis', 'IKU')->values();
        $indikatorMakro = $capaianIku;
        $aspek = $sources->whereNotNull('kelompok')->groupBy('kelompok')->map->count();
        $opdRpjmdList = $sources->whereNotNull('perangkat_daerah')->pluck('perangkat_daerah')->unique()->sort()->values();

        $counts = [
            'total_rpjmd' => $sources->count(),
            'iku'         => $sources->where('jenis', 'IKU')->count(),
            'ikk'         => $sources->where('jenis', 'IKK')->count(),
            'ikd'         => $sources->where('jenis', 'IKD')->count(),
            'daerah'      => $sources->whereNull('perangkat_daerah')->count(),
            'makro'       => $capaianIku->count(),
            'aspek'       => $aspek->count(),
            'opd'         => $opdRpjmdList->count(),
            'subkegiatan' => SubKegiatan::count(),
            'lkpj'        => Lkpj::where('tahun', $tahun)->count(),
            'evaluasi'    => EvaluasiRenja::whereHas('periode', fn ($query) => $query->where('tahun', $tahun))->count(),
            'jdih'        => JdihDokumen::published()->count(),
        ];

        $kinerja = $this->kinerjaDaerah($tahun);
        $tren = $this->trenKumulatif($tahun, $kinerja['total_pagu']);
        $ringkasanMakro = $this->buildRingkasanMakro($tahun);

        $jdihTerbaru = JdihDokumen::published()
            ->orderByDesc('tahun')
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        $opdList = Opd::whereNotNull('nama')->orderBy('nama')->get();
        $terakhirDiperbarui = collect($indikatorRpjmd)
            ->flatMap(fn (array $item) => collect($item['points'])->pluck('updated_at'))
            ->merge($kinerja['timestamps'])
            ->filter()
            ->sort()
            ->last();

        unset($kinerja['timestamps']);

        $carouselPublications = \App\Models\Publication::forCarousel()->get();

        return view('landing', compact(
            'carouselPublications', 'tahun', 'tahunRange', 'counts', 'aspek', 'indikatorDaerah', 'indikatorRpjmd',
            'capaianIku', 'indikatorMakro', 'ringkasanMakro', 'opdRpjmdList', 'kinerja', 'tren',
            'jdihTerbaru', 'opdList', 'terakhirDiperbarui'
        ));
    }

    /** @return array<string,mixed> */
    private function kinerjaDaerah(int $tahun): array
    {
        $hasData = RealisasiTriwulanan::where('tahun', $tahun)->exists();
        $quarter = (int) (RealisasiTriwulanan::where('tahun', $tahun)
            ->whereNotNull('realisasi_anggaran')->max('triwulan') ?: 4);

        $subActivities = SubKegiatan::with([
            'targetTahunans' => fn ($query) => $query->where('tahun', $tahun),
            'realisasiTriwulanans' => fn ($query) => $query->where('tahun', $tahun)->where('triwulan', $quarter),
        ])->get();

        $physical = collect();
        $financial = collect();
        $budget = 0.0;
        $realised = 0.0;
        $timestamps = collect();

        foreach ($subActivities as $subActivity) {
            $target = $subActivity->targetTahunans->first();
            $actual = $subActivity->realisasiTriwulanans->first();
            $physicalTarget = (float) ($target->target_kinerja ?? 0);
            $physicalActual = (float) ($actual->realisasi_kinerja ?? 0);
            $effectiveBudget = $target ? (float) $target->paguEfektif() : 0.0;
            $actualBudget = (float) ($actual->realisasi_anggaran ?? 0);

            if ($physicalTarget > 0) {
                $physical->push(min(200, round($physicalActual / $physicalTarget * 100, 1)));
            }
            if ($effectiveBudget > 0) {
                $financial->push(min(200, round($actualBudget / $effectiveBudget * 100, 1)));
            }

            $budget += $effectiveBudget;
            $realised += $actualBudget;
            if ($actual?->updated_at) {
                $timestamps->push($actual->updated_at);
            }
        }

        return [
            'has_data' => $hasData && ($physical->isNotEmpty() || $budget > 0),
            'rata_fisik' => $physical->isNotEmpty() ? round((float) $physical->avg(), 1) : 0,
            'rata_keuangan' => $financial->isNotEmpty() ? round((float) $financial->avg(), 1) : 0,
            'total_pagu' => $budget,
            'total_realisasi' => $realised,
            'triwulan' => $quarter,
            'timestamps' => $timestamps,
        ];
    }

    /** @return array<string,mixed> */
    private function trenKumulatif(int $tahun, float $totalPagu): array
    {
        $hasData = RealisasiTriwulanan::where('tahun', $tahun)->exists();
        $labels = ['TW I', 'TW II', 'TW III', 'TW IV'];
        $realisasiAnggaran = [];
        $fisikKumulatif = [];
        $keuanganKumulatif = [];

        $accumulatedBudget = 0.0;
        foreach ([1, 2, 3, 4] as $quarter) {
            $qBudget = (float) RealisasiTriwulanan::where('tahun', $tahun)->where('triwulan', $quarter)->sum('realisasi_anggaran');
            $qKinerja = (float) RealisasiTriwulanan::where('tahun', $tahun)->where('triwulan', $quarter)->avg('realisasi_kinerja');
            
            $realisasiAnggaran[] = round($qBudget / 1_000_000, 0);

            // Jika triwulan 4 sudah ada nilai total, gunakan nilai kuartal
            if ($totalPagu > 0) {
                $accumulatedBudget += $qBudget;
                $pctKeuangan = min(100, round(($accumulatedBudget / $totalPagu) * 100, 1));
            } else {
                $pctKeuangan = 0;
            }

            $keuanganKumulatif[] = $pctKeuangan;
            $fisikKumulatif[] = round($qKinerja, 1);
        }

        return [
            'has_data' => $hasData,
            'labels' => $labels,
            'realisasi' => $realisasiAnggaran,
            'fisik' => $fisikKumulatif,
            'keuangan' => $keuanganKumulatif,
        ];
    }

    /** @return array<string,array<string,mixed>> */
    private function buildRingkasanMakro(int $tahun): array
    {
        $indicators = [
            'ipm' => [
                'nama' => 'Indeks Pembangunan Manusia (IPM)',
                'label' => 'IPM',
                'icon' => 'user',
                'source' => RpjmdIndicatorSource::where('nama', 'LIKE', '%Pembangunan Manusia%')->where('jenis', 'IKU')->first()
                    ?? RpjmdIndicatorSource::where('nama', 'LIKE', '%Pembangunan Manusia%')->first(),
                'arah' => 'positif',
            ],
            'pertumbuhan_ekonomi' => [
                'nama' => 'Laju Pertumbuhan Ekonomi',
                'label' => 'Pertumbuhan Ekonomi',
                'icon' => 'trending-up',
                'source' => RpjmdIndicatorSource::where('nama', 'LIKE', '%Pertumbuhan Ekonomi%')->where('jenis', 'IKU')->first()
                    ?? RpjmdIndicatorSource::where('nama', 'LIKE', '%Pertumbuhan Ekonomi%')->first(),
                'arah' => 'positif',
            ],
            'rasio_gini' => [
                'nama' => 'Rasio Gini',
                'label' => 'Rasio Gini',
                'icon' => 'scale',
                'source' => RpjmdIndicatorSource::where('nama', 'LIKE', '%Gini%')->where('jenis', 'IKU')->first()
                    ?? RpjmdIndicatorSource::where('nama', 'LIKE', '%Gini%')->first(),
                'arah' => 'negatif', // Makin kecil makin baik
            ],
            'kemiskinan' => [
                'nama' => 'Tingkat Kemiskinan',
                'label' => 'Tingkat Kemiskinan',
                'icon' => 'shield-alert',
                'source' => RpjmdIndicatorSource::where('nama', 'LIKE', '%Kemiskinan%')->where('nama', 'NOT LIKE', '%Keparahan%')->first(),
                'arah' => 'negatif', // Makin kecil makin baik
            ],
            'pengangguran' => [
                'nama' => 'Tingkat Pengangguran Terbuka',
                'label' => 'Pengangguran Terbuka',
                'icon' => 'briefcase',
                'source' => RpjmdIndicatorSource::where('nama', 'LIKE', '%Pengangguran%')->first(),
                'arah' => 'negatif', // Makin kecil makin baik
            ],
        ];

        $result = [];
        foreach ($indicators as $key => $meta) {
            $src = $meta['source'];
            if (! $src) {
                continue;
            }

            $targets = (array) ($src->targets ?? []);
            $baseline = $src->baseline_2024;
            $valRaw = $targets[(string) $tahun] ?? ($tahun === 2024 ? $baseline : ($targets['2025'] ?? $baseline));
            $prevYear = $tahun - 1;
            $prevRaw = $targets[(string) $prevYear] ?? ($prevYear === 2024 ? $baseline : null);

            $valNum = (float) str_replace(['.', ','], ['', '.'], (string) $valRaw);
            $prevNum = $prevRaw !== null ? (float) str_replace(['.', ','], ['', '.'], (string) $prevRaw) : null;

            $delta = null;
            $deltaFormatted = null;
            $isGood = true;

            if ($prevNum !== null && $prevNum > 0 && $valNum > 0) {
                $delta = round($valNum - $prevNum, 3);
                $sign = $delta >= 0 ? '+' : '';
                $deltaFormatted = $sign . number_format($delta, ($key === 'rasio_gini' ? 3 : 2), ',', '.');
                
                // Cek arah capaian
                if ($meta['arah'] === 'negatif') {
                    $isGood = $delta <= 0; // Turun adalah capaian positif
                } else {
                    $isGood = $delta >= 0; // Naik adalah capaian positif
                }
            }

            // Bangun deret tren sparkline 2024-2030
            $sparkline = [];
            if ($baseline) {
                $sparkline[] = (float) str_replace(['.', ','], ['', '.'], (string) $baseline);
            }
            foreach (range(2025, 2030) as $y) {
                if (isset($targets[(string) $y])) {
                    $sparkline[] = (float) str_replace(['.', ','], ['', '.'], (string) $targets[(string) $y]);
                }
            }

            $result[$key] = [
                'id' => $src->id,
                'nama' => $src->nama,
                'label' => $meta['label'],
                'icon' => $meta['icon'],
                'satuan' => $src->satuan ?: '',
                'nilai' => $valRaw ?: ($baseline ?: '—'),
                'delta' => $deltaFormatted,
                'delta_raw' => $delta,
                'is_good' => $isGood,
                'arah' => $meta['arah'],
                'sparkline' => $sparkline,
            ];
        }

        return $result;
    }
}
