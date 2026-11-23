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
use App\Traits\ParsesAngkaFleksibel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IndikatorController extends Controller
{
    use HasFlashMessage, LogsAudit, ParsesAngkaFleksibel;

    public function index(Request $request): View
    {
        $jenis = strtoupper((string) $request->input('jenis', 'IKD'));
        if (! in_array($jenis, ['IKD', 'IKU', 'MAKRO', 'IKK'], true)) {
            $jenis = 'IKD';
        }
        $q     = trim((string) $request->input('q'));
        $opdId = $request->input('opd_id');
        $aspek = trim((string) $request->input('aspek'));

        $items = Indikator::query()
            ->with(['opd', 'urusan', 'sasaranRpjmd'])
            ->when($jenis === 'IKD', fn ($query) => $query->ikd())
            ->when($jenis === 'IKU', fn ($query) => $query->iku())
            ->when($jenis === 'MAKRO', fn ($query) => $query->makro())
            ->when($jenis === 'IKK', fn ($query) => $query->ikk())
            ->when($q, fn ($query) => $query->where('nama', 'like', "%{$q}%"))
            ->when($opdId, fn ($query) => $query->where('opd_id', $opdId))
            ->when($aspek, fn ($query) => $query->where('aspek', $aspek))
            ->orderByRaw('CASE WHEN urutan IS NULL THEN 1 ELSE 0 END')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        $opdList     = Opd::orderBy('nama')->get();
        $urusanList  = Urusan::orderBy('nama')->get();
        $sasaranList = SasaranRpjmd::orderBy('nama')->get();
        $aspekList = Indikator::bankData()->whereNotNull('aspek')->distinct()->orderBy('aspek')->pluck('aspek');

        return view('indikator.index', compact('items', 'jenis', 'q', 'opdId', 'aspek', 'opdList', 'urusanList', 'sasaranList', 'aspekList'));
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
        $tahunAkhir   = max(2030, (int) now()->year);
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

        // Susun struktur baris per tahun, sekaligus kumpulkan kapan tiap baris
        // (target + realisasi 4 triwulan tahun itu) terakhir disentuh, supaya
        // halaman ini bisa menjawab "data ini kapan terakhir diperbarui?".
        $baris = [];
        $semuaTimestamp = collect([$indikator->updated_at]);

        foreach ($rentangTahun as $tahun) {
            $realisasiPerTw = collect([1, 2, 3, 4])->mapWithKeys(function ($tw) use ($realisasis, $tahun) {
                $r = ($realisasis[$tahun] ?? collect())->firstWhere('triwulan', $tw);
                return [$tw => $r];
            });

            $targetTahun = $targets->get($tahun);

            $timestampBaris = collect([$targetTahun?->updated_at])
                ->merge($realisasiPerTw->map(fn ($r) => $r?->updated_at))
                ->filter();

            $semuaTimestamp = $semuaTimestamp->merge($timestampBaris);

            $baris[$tahun] = [
                'target'     => $targetTahun,
                'realisasi'  => $realisasiPerTw,
                'updated_at' => $timestampBaris->sort()->last(),
            ];
        }

        // Kapan data indikator ini (di baris manapun) terakhir diubah — dipakai
        // untuk badge ringkasan di bagian atas halaman.
        $terakhirDiperbarui = $semuaTimestamp->filter()->sort()->last();

        // Data grafik: rata-rata capaian tahunan (realisasi terakhir yang tercatat ÷ target × 100).
        // Target & realisasi disimpan sebagai teks bebas (kolomnya varchar), jadi isinya bisa saja
        // bukan angka ("N/A", "-", salah ketik, dst). Nilai seperti itu TIDAK dipaksa jadi 0 —
        // dibiarkan null supaya grafik menampilkan celah (titik dilewati), bukan angka palsu.
        // $adaDataGrafik   : apakah ada minimal satu titik numerik untuk ditampilkan sama sekali.
        // $adaNilaiDilewati: apakah ada nilai terisi tapi bukan angka, sehingga dilewati grafik
        //                    (dipakai untuk menampilkan catatan kecil ke pengguna).
        $grafikCapaian    = [];
        $grafikRealisasi  = [];
        $grafikTarget     = [];
        $adaDataGrafik    = false;
        $adaNilaiDilewati = false;

        foreach ($rentangTahun as $tahun) {
            $targetRaw = $baris[$tahun]['target']?->getRawOriginal('target');
            $targetVal = $this->angkaAtauNull($targetRaw);
            if (filled($targetRaw) && $targetVal === null) {
                $adaNilaiDilewati = true;
            }

            $realisasiRaw = null;
            foreach ([4, 3, 2, 1] as $tw) {
                $r = $baris[$tahun]['realisasi'][$tw] ?? null;
                $raw = $r?->getRawOriginal('realisasi');
                if ($raw !== null && trim((string) $raw) !== '') {
                    $realisasiRaw = $raw;
                    break;
                }
            }
            $realisasiVal = $this->angkaAtauNull($realisasiRaw);
            if (filled($realisasiRaw) && $realisasiVal === null) {
                $adaNilaiDilewati = true;
            }

            $grafikTarget[$tahun]    = $targetVal;
            $grafikRealisasi[$tahun] = $realisasiVal;
            $grafikCapaian[$tahun]   = ($targetVal !== null && $targetVal > 0 && $realisasiVal !== null)
                ? round($realisasiVal / $targetVal * 100, 2)
                : null;

            if ($targetVal !== null || $realisasiVal !== null) {
                $adaDataGrafik = true;
            }
        }

        return view('indikator.kelola', compact(
            'indikator', 'baris', 'rentangTahun',
            'grafikCapaian', 'grafikRealisasi', 'grafikTarget',
            'adaDataGrafik', 'adaNilaiDilewati',
            'terakhirDiperbarui'
        ));
    }

    public function simpanTarget(Request $request, Indikator $indikator, int $tahun): RedirectResponse
    {
        $data = $request->validate([
            'target' => 'nullable|string|max:100',
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
            'realisasi' => 'nullable|string|max:100',
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
