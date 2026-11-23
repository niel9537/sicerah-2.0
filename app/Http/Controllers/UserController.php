<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Opd;
use App\Models\User;
use App\Traits\HasFlashMessage;
use App\Traits\LogsAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    use HasFlashMessage, LogsAudit;

    public function index(Request $request): View
    {
        $q    = trim((string) $request->input('q'));
        $role = $request->input('role');

        $items = User::query()
            ->with('opd')
            ->when($q, fn ($query) => $query->where('nama', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"))
            ->when($role, fn ($query) => $query->where('role', $role))
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        $opdList = Opd::orderBy('nama')->get();

        return view('master.user.index', compact('items', 'q', 'role', 'opdList'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $this->logAudit('create', $user);

        return $this->redirectSuccess('user.index', 'Pengguna berhasil ditambahkan.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $dataLama = $user->getOriginal();
        $user->update($data);
        $this->logAudit('update', $user, $dataLama);

        return $this->redirectSuccess('user.index', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return $this->flashError('Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $dataLama = $user->getOriginal();
        $user->delete();
        $this->logAudit('delete', $user, $dataLama);

        return $this->redirectSuccess('user.index', 'Pengguna berhasil dihapus.');
    }
}