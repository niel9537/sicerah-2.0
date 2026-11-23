<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use App\Models\Program;
use App\Models\SubKegiatan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RealisasiKinerjaController extends Controller
{
    public function index(Request $request): View
    {
        $tahun     = (int) $request->input('tahun', now()->year);
        $triwulan  = (int) $request->input('triwulan', 4);
        $opdId     = $request->input('opd_id');
        $programId = $request->input('program_id');

        $subKegiatans = SubKegiatan::query()
            ->with([
                'kegiatan.program.opd',
                'targetTahunans' => fn ($q) => $q->where('tahun', $tahun),
                'realisasiTriwulanans' => fn ($q) => $q->where('tahun', $tahun)->where('triwulan', $triwulan),
            ])
            ->when($opdId, fn ($q) => $q->whereHas('kegiatan.program', fn ($p) => $p->where('opd_id', $opdId)))
            ->when($programId, fn ($q) => $q->whereHas('kegiatan', fn ($k) => $k->where('program_id', $programId)))
            ->orderBy('kode')
            ->get();

        $rekap = $subKegiatans->map(function (SubKegiatan $sub) {
            $target       = $sub->targetTahunans->first();
            $realisasi    = $sub->realisasiTriwulanans->first();
            $paguEfektif  = $target?->paguEfektif() ?? 0;
            $targetFisik  = (float) ($target?->target_kinerja ?? 0);
            $realFisik    = (float) ($realisasi?->realisasi_kinerja ?? 0);
            $realAnggaran = (float) ($realisasi?->realisasi_anggaran ?? 0);

            return (object) [
                'subkegiatan'        => $sub,
                'opd'                => $sub->kegiatan?->program?->opd,
                'target_fisik'       => $targetFisik,
                'realisasi_fisik'    => $realFisik,
                'pct_fisik'          => $targetFisik > 0 ? round($realFisik / $targetFisik * 100, 1) : 0,
                'pagu_efektif'       => $paguEfektif,
                'realisasi_anggaran' => $realAnggaran,
                'pct_keuangan'       => $paguEfektif > 0 ? round($realAnggaran / $paguEfektif * 100, 1) : 0,
                'status'             => $realisasi?->status,
                // Kapan baris ini (realisasi triwulan berjalan) terakhir disimpan.
                // realisasi_triwulanan sudah punya kolom created_at/updated_at bawaan,
                // hanya belum pernah ditampilkan di UI manapun sebelum ini.
                'updated_at'         => $realisasi?->updated_at,
            ];
        });

        $summary = [
            'total_pagu'      => $rekap->sum('pagu_efektif'),
            'total_realisasi' => $rekap->sum('realisasi_anggaran'),
            'rata_fisik'      => $rekap->count() ? round($rekap->avg('pct_fisik'), 1) : 0,
            'rata_keuangan'   => $rekap->count() ? round($rekap->avg('pct_keuangan'), 1) : 0,
            // Baris paling baru diperbarui di antara seluruh sub kegiatan yang tampil,
            // dipakai untuk banner "data diperbarui X yang lalu" di atas tabel.
            'terakhir_diperbarui' => $rekap->pluck('updated_at')->filter()->sort()->last(),
        ];

        $opdList     = Opd::orderBy('nama')->get();
        $programList = Program::orderBy('nama')->get();

        return view('realisasi.index', compact('rekap', 'summary', 'tahun', 'triwulan', 'opdId', 'programId', 'opdList', 'programList'));
    }
}
