<?php

namespace App\Http\Controllers;

use App\Http\Requests\TargetTahunan\StoreTargetTahunanRequest;
use App\Http\Requests\TargetTahunan\UpdateTargetTahunanRequest;
use App\Models\SubKegiatan;
use App\Models\TargetTahunan;
use App\Traits\HasFlashMessage;
use App\Traits\LogsAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TargetTahunanController extends Controller
{
    use HasFlashMessage, LogsAudit;

    public function index(Request $request): View
    {
        $tahun = (int) $request->input('tahun', now()->year);
        $q     = trim((string) $request->input('q'));

        $items = TargetTahunan::query()
            ->with('subKegiatan.kegiatan.program.opd')
            ->where('tahun', $tahun)
            ->when($q, fn ($query) => $query->whereHas(
                'subKegiatan',
                fn ($s) => $s->where('nama', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%")
            ))
            ->orderBy('subkegiatan_id')
            ->paginate(15)
            ->withQueryString();

        $subKegiatanList = SubKegiatan::orderBy('nama')->get();

        return view('target-tahunan.index', compact('items', 'tahun', 'q', 'subKegiatanList'));
    }

    public function store(StoreTargetTahunanRequest $request): RedirectResponse
    {
        $target = TargetTahunan::create($request->validated());
        $this->logAudit('create', $target);

        return $this->redirectSuccess('target-tahunan.index', 'Target tahunan berhasil ditambahkan.', ['tahun' => $target->tahun]);
    }

    public function update(UpdateTargetTahunanRequest $request, TargetTahunan $target_tahunan): RedirectResponse
    {
        $dataLama = $target_tahunan->getOriginal();
        $target_tahunan->update($request->validated());
        $this->logAudit('update', $target_tahunan, $dataLama);

        return $this->redirectSuccess('target-tahunan.index', 'Target tahunan berhasil diperbarui.', ['tahun' => $target_tahunan->tahun]);
    }

    public function destroy(TargetTahunan $target_tahunan): RedirectResponse
    {
        $adaRealisasi = $target_tahunan->subKegiatan
            ?->realisasiTriwulanans()
            ->where('tahun', $target_tahunan->tahun)
            ->exists();

        if ($adaRealisasi) {
            return $this->flashError('Target tidak dapat dihapus karena sub kegiatan ini sudah memiliki realisasi pada tahun tersebut.');
        }

        $dataLama = $target_tahunan->getOriginal();
        $tahun    = $target_tahunan->tahun;
        $target_tahunan->delete();
        $this->logAudit('delete', $target_tahunan, $dataLama);

        return $this->redirectSuccess('target-tahunan.index', 'Target tahunan berhasil dihapus.', ['tahun' => $tahun]);
    }
}