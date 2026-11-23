<?php

namespace App\Http\Controllers;

use App\Models\JdihDokumen;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Halaman publik JDIH (Jaringan Dokumentasi dan Informasi Hukum) — daftar
 * lengkap dokumen perencanaan & regulasi Bappelitbangda, bisa difilter per
 * kategori/tahun. Tidak butuh login, sama seperti landing page.
 */
class JdihController extends Controller
{
    public function index(Request $request): View
    {
        $kategori = $request->input('kategori');
        $tahun = $request->input('tahun');
        $q = trim((string) $request->input('q'));

        $items = JdihDokumen::query()
            ->published()
            ->kategori($kategori)
            ->when($tahun, fn ($query) => $query->where('tahun', $tahun))
            ->when($q, fn ($query) => $query->where('judul', 'like', "%{$q}%"))
            ->orderByDesc('tahun')
            ->orderBy('kategori')
            ->orderBy('judul')
            ->paginate(12)
            ->withQueryString();

        $tahunList = JdihDokumen::published()->whereNotNull('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');
        $kategoriList = JdihDokumen::KATEGORI_LABEL;

        return view('jdih.index', compact('items', 'kategori', 'tahun', 'q', 'tahunList', 'kategoriList'));
    }
}
