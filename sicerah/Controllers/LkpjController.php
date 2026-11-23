<?php

namespace App\Http\Controllers;

use App\Http\Requests\Lkpj\StoreLkpjRequest;
use App\Http\Requests\Lkpj\UpdateLkpjRequest;
use App\Models\Lkpj;
use App\Models\Opd;
use App\Models\Urusan;
use App\Traits\HasFlashMessage;
use App\Traits\LogsAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LkpjController extends Controller
{
    use HasFlashMessage, LogsAudit;

    public function index(Request $request): View
    {
        $user  = auth()->user();
        $tahun = (int) $request->input('tahun', now()->year);
        $q     = trim((string) $request->input('q'));

        $items = Lkpj::query()
            ->with(['urusan', 'opd'])
            ->where('tahun', $tahun)
            ->when($user->role === 'operator_opd', fn ($query) => $query->where('opd_id', $user->opd_id))
            ->when($q, fn ($query) => $query->where('uraian_program', 'like', "%{$q}%"))
            ->orderBy('urusan_id')
            ->paginate(10)
            ->withQueryString();

        $urusanList = Urusan::orderBy('nama')->get();
        $opdList    = Opd::orderBy('nama')->get();

        return view('lkpj.index', compact('items', 'tahun', 'q', 'urusanList', 'opdList'));
    }

    public function store(StoreLkpjRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        if ($user->role === 'operator_opd') {
            $data['opd_id'] = $user->opd_id;
            $data['status'] = $data['status'] ?? 'draft';
        }

        $lkpj = Lkpj::create($data);
        $this->logAudit('create', $lkpj);

        return $this->redirectSuccess('lkpj.index', 'LKPJ berhasil ditambahkan.', ['tahun' => $lkpj->tahun]);
    }

    public function update(UpdateLkpjRequest $request, Lkpj $lkpj): RedirectResponse
    {
        $this->assertOwnedByUser($lkpj);

        $user = auth()->user();
        $data = $request->validated();

        if ($user->role === 'operator_opd') {
            unset($data['opd_id']);
        }

        $dataLama = $lkpj->getOriginal();
        $lkpj->update($data);
        $this->logAudit('update', $lkpj, $dataLama);

        return $this->redirectSuccess('lkpj.index', 'LKPJ berhasil diperbarui.', ['tahun' => $lkpj->tahun]);
    }

    public function destroy(Lkpj $lkpj): RedirectResponse
    {
        $this->assertOwnedByUser($lkpj);

        $tahun    = $lkpj->tahun;
        $dataLama = $lkpj->getOriginal();
        $lkpj->delete();
        $this->logAudit('delete', $lkpj, $dataLama);

        return $this->redirectSuccess('lkpj.index', 'LKPJ berhasil dihapus.', ['tahun' => $tahun]);
    }

    private function assertOwnedByUser(Lkpj $lkpj): void
    {
        $user = auth()->user();
        if ($user->role !== 'operator_opd') {
            return;
        }

        abort_unless($lkpj->opd_id === $user->opd_id, 403, 'Anda tidak memiliki akses ke data LKPJ ini.');
    }
}