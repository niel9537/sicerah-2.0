<?php

namespace App\Http\Controllers;

use App\Http\Requests\Urusan\StoreUrusanRequest;
use App\Http\Requests\Urusan\UpdateUrusanRequest;
use App\Models\Urusan;
use App\Traits\HasFlashMessage;
use App\Traits\LogsAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UrusanController extends Controller
{
    use HasFlashMessage, LogsAudit;

    public function index(Request $request): View
    {
        $q = trim((string) $request->input('q'));

        $items = Urusan::query()
            ->when($q, fn ($query) => $query->where('nama', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%"))
            ->withCount('programs')
            ->orderBy('kode')
            ->paginate(10)
            ->withQueryString();

        return view('master.urusan.index', compact('items', 'q'));
    }

    public function store(StoreUrusanRequest $request): RedirectResponse
    {
        $urusan = Urusan::create($request->validated());
        $this->logAudit('create', $urusan);

        return $this->redirectSuccess('urusan.index', 'Data Urusan berhasil ditambahkan.');
    }

    public function update(UpdateUrusanRequest $request, Urusan $urusan): RedirectResponse
    {
        $dataLama = $urusan->getOriginal();
        $urusan->update($request->validated());
        $this->logAudit('update', $urusan, $dataLama);

        return $this->redirectSuccess('urusan.index', 'Data Urusan berhasil diperbarui.');
    }

    public function destroy(Urusan $urusan): RedirectResponse
    {
        if ($urusan->programs()->exists()) {
            return $this->flashError('Urusan tidak dapat dihapus karena masih memiliki program terkait.');
        }

        $dataLama = $urusan->getOriginal();
        $urusan->delete();
        $this->logAudit('delete', $urusan, $dataLama);

        return $this->redirectSuccess('urusan.index', 'Data Urusan berhasil dihapus.');
    }
}