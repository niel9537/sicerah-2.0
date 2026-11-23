<?php

namespace App\Http\Controllers;

use App\Models\RpjmdIndicatorSource;
use Illuminate\Http\Request;

class RpjmdController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'jenis' => 'nullable|in:IKD,IKK,IKU', 'q' => 'nullable|string|max:255',
            'perangkat_daerah' => 'nullable|string|max:255', 'lingkup' => 'nullable|in:daerah,opd',
        ]);
        $items = RpjmdIndicatorSource::with('indikator')
            ->when($filters['jenis'] ?? null, fn ($q, $v) => $q->where('jenis', $v))
            ->when($filters['q'] ?? null, fn ($q, $v) => $q->where('nama', 'like', "%{$v}%"))
            ->when($filters['perangkat_daerah'] ?? null, fn ($q, $v) => $q->where('perangkat_daerah', $v))
            ->when(($filters['lingkup'] ?? '') === 'daerah', fn ($q) => $q->whereNull('perangkat_daerah'))
            ->when(($filters['lingkup'] ?? '') === 'opd', fn ($q) => $q->whereNotNull('perangkat_daerah'))
            ->orderBy('file')->orderBy('baris')->paginate(5)->withQueryString();
        $opds = RpjmdIndicatorSource::whereNotNull('perangkat_daerah')->distinct()->orderBy('perangkat_daerah')->pluck('perangkat_daerah');
        return view('rpjmd.index', compact('items', 'opds', 'filters'));
    }
}
