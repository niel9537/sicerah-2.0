<?php

namespace App\Services;

use App\Models\EvaluasiRenja;
use App\Models\RealisasiTriwulanan;

class EvaluasiRenjaService
{
    public function syncRealisasiTriwulanan(EvaluasiRenja $evaluasi): RealisasiTriwulanan
    {
        $evaluasi->loadMissing('periode');

        return RealisasiTriwulanan::updateOrCreate(
            [
                'subkegiatan_id' => $evaluasi->subkegiatan_id,
                'tahun'          => $evaluasi->periode->tahun,
                'triwulan'       => $evaluasi->periode->triwulan,
            ],
            [
                'realisasi_kinerja'  => $evaluasi->realisasi_fisik,
                'realisasi_anggaran' => $evaluasi->realisasi_keuangan,
                'status'             => $evaluasi->status,
                'catatan_verifikasi' => $evaluasi->catatan_verifikasi,
                'input_by'           => $evaluasi->created_by,
                'verified_by'        => $evaluasi->verified_by,
                'verified_at'        => $evaluasi->verified_at,
            ]
        );
    }
}