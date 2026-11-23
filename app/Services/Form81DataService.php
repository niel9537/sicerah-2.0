<?php

namespace App\Services;

use App\Models\Opd;
use App\Models\Program;
use App\Models\Urusan;
use Illuminate\Support\Collection;

class Form81DataService
{
    /**
     * Susun data hierarkis untuk Form 81: Urusan → Program → Kegiatan → Sub Kegiatan.
     *
     * @return array{ header: array, rows: array, footer: array }
     */
    public function build(int $tahun, ?int $opdId = null): array
    {
        $urusans = Urusan::query()
            ->with([
                'programs' => function ($q) use ($opdId, $tahun) {
                    $q->when($opdId, fn ($p) => $p->where('opd_id', $opdId))
                      ->with([
                          'opd',
                          'kegiatans.subKegiatans.targetTahunans' => fn ($t) => $t->where('tahun', $tahun),
                          'kegiatans.subKegiatans.realisasiTriwulanans' => fn ($r) => $r->where('tahun', $tahun),
                      ]);
                },
            ])
            ->when($opdId, fn ($q) => $q->whereHas('programs', fn ($p) => $p->where('opd_id', $opdId)))
            ->orderBy('kode')
            ->get();

        $rows = [];
        $no   = 0;

        foreach ($urusans as $urusan) {
            if ($urusan->programs->isEmpty()) {
                continue;
            }

            $rows[] = $this->makeRow('urusan', $urusan, ++$no);

            foreach ($urusan->programs as $program) {
                $rows[] = $this->makeRow('program', $program, ++$no);

                foreach ($program->kegiatans as $kegiatan) {
                    $rows[] = $this->makeRow('kegiatan', $kegiatan, ++$no);

                    foreach ($kegiatan->subKegiatans as $sub) {
                        $rows[] = $this->makeRow('subkegiatan', $sub, ++$no, $tahun, $program->opd);
                    }
                }
            }
        }

        $footer = $this->buildFooter($rows);

        return [
            'header' => [
                'tahun' => $tahun,
                'opd'   => $opdId ? Opd::find($opdId)?->nama : 'SELURUH SKPD',
            ],
            'rows'   => $rows,
            'footer' => $footer,
        ];
    }

    private function makeRow(string $level, mixed $model, int $no, ?int $tahun = null, ?Opd $opd = null): array
    {
        $base = [
            'no'        => $no,
            'level'     => $level, // urusan | program | kegiatan | subkegiatan
            'kode'      => $model->kode ?? '',
            'uraian'    => $model->nama ?? '',
            'indikator' => '',
            'satuan'    => '',
            'target_k'  => null,
            'target_rp' => null,
            'tw_1_k'    => null, 'tw_1_rp' => null,
            'tw_2_k'    => null, 'tw_2_rp' => null,
            'tw_3_k'    => null, 'tw_3_rp' => null,
            'tw_4_k'    => null, 'tw_4_rp' => null,
            'real_k'    => null,
            'real_rp'   => null,
            'pct_k'     => null,
            'pct_rp'    => null,
            'opd'       => '',
            'ket'       => '',
        ];

        if ($level !== 'subkegiatan') {
            return $base;
        }

        $target = $model->targetTahunans->first();
        $rlst   = $model->realisasiTriwulanans;

        $base['indikator'] = $model->indikator ?? '';
        $base['satuan']    = $model->satuan ?? '';
        $base['target_k']  = $target?->target_kinerja;
        $base['target_rp'] = $target?->paguEfektif();

        foreach ([1, 2, 3, 4] as $tw) {
            $r = $rlst->firstWhere('triwulan', $tw);
            $base["tw_{$tw}_k"]  = $r?->realisasi_kinerja;
            $base["tw_{$tw}_rp"] = $r?->realisasi_anggaran;
        }

        // Realisasi kumulatif = triwulan terakhir yang tersedia
        $rTerakhir = null;
        foreach ([4, 3, 2, 1] as $tw) {
            if ($rlst->firstWhere('triwulan', $tw)) {
                $rTerakhir = $rlst->firstWhere('triwulan', $tw);
                break;
            }
        }
        $base['real_k']  = $rTerakhir?->realisasi_kinerja;
        $base['real_rp'] = $rTerakhir?->realisasi_anggaran;

        // Persentase capaian
        $base['pct_k']   = ($base['target_k'] > 0 && $base['real_k'] !== null)
            ? round((float) $base['real_k'] / (float) $base['target_k'] * 100, 2) : null;
        $base['pct_rp']  = ($base['target_rp'] > 0 && $base['real_rp'] !== null)
            ? round((float) $base['real_rp'] / (float) $base['target_rp'] * 100, 2) : null;

        $base['opd'] = $opd?->nama ?? '';

        return $base;
    }

    private function buildFooter(array $rows): array
    {
        $subs = collect($rows)->where('level', 'subkegiatan');

        return [
            'total_target_rp' => $subs->sum('target_rp'),
            'total_real_rp'   => $subs->sum('real_rp'),
            'jumlah_sub'      => $subs->count(),
            'rata_pct_k'      => $subs->whereNotNull('pct_k')->avg('pct_k'),
            'rata_pct_rp'     => $subs->whereNotNull('pct_rp')->avg('pct_rp'),
        ];
    }
}