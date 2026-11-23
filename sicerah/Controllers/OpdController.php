<?php

namespace App\Http\Controllers;

use App\Http\Requests\Opd\StoreOpdRequest;
use App\Http\Requests\Opd\UpdateOpdRequest;
use App\Models\Opd;
use App\Traits\HasFlashMessage;
use App\Traits\LogsAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OpdController extends Controller
{
    use HasFlashMessage, LogsAudit;

    public function index(Request $request): View
    {
        $q = trim((string) $request->input('q'));

        $items = Opd::query()
            ->when($q, fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                  ->orWhere('kode', 'like', "%{$q}%");
            }))
            ->withCount('programs')
            ->orderBy('kode')
            ->paginate(10)
            ->withQueryString();

        return view('master.opd.index', compact('items', 'q'));
    }

    public function store(StoreOpdRequest $request): RedirectResponse
    {
        $opd = Opd::create($request->validated());
        $this->logAudit('create', $opd);

        return $this->redirectSuccess('opd.index', 'Data OPD berhasil ditambahkan.');
    }

    public function update(UpdateOpdRequest $request, Opd $opd): RedirectResponse
    {
        $dataLama = $opd->getOriginal();
        $opd->update($request->validated());
        $this->logAudit('update', $opd, $dataLama);

        return $this->redirectSuccess('opd.index', 'Data OPD berhasil diperbarui.');
    }

    public function destroy(Opd $opd): RedirectResponse
    {
        if ($opd->programs()->exists()) {
            return $this->flashError('OPD tidak dapat dihapus karena masih memiliki program terkait.');
        }

        $dataLama = $opd->getOriginal();
        $opd->delete();
        $this->logAudit('delete', $opd, $dataLama);

        return $this->redirectSuccess('opd.index', 'Data OPD berhasil dihapus.');
    }
}