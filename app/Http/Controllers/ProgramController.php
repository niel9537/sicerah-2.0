<?php

namespace App\Http\Controllers;

use App\Http\Requests\Program\StoreProgramRequest;
use App\Http\Requests\Program\UpdateProgramRequest;
use App\Models\Opd;
use App\Models\Program;
use App\Models\SasaranPd;
use App\Models\Urusan;
use App\Traits\HasFlashMessage;
use App\Traits\LogsAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramController extends Controller
{
    use HasFlashMessage, LogsAudit;

    public function index(Request $request): View
    {
        $q       = trim((string) $request->input('q'));
        $opdId   = $request->input('opd_id');
        $urusanId= $request->input('urusan_id');

        $items = Program::query()
            ->with(['urusan', 'opd', 'sasaranPd'])
            ->withCount('kegiatans')
            ->when($q, fn ($query) => $query->where('nama', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%"))
            ->when($opdId, fn ($query) => $query->where('opd_id', $opdId))
            ->when($urusanId, fn ($query) => $query->where('urusan_id', $urusanId))
            ->orderBy('kode')
            ->paginate(10)
            ->withQueryString();

        $opdList    = Opd::orderBy('nama')->get();
        $urusanList = Urusan::orderBy('nama')->get();
        $sasaranList= SasaranPd::orderBy('nama')->get();

        return view('master.program.index', compact('items', 'q', 'opdId', 'urusanId', 'opdList', 'urusanList', 'sasaranList'));
    }

    public function store(StoreProgramRequest $request): RedirectResponse
    {
        $program = Program::create($request->validated());
        $this->logAudit('create', $program);

        return $this->redirectSuccess('program.index', 'Data Program berhasil ditambahkan.');
    }

    public function update(UpdateProgramRequest $request, Program $program): RedirectResponse
    {
        $dataLama = $program->getOriginal();
        $program->update($request->validated());
        $this->logAudit('update', $program, $dataLama);

        return $this->redirectSuccess('program.index', 'Data Program berhasil diperbarui.');
    }

    public function destroy(Program $program): RedirectResponse
    {
        if ($program->kegiatans()->exists()) {
            return $this->flashError('Program tidak dapat dihapus karena masih memiliki kegiatan terkait.');
        }

        $dataLama = $program->getOriginal();
        $program->delete();
        $this->logAudit('delete', $program, $dataLama);

        return $this->redirectSuccess('program.index', 'Data Program berhasil dihapus.');
    }
}