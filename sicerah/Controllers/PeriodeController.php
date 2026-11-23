<?php

namespace App\Http\Controllers;

use App\Http\Requests\Periode\StorePeriodeRequest;
use App\Http\Requests\Periode\UpdatePeriodeRequest;
use App\Models\Periode;
use App\Traits\HasFlashMessage;
use App\Traits\LogsAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeriodeController extends Controller
{
    use HasFlashMessage, LogsAudit;

    public function index(Request $request): View
    {
        $items = Periode::query()
            ->orderByDesc('tahun')
            ->orderBy('triwulan')
            ->paginate(12);

        return view('master.periode.index', compact('items'));
    }

    public function store(StorePeriodeRequest $request): RedirectResponse
    {
        $periode = Periode::create($request->validated());
        $this->logAudit('create', $periode);

        return $this->redirectSuccess('periode.index', 'Data Periode berhasil ditambahkan.');
    }

    public function update(UpdatePeriodeRequest $request, Periode $periode): RedirectResponse
    {
        $dataLama = $periode->getOriginal();
        $periode->update($request->validated());
        $this->logAudit('update', $periode, $dataLama);

        return $this->redirectSuccess('periode.index', 'Data Periode berhasil diperbarui.');
    }

    public function destroy(Periode $periode): RedirectResponse
    {
        if ($periode->evaluasiRenjas()->exists()) {
            return $this->flashError('Periode tidak dapat dihapus karena sudah memiliki data evaluasi terkait.');
        }

        $dataLama = $periode->getOriginal();
        $periode->delete();
        $this->logAudit('delete', $periode, $dataLama);

        return $this->redirectSuccess('periode.index', 'Data Periode berhasil dihapus.');
    }

    /** Toggle status aktif/tutup cepat */
    public function toggleStatus(Periode $periode): RedirectResponse
    {
        $dataLama = $periode->getOriginal();
        $periode->update(['status' => $periode->status === 'aktif' ? 'tutup' : 'aktif']);
        $this->logAudit('update', $periode, $dataLama);

        return $this->redirectSuccess('periode.index', 'Status periode berhasil diubah.');
    }
}