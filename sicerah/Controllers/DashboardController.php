<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiRenja;
use App\Models\Indikator;
use App\Models\Lkpj;
use App\Models\RealisasiTriwulanan;
use App\Models\SubKegiatan;
use App\Models\TargetTahunan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $tahun = (int) $request->input('tahun', now()->year);

        $stats = [
            'iku'      => Indikator::iku()->count(),
            'ikk'      => Indikator::ikk()->count(),
            'lkpj'     => Lkpj::where('tahun', $tahun)->count(),
            'evaluasi' => EvaluasiRenja::whereHas('periode', fn ($q) => $q->where('tahun', $tahun))->count(),
        ];

        // Progress pengisian: berapa sub kegiatan yg sudah punya realisasi TW berjalan
        $totalSub    = SubKegiatan::count();
        $sudahIsiTw  = [];
        foreach ([1, 2, 3, 4] as $tw) {
            $sudahIsiTw[$tw] = RealisasiTriwulanan::forPeriode($tahun, $tw)->count();
        }

        // Grafik: total anggaran vs realisasi per triwulan (dalam juta)
        $chart = ['labels' => ['TW I', 'TW II', 'TW III', 'TW IV'], 'target' => [], 'realisasi' => []];
        //$totalTarget = (float) TargetTahunan::where('tahun', $tahun)->sum('pagu_renja');
        $totalTarget = TargetTahunan::where('tahun', $tahun)->get()->sum(fn ($t) => $t->paguEfektif());
        foreach ([1, 2, 3, 4] as $tw) {
            $chart['target'][]    = round($totalTarget / 1_000_000, 0);
            $chart['realisasi'][] = round(RealisasiTriwulanan::forPeriode($tahun, $tw)->sum('realisasi_anggaran') / 1_000_000, 0);
        }

        // Aktivitas terbaru
        $aktivitas = EvaluasiRenja::with(['subKegiatan', 'periode', 'creator'])
            ->latest()->limit(5)->get();

        return view('dashboard', compact('stats', 'sudahIsiTw', 'totalSub', 'chart', 'aktivitas', 'tahun'));
    }
}