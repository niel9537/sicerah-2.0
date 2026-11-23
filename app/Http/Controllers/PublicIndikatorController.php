<?php

namespace App\Http\Controllers;

use App\Models\RpjmdIndicatorSource;
use App\Services\IndicatorDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicIndikatorController extends Controller
{
    public function index(Request $request, IndicatorDataService $indicatorData): View
    {
        $tahunRange = range(2024, 2030);
        $tahun = (int) $request->integer('tahun', min(max((int) now()->year, 2024), 2030));
        if (!in_array($tahun, $tahunRange, true)) {
            $tahun = min(max((int) now()->year, 2024), 2030);
        }

        // Jenis: IKU, IKK, IKD (default IKD = Bank Data)
        $jenis = strtoupper((string) $request->input('jenis', 'IKD'));
        if (!in_array($jenis, ['IKU', 'IKK', 'IKD'], true)) {
            $jenis = 'IKD';
        }

        $q = trim((string) $request->input('q', ''));
        $aspek = trim((string) $request->input('aspek', ''));
        $opd = trim((string) $request->input('opd', ''));

        // Ambil sources berdasarkan jenis
        $query = RpjmdIndicatorSource::with([
            'indikator.realisasis' => fn($q) => $q->whereBetween('tahun', [2024, 2030]),
        ])->orderBy('file')->orderBy('baris');

        if ($jenis === 'IKU') {
            $query->where('jenis', 'IKU');
        } elseif ($jenis === 'IKK') {
            $query->where('jenis', 'IKK');
        } else {
            // IKD = Bank Data
            $query->where('jenis', 'IKD');
        }

        if ($q) {
            $query->where('nama', 'like', "%{$q}%");
        }
        if ($aspek) {
            $query->where('kelompok', $aspek);
        }
        if ($opd) {
            $query->where('perangkat_daerah', 'like', "%{$opd}%");
        }

        $sources = $query->get();

        $indikatorList = $sources
            ->map(fn(RpjmdIndicatorSource $source) => $indicatorData->presentRpjmd($source, $tahunRange, $tahun))
            ->values();

        // Daftar filter unik
        $aspekList = RpjmdIndicatorSource::where('jenis', $jenis)
            ->whereNotNull('kelompok')
            ->distinct()
            ->orderBy('kelompok')
            ->pluck('kelompok');

        $opdList = RpjmdIndicatorSource::where('jenis', $jenis)
            ->whereNotNull('perangkat_daerah')
            ->distinct()
            ->orderBy('perangkat_daerah')
            ->pluck('perangkat_daerah');

        // Counts per jenis untuk tab
        $counts = [
            'IKD' => RpjmdIndicatorSource::where('jenis', 'IKD')->count(),
            'IKU' => RpjmdIndicatorSource::where('jenis', 'IKU')->count(),
            'IKK' => RpjmdIndicatorSource::where('jenis', 'IKK')->count(),
        ];

        return view('indikator.publik', compact(
            'tahun', 'tahunRange', 'jenis', 'q', 'aspek', 'opd',
            'indikatorList', 'aspekList', 'opdList', 'counts'
        ));
    }
}
