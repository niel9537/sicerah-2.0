<?php

namespace App\Http\Controllers;

use App\Http\Requests\Evaluasi\RejectEvaluasiRenjaRequest;
use App\Models\EvaluasiRenja;
use App\Models\Opd;
use App\Models\Periode;
use App\Models\TargetTahunan;
use App\Services\EvaluasiRenjaService;
use App\Traits\HasFlashMessage;
use App\Traits\LogsAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerifikasiController extends Controller
{
    use HasFlashMessage, LogsAudit;

    public function __construct(private readonly EvaluasiRenjaService $evaluasiService)
    {
    }

    public function index(Request $request): View
    {
        $periodeId = $request->input('periode_id');
        $opdId     = $request->input('opd_id');
        $status    = $request->input('status', EvaluasiRenja::STATUS_DIAJUKAN);

        $items = EvaluasiRenja::query()
            ->with(['subKegiatan.kegiatan.program.opd', 'periode', 'creator'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($periodeId, fn ($q) => $q->where('periode_id', $periodeId))
            ->when($opdId, fn ($q) => $q->whereHas(
                'subKegiatan.kegiatan.program',
                fn ($p) => $p->where('opd_id', $opdId)
            ))
            ->orderBy('updated_at')
            ->paginate(10)
            ->withQueryString();

        $periodeList = Periode::orderByDesc('tahun')->orderByDesc('triwulan')->get();
        $opdList     = Opd::orderBy('nama')->get();

        return view('verifikasi.index', compact('items', 'periodeList', 'opdList', 'periodeId', 'opdId', 'status'));
    }

    public function show(EvaluasiRenja $evaluasi): View
    {
        $evaluasi->load(['subKegiatan.kegiatan.program.opd', 'periode', 'creator', 'verifier']);

        $target = TargetTahunan::where('subkegiatan_id', $evaluasi->subkegiatan_id)
            ->where('tahun', $evaluasi->periode->tahun)
            ->first();

        return view('verifikasi.show', compact('evaluasi', 'target'));
    }

    public function approve(EvaluasiRenja $evaluasi): RedirectResponse
    {
        abort_unless($evaluasi->status === EvaluasiRenja::STATUS_DIAJUKAN, 403, 'Hanya evaluasi berstatus Diajukan yang dapat diverifikasi.');

        $dataLama = $evaluasi->getOriginal();
        $evaluasi->update([
            'status'             => EvaluasiRenja::STATUS_DIVERIFIKASI,
            'verified_by'        => auth()->id(),
            'verified_at'        => now(),
            'catatan_verifikasi' => null,
        ]);

        $this->evaluasiService->syncRealisasiTriwulanan($evaluasi);
        $this->logAudit('update', $evaluasi, $dataLama);

        return $this->redirectSuccess('verifikasi.index', 'Evaluasi berhasil diverifikasi/disetujui.');
    }

    public function reject(RejectEvaluasiRenjaRequest $request, EvaluasiRenja $evaluasi): RedirectResponse
    {
        abort_unless($evaluasi->status === EvaluasiRenja::STATUS_DIAJUKAN, 403, 'Hanya evaluasi berstatus Diajukan yang dapat ditolak.');

        $dataLama = $evaluasi->getOriginal();
        $evaluasi->update([
            'status'             => EvaluasiRenja::STATUS_DITOLAK,
            'verified_by'        => auth()->id(),
            'verified_at'        => now(),
            'catatan_verifikasi' => $request->catatan_verifikasi,
        ]);

        $this->evaluasiService->syncRealisasiTriwulanan($evaluasi);
        $this->logAudit('update', $evaluasi, $dataLama);

        return $this->redirectSuccess('verifikasi.index', 'Evaluasi dikembalikan ke OPD dengan catatan perbaikan.');
    }
}