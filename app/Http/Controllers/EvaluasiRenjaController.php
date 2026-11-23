<?php

namespace App\Http\Controllers;

use App\Http\Requests\Evaluasi\SubmitEvaluasiRenjaRequest;
use App\Http\Requests\Evaluasi\UpdateEvaluasiRenjaRequest;
use App\Models\EvaluasiRenja;
use App\Models\Periode;
use App\Models\SubKegiatan;
use App\Models\TargetTahunan;
use App\Services\EvaluasiRenjaService;
use App\Traits\HasFlashMessage;
use App\Traits\LogsAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluasiRenjaController extends Controller
{
    use HasFlashMessage, LogsAudit;

    public function __construct(private readonly EvaluasiRenjaService $evaluasiService)
    {
    }

    /**
     * Daftar sekarang dikelompokkan per Sub Kegiatan + Tahun (bukan per baris triwulan lagi).
     */
    public function index(Request $request): View
    {
        $user  = auth()->user();
        $tahun = (int) $request->input('tahun', now()->year);

        $subKegiatanList = SubKegiatan::query()
            ->with([
                'kegiatan.program.opd',
                'evaluasiRenjas' => fn ($q) => $q->whereHas('periode', fn ($p) => $p->where('tahun', $tahun))->with('periode'),
            ])
            ->when($user->role === 'operator_opd', fn ($q) => $q->forOpd($user->opd_id))
            ->orderBy('kode')
            ->paginate(15)
            ->withQueryString();

        return view('evaluasi.index', compact('subKegiatanList', 'tahun'));
    }

    /**
     * Halaman pemilihan sebelum masuk ke kelola.
     */
    public function pilih(): View
    {
        $user = auth()->user();

        if ($user->role === 'operator_opd' && ! $user->opd_id) {
            abort(403, 'Akun Anda belum dikaitkan dengan OPD. Hubungi Administrator.');
        }

        $subKegiatanList = SubKegiatan::query()
            ->with('kegiatan.program.opd')
            ->when($user->role === 'operator_opd', fn ($q) => $q->forOpd($user->opd_id))
            ->orderBy('nama')
            ->get();

        return view('evaluasi.pilih', compact('subKegiatanList'));
    }

    /**
     * Halaman utama: satu Sub Kegiatan, satu Tahun, 4 tab Triwulan sekaligus.
     */
    public function kelola(SubKegiatan $subkegiatan, int $tahun): View
    {
        $this->assertSubKegiatanAllowed($subkegiatan->id);

        $target = TargetTahunan::where('subkegiatan_id', $subkegiatan->id)
            ->where('tahun', $tahun)
            ->first();

        $periodes = Periode::where('tahun', $tahun)->orderBy('triwulan')->get()->keyBy('triwulan');

        $evaluasis = EvaluasiRenja::where('subkegiatan_id', $subkegiatan->id)
            ->whereIn('periode_id', $periodes->pluck('id'))
            ->with('periode')
            ->get()
            ->keyBy(fn ($e) => $e->periode->triwulan ?? null);

        $slots = [];
        foreach ([1, 2, 3, 4] as $tw) {
            $slots[$tw] = [
                'periode'  => $periodes->get($tw),
                'evaluasi' => $evaluasis->get($tw),
            ];
        }

        // Tab default: triwulan pertama yang masih bisa diisi/diperbaiki
        $activeTriwulan = 1;
        foreach ($slots as $tw => $slot) {
            $belumAda = ! $slot['evaluasi'];
            $bisaEdit = $slot['evaluasi'] && in_array($slot['evaluasi']->status, [EvaluasiRenja::STATUS_DRAFT, EvaluasiRenja::STATUS_DITOLAK], true);
            if ($belumAda || $bisaEdit) {
                $activeTriwulan = $tw;
                break;
            }
        }

        return view('evaluasi.kelola', compact('subkegiatan', 'tahun', 'target', 'slots', 'activeTriwulan'));
    }

    /**
     * Simpan (buat/perbarui) draft untuk satu triwulan tertentu.
     */
    public function simpanTriwulan(UpdateEvaluasiRenjaRequest $request, SubKegiatan $subkegiatan, int $tahun, int $triwulan): RedirectResponse
    {
        $this->assertSubKegiatanAllowed($subkegiatan->id);

        $periode = Periode::where('tahun', $tahun)->where('triwulan', $triwulan)->first();
        abort_unless($periode, 404, 'Periode belum tersedia. Hubungi Admin Bappeda untuk membuatnya di menu Periode.');

        $evaluasi = EvaluasiRenja::where('subkegiatan_id', $subkegiatan->id)
            ->where('periode_id', $periode->id)
            ->first();

        if ($evaluasi) {
            $this->assertOwnedByUser($evaluasi);
            $this->assertEditable($evaluasi);

            $dataLama = $evaluasi->getOriginal();
            $evaluasi->update($request->validated());

            if ($evaluasi->status === EvaluasiRenja::STATUS_DITOLAK) {
                $evaluasi->update(['status' => EvaluasiRenja::STATUS_DRAFT, 'catatan_verifikasi' => null]);
            }

            $this->logAudit('update', $evaluasi, $dataLama);
        } else {
            $target = TargetTahunan::where('subkegiatan_id', $subkegiatan->id)->where('tahun', $tahun)->first();

            if (! $target) {
                return $this->flashError('Target tahunan untuk sub kegiatan dan tahun ' . $tahun . ' belum diatur. Hubungi Admin Bappeda terlebih dahulu.');
            }

            $evaluasi = EvaluasiRenja::create([
                ...$request->validated(),
                'subkegiatan_id'  => $subkegiatan->id,
                'periode_id'      => $periode->id,
                'target_fisik'    => $target->target_kinerja,
                'target_keuangan' => $target->paguEfektif(),
                'status'          => EvaluasiRenja::STATUS_DRAFT,
                'created_by'      => auth()->id(),
            ]);

            $this->logAudit('create', $evaluasi);
        }

        $this->evaluasiService->syncRealisasiTriwulanan($evaluasi);

        return $this->redirectSuccess(
            'evaluasi.kelola',
            'Draft Triwulan ' . $this->romawi($triwulan) . ' berhasil disimpan.',
            ['subkegiatan' => $subkegiatan->id, 'tahun' => $tahun]
        );
    }

    /**
     * Ajukan satu triwulan tertentu untuk verifikasi.
     */
    public function ajukanTriwulan(SubmitEvaluasiRenjaRequest $request, SubKegiatan $subkegiatan, int $tahun, int $triwulan): RedirectResponse
    {
        $this->assertSubKegiatanAllowed($subkegiatan->id);

        $periode  = Periode::where('tahun', $tahun)->where('triwulan', $triwulan)->firstOrFail();
        $evaluasi = EvaluasiRenja::where('subkegiatan_id', $subkegiatan->id)->where('periode_id', $periode->id)->first();

        abort_unless($evaluasi, 404, 'Simpan draft terlebih dahulu sebelum mengajukan.');

        $this->assertOwnedByUser($evaluasi);
        $this->assertEditable($evaluasi);

        $dataLama = $evaluasi->getOriginal();
        $evaluasi->update([...$request->validated(), 'status' => EvaluasiRenja::STATUS_DIAJUKAN]);
        $this->evaluasiService->syncRealisasiTriwulanan($evaluasi);
        $this->logAudit('update', $evaluasi, $dataLama);

        return $this->redirectSuccess(
            'evaluasi.kelola',
            'Triwulan ' . $this->romawi($triwulan) . ' berhasil diajukan untuk verifikasi.',
            ['subkegiatan' => $subkegiatan->id, 'tahun' => $tahun]
        );
    }

    public function hapusTriwulan(SubKegiatan $subkegiatan, int $tahun, int $triwulan): RedirectResponse
    {
        $this->assertSubKegiatanAllowed($subkegiatan->id);

        $periode  = Periode::where('tahun', $tahun)->where('triwulan', $triwulan)->first();
        $evaluasi = $periode ? EvaluasiRenja::where('subkegiatan_id', $subkegiatan->id)->where('periode_id', $periode->id)->first() : null;

        if (! $evaluasi) {
            return $this->flashError('Tidak ada draft untuk dihapus.');
        }

        $this->assertOwnedByUser($evaluasi);

        if ($evaluasi->status !== EvaluasiRenja::STATUS_DRAFT) {
            return $this->flashError('Hanya draft yang dapat dihapus.');
        }

        $dataLama = $evaluasi->getOriginal();
        $evaluasi->delete();
        $this->logAudit('delete', $evaluasi, $dataLama);

        return $this->redirectSuccess(
            'evaluasi.kelola',
            'Draft Triwulan ' . $this->romawi($triwulan) . ' berhasil dihapus.',
            ['subkegiatan' => $subkegiatan->id, 'tahun' => $tahun]
        );
    }

    /* ---------------- Guard helpers ---------------- */

    private function assertSubKegiatanAllowed(int $subKegiatanId): void
    {
        $user = auth()->user();
        if ($user->role !== 'operator_opd') {
            return;
        }

        $allowed = SubKegiatan::forOpd($user->opd_id)->whereKey($subKegiatanId)->exists();
        abort_unless($allowed, 403, 'Sub kegiatan tersebut bukan milik OPD Anda.');
    }

    private function assertOwnedByUser(EvaluasiRenja $evaluasi): void
    {
        $user = auth()->user();
        if ($user->role !== 'operator_opd') {
            return;
        }

        $evaluasi->loadMissing('subKegiatan.kegiatan.program');
        abort_unless(
            $evaluasi->subKegiatan?->kegiatan?->program?->opd_id === $user->opd_id,
            403,
            'Anda tidak memiliki akses ke evaluasi ini.'
        );
    }

    private function assertEditable(EvaluasiRenja $evaluasi): void
    {
        abort_unless(
            in_array($evaluasi->status, [EvaluasiRenja::STATUS_DRAFT, EvaluasiRenja::STATUS_DITOLAK], true),
            403,
            'Evaluasi yang sudah diajukan/diverifikasi tidak dapat diubah.'
        );
    }

    private function romawi(int $tw): string
    {
        return ['I', 'II', 'III', 'IV'][$tw - 1] ?? '-';
    }
}