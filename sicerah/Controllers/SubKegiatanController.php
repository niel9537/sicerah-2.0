<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubKegiatan\StoreSubKegiatanRequest;
use App\Http\Requests\SubKegiatan\UpdateSubKegiatanRequest;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Traits\HasFlashMessage;
use App\Traits\LogsAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubKegiatanController extends Controller
{
    use HasFlashMessage, LogsAudit;

    public function index(Request $request): View
    {
        $q         = trim((string) $request->input('q'));
        $kegiatanId= $request->input('kegiatan_id');

        $items = SubKegiatan::query()
            ->with('kegiatan.program')
            ->when($q, fn ($query) => $query->where('nama', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%"))
            ->when($kegiatanId, fn ($query) => $query->where('kegiatan_id', $kegiatanId))
            ->orderBy('kode')
            ->paginate(10)
            ->withQueryString();

        $kegiatanList = Kegiatan::orderBy('nama')->get();

        return view('master.subkegiatan.index', compact('items', 'q', 'kegiatanId', 'kegiatanList'));
    }

    public function store(StoreSubKegiatanRequest $request): RedirectResponse
    {
        $subKegiatan = SubKegiatan::create($request->validated());
        $this->logAudit('create', $subKegiatan);

        return $this->redirectSuccess('subkegiatan.index', 'Data Sub Kegiatan berhasil ditambahkan.');
    }

    public function update(UpdateSubKegiatanRequest $request, SubKegiatan $subkegiatan): RedirectResponse
    {
        $dataLama = $subkegiatan->getOriginal();
        $subkegiatan->update($request->validated());
        $this->logAudit('update', $subkegiatan, $dataLama);

        return $this->redirectSuccess('subkegiatan.index', 'Data Sub Kegiatan berhasil diperbarui.');
    }

    public function destroy(SubKegiatan $subkegiatan): RedirectResponse
    {
        if ($subkegiatan->targetTahunans()->exists() || $subkegiatan->realisasiTriwulanans()->exists()) {
            return $this->flashError('Sub Kegiatan tidak dapat dihapus karena sudah memiliki target/realisasi terkait.');
        }

        $dataLama = $subkegiatan->getOriginal();
        $subkegiatan->delete();
        $this->logAudit('delete', $subkegiatan, $dataLama);

        return $this->redirectSuccess('subkegiatan.index', 'Data Sub Kegiatan berhasil dihapus.');
    }
}