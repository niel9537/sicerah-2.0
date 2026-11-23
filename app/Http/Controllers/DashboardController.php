<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiRenja;
use App\Models\Lkpj;
use App\Models\RealisasiTriwulanan;
use App\Models\RpjmdIndicatorSource;
use App\Models\SubKegiatan;
use App\Models\TargetTahunan;
use App\Services\IndicatorDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, IndicatorDataService $indicatorData): View
    {
        $tahun = (int) $request->input('tahun', now()->year);

        // Stats dari dokumen resmi RPJMD 2025–2030
        $stats = [
            'total_rpjmd' => RpjmdIndicatorSource::count(),
            'iku'         => RpjmdIndicatorSource::where('jenis', 'IKU')->count(),
            'ikd'         => RpjmdIndicatorSource::where('jenis', 'IKD')->count(),
            'ikk'         => RpjmdIndicatorSource::where('jenis', 'IKK')->count(),
            'lkpj'        => Lkpj::where('tahun', $tahun)->count(),
            'evaluasi'    => EvaluasiRenja::whereHas('periode', fn ($q) => $q->where('tahun', $tahun))->count(),
        ];

        // Overview sebaran indikator RPJMD per lingkup dan kelompok
        $indicatorOverview = [
            'total'   => $stats['total_rpjmd'],
            'daerah'  => RpjmdIndicatorSource::whereNull('perangkat_daerah')->count(),
            'opd'     => RpjmdIndicatorSource::whereNotNull('perangkat_daerah')->count(),
            'groups'  => RpjmdIndicatorSource::whereNotNull('kelompok')
                ->groupBy('kelompok')
                ->selectRaw('kelompok, count(*) as aggregate')
                ->orderByDesc('aggregate')
                ->pluck('aggregate', 'kelompok'),
        ];

        // 12 IKU Daerah untuk sorotan utama kinerja RPJMD
        $ikuSources = RpjmdIndicatorSource::where('jenis', 'IKU')
            ->with(['indikator.realisasis' => fn ($q) => $q->where('tahun', $tahun)])
            ->orderBy('baris')
            ->get();

        $macroHighlights = $ikuSources->map(
            fn (RpjmdIndicatorSource $src) => $indicatorData->presentRpjmd($src, range(2024, 2030), $tahun)
        )->values();

        // Progress pengisian: berapa sub kegiatan yg sudah punya realisasi TW berjalan
        $totalSub    = SubKegiatan::count();
        $sudahIsiTw  = [];
        foreach ([1, 2, 3, 4] as $tw) {
            $sudahIsiTw[$tw] = RealisasiTriwulanan::forPeriode($tahun, $tw)->count();
        }

        // Grafik: total anggaran vs realisasi per triwulan (dalam juta)
        $chart = ['labels' => ['TW I', 'TW II', 'TW III', 'TW IV'], 'target' => [], 'realisasi' => []];
        $totalTarget = TargetTahunan::where('tahun', $tahun)->get()->sum(fn ($t) => $t->paguEfektif());
        foreach ([1, 2, 3, 4] as $tw) {
            $chart['target'][]    = round($totalTarget / 1_000_000, 0);
            $chart['realisasi'][] = round(RealisasiTriwulanan::forPeriode($tahun, $tw)->sum('realisasi_anggaran') / 1_000_000, 0);
        }

        // Aktivitas terbaru
        $aktivitas = EvaluasiRenja::with(['subKegiatan', 'periode', 'creator'])
            ->latest()->limit(5)->get();

        // Kapan data kinerja tahun ini terakhir disentuh
        $terakhirDiperbarui = collect()
            ->push(RealisasiTriwulanan::where('tahun', $tahun)->max('updated_at'))
            ->push(EvaluasiRenja::whereHas('periode', fn ($q) => $q->where('tahun', $tahun))->max('updated_at'))
            ->filter()
            ->map(fn ($t) => \Illuminate\Support\Carbon::parse($t))
            ->sort()
            ->last();

        return view('dashboard', compact(
            'stats',
            'sudahIsiTw',
            'totalSub',
            'chart',
            'aktivitas',
            'tahun',
            'terakhirDiperbarui',
            'indicatorOverview',
            'macroHighlights'
        ));
    }
}
