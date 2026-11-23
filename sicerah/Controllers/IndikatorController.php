<?php

namespace App\Http\Controllers;

use App\Http\Requests\Indikator\StoreIndikatorRequest;
use App\Http\Requests\Indikator\UpdateIndikatorRequest;
use App\Models\Indikator;
use App\Models\IndikatorRealisasi;
use App\Models\IndikatorTarget;
use App\Models\Opd;
use App\Models\SasaranRpjmd;
use App\Models\Urusan;
use App\Traits\HasFlashMessage;
use App\Traits\LogsAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IndikatorController extends Controller
{
    use HasFlashMessage, LogsAudit;

    public function index(Request $request): View
    {
        $jenis = $request->input('jenis', 'IKU');
        $q     = trim((string) $request->input('q'));
        $opdId = $request->input('opd_id');

        $items = Indikator::query()
            ->with(['opd', 'urusan', 'sasaranRpjmd'])
            ->where('jenis', $jenis)
            ->when($q, fn ($query) => $query->where('nama', 'like', "%{$q}%"))
            ->when($opdId, fn ($query) => $query->where('opd_id', $opdId))
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        $opdList     = Opd::orderBy('nama')->get();
        $urusanList  = Urusan::orderBy('nama')->get();
        $sasaranList = SasaranRpjmd::orderBy('nama')->get();

        return view('indikator.index', compact('items', 'jenis', 'q', 'opdId', 'opdList', 'urusanList', 'sasaranList'));
    }

    public function store(StoreIndikatorRequest $request): RedirectResponse
    {
        $indikator = Indikator::create($request->validated());
        $this->logAudit('create', $indikator);

        return $this->redirectSuccess('indikator.index', 'Indikator berhasil ditambahkan.', ['jenis' => $indikator->jenis]);
    }

    public function update(UpdateIndikatorRequest $request, Indikator $indikator): RedirectResponse
    {
        $dataLama = $indikator->getOriginal();
        $indikator->update($request->validated());
        $this->logAudit('update', $indikator, $dataLama);

        return $this->redirectSuccess('indikator.index', 'Indikator berhasil diperbarui.', ['jenis' => $indikator->jenis]);
    }

    public function destroy(Indikator $indikator): RedirectResponse
    {
        if ($indikator->targets()->exists() || $indikator->realisasis()->exists()) {
            return $this->flashError('Indikator tidak dapat dihapus karena sudah memiliki target/realisasi.');
        }

        $jenis    = $indikator->jenis;
        $dataLama = $indikator->getOriginal();
        $indikator->delete();
        $this->logAudit('delete', $indikator, $dataLama);

        return $this->redirectSuccess('indikator.index', 'Indikator berhasil dihapus.', ['jenis' => $jenis]);
    }

    /**
     * Halaman kelola capaian: tabel input target & realisasi triwulanan
     * untuk rentang 2020 s/d tahun berjalan, plus grafik tren capaian tahunan.
     */
    public function kelola(Indikator $indikator): View
    {
        $tahunMulai   = 2020;
        $tahunAkhir   = (int) now()->year;
        $rentangTahun = range($tahunMulai, $tahunAkhir);

        $indikator->load('opd');

        // Ambil semua target & realisasi dalam rentang, keyBy tahun/triwulan supaya mudah di-lookup
        $targets = IndikatorTarget::where('indikator_id', $indikator->id)
            ->whereBetween('tahun', [$tahunMulai, $tahunAkhir])
            ->get()
            ->keyBy('tahun');

        $realisasis = IndikatorRealisasi::where('indikator_id', $indikator->id)
            ->whereBetween('tahun', [$tahunMulai, $tahunAkhir])
            ->get()
            ->groupBy('tahun');

        // Susun struktur baris per tahun
        $baris = [];
        foreach ($rentangTahun as $tahun) {
            $realisasiPerTw = collect([1, 2, 3, 4])->mapWithKeys(function ($tw) use ($realisasis, $tahun) {
                $r = ($realisasis[$tahun] ?? collect())->firstWhere('triwulan', $tw);
                return [$tw => $r];
            });

            $baris[$tahun] = [
                'target'    => $targets->get($tahun),
                'realisasi' => $realisasiPerTw,
            ];
        }

        // Data grafik: rata-rata capaian tahunan (realisasi TW IV kumulatif ÷ target × 100)
        // Kalau TW IV belum ada, ambil TW terakhir yang tersedia. Kalau tak ada sama sekali → 0.
        $grafikCapaian = [];
        foreach ($rentangTahun as $tahun) {
            $target = optional($baris[$tahun]['target'])->target;
            $realisasiTerakhir = null;

            foreach ([4, 3, 2, 1] as $tw) {
                $r = $baris[$tahun]['realisasi'][$tw] ?? null;
                if ($r && $r->realisasi !== null) {
                    $realisasiTerakhir = (float) $r->realisasi;
                    break;
                }
            }

            $grafikCapaian[$tahun] = ($target && (float) $target > 0 && $realisasiTerakhir !== null)
                ? round($realisasiTerakhir / (float) $target * 100, 2)
                : 0;
        }

        $grafikRealisasi = [];
        foreach ($rentangTahun as $tahun) {
            $realisasiTerakhir = null;
            foreach ([4, 3, 2, 1] as $tw) {
                $r = $baris[$tahun]['realisasi'][$tw] ?? null;
                if ($r && $r->realisasi !== null) {
                    $realisasiTerakhir = (float) $r->realisasi;
                    break;
                }
            }
            $grafikRealisasi[$tahun] = $realisasiTerakhir ?? 0;
        }

        $grafikTarget = [];
        foreach ($rentangTahun as $tahun) {
            $grafikTarget[$tahun] = optional($baris[$tahun]['target'])->target
                ? (float) $baris[$tahun]['target']->target
                : 0;
        }

        return view('indikator.kelola', compact(
            'indikator', 'baris', 'rentangTahun',
            'grafikCapaian', 'grafikRealisasi', 'grafikTarget'
        ));
    }

    public function simpanTarget(Request $request, Indikator $indikator, int $tahun): RedirectResponse
    {
        $data = $request->validate([
            'target' => 'nullable|numeric|min:0',
        ]);

        // Kalau target dikosongkan → hapus target tahun tsb
        if ($data['target'] === null || $data['target'] === '') {
            IndikatorTarget::where('indikator_id', $indikator->id)->where('tahun', $tahun)->delete();
        } else {
            IndikatorTarget::updateOrCreate(
                ['indikator_id' => $indikator->id, 'tahun' => $tahun],
                ['target' => $data['target']]
            );
        }

        return $this->redirectSuccess(
            'indikator.kelola',
            "Target tahun {$tahun} berhasil disimpan.",
            ['indikator' => $indikator->id]
        );
    }

    public function simpanRealisasi(Request $request, Indikator $indikator, int $tahun, int $triwulan): RedirectResponse
    {
        $data = $request->validate([
            'realisasi' => 'nullable|numeric|min:0',
            'status'    => 'nullable|in:draft,diajukan,diverifikasi,ditolak',
        ]);

        // Kalau realisasi dikosongkan → hapus record
        if ($data['realisasi'] === null || $data['realisasi'] === '') {
            IndikatorRealisasi::where('indikator_id', $indikator->id)
                ->where('tahun', $tahun)
                ->where('triwulan', $triwulan)
                ->delete();
        } else {
            IndikatorRealisasi::updateOrCreate(
                ['indikator_id' => $indikator->id, 'tahun' => $tahun, 'triwulan' => $triwulan],
                [
                    'realisasi' => $data['realisasi'],
                    'status'    => $data['status'] ?? 'draft',
                ]
            );
        }

        return $this->redirectSuccess(
            'indikator.kelola',
            'Realisasi Triwulan ' . ['I', 'II', 'III', 'IV'][$triwulan - 1] . " tahun {$tahun} berhasil disimpan.",
            ['indikator' => $indikator->id]
        );
    }
}