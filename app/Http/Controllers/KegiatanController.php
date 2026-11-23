<?php

namespace App\Http\Controllers;

use App\Http\Requests\Kegiatan\StoreKegiatanRequest;
use App\Http\Requests\Kegiatan\UpdateKegiatanRequest;
use App\Models\Kegiatan;
use App\Models\Program;
use App\Traits\HasFlashMessage;
use App\Traits\LogsAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KegiatanController extends Controller
{
    use HasFlashMessage, LogsAudit;

    public function index(Request $request): View
    {
        $q        = trim((string) $request->input('q'));
        $programId= $request->input('program_id');

        $items = Kegiatan::query()
            ->with('program.opd')
            ->withCount('subKegiatans')
            ->when($q, fn ($query) => $query->where('nama', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%"))
            ->when($programId, fn ($query) => $query->where('program_id', $programId))
            ->orderBy('kode')
            ->paginate(10)
            ->withQueryString();

        $programList = Program::orderBy('nama')->get();

        return view('master.kegiatan.index', compact('items', 'q', 'programId', 'programList'));
    }

    public function store(StoreKegiatanRequest $request): RedirectResponse
    {
        $kegiatan = Kegiatan::create($request->validated());
        $this->logAudit('create', $kegiatan);

        return $this->redirectSuccess('kegiatan.index', 'Data Kegiatan berhasil ditambahkan.');
    }

    public function update(UpdateKegiatanRequest $request, Kegiatan $kegiatan): RedirectResponse
    {
        $dataLama = $kegiatan->getOriginal();
        $kegiatan->update($request->validated());
        $this->logAudit('update', $kegiatan, $dataLama);

        return $this->redirectSuccess('kegiatan.index', 'Data Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan): RedirectResponse
    {
        if ($kegiatan->subKegiatans()->exists()) {
            return $this->flashError('Kegiatan tidak dapat dihapus karena masih memiliki sub kegiatan terkait.');
        }

        $dataLama = $kegiatan->getOriginal();
        $kegiatan->delete();
        $this->logAudit('delete', $kegiatan, $dataLama);

        return $this->redirectSuccess('kegiatan.index', 'Data Kegiatan berhasil dihapus.');
    }
}