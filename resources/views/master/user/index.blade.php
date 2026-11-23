@extends('layouts.app')

@section('title', 'Pengguna')
@section('subtitle', 'Kelola akun pengguna sistem')

@section('content')
    <x-ui.breadcrumb :items="['Pengaturan' => '', 'Pengguna' => '']" />

    <x-ui.card>
        <x-slot:actions>
            <button @click="$dispatch('open-modal-user-create')" class="btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Pengguna
            </button>
        </x-slot:actions>

        <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama atau email..." class="input">
            <select name="role" class="select" onchange="this.form.submit()">
                <option value="">Semua Role</option>
                <option value="super_admin" @selected($role === 'super_admin')>Super Administrator</option>
                <option value="admin_bappeda" @selected($role === 'admin_bappeda')>Admin Bappeda</option>
                <option value="operator_opd" @selected($role === 'operator_opd')>Operator OPD</option>
                <option value="verifikator" @selected($role === 'verifikator')>Verifikator</option>
                <option value="inspektorat" @selected($role === 'inspektorat')>Inspektorat</option>
                <option value="pimpinan" @selected($role === 'pimpinan')>Pimpinan</option>
            </select>
            <button type="submit" class="btn-secondary justify-center">Filter</button>
        </form>

        <x-ui.table :headers="['Nama', 'Email', 'Role', 'OPD', 'Aksi']">
            @forelse($items as $item)
                <tr>
                    <td class="font-medium text-slate-800">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-brand-gradient text-white flex items-center justify-center text-xs font-semibold">
                                {{ strtoupper(mb_substr($item->nama, 0, 1)) }}
                            </div>
                            {{ $item->nama }}
                        </div>
                    </td>
                    <td class="text-xs">{{ $item->email }}</td>
                    <td><span class="badge-maroon">{{ $item->roleLabel() }}</span></td>
                    <td class="text-xs">{{ $item->opd?->nama ?? '-' }}</td>
                    <td>
                        <div class="flex items-center gap-1">
                            <button @click='$dispatch("open-modal-user-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Ubah</button>
                            @if($item->id !== auth()->id())
                                <form method="POST" action="{{ route('user.destroy', $item) }}" onsubmit="return confirm('Hapus pengguna ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm">Hapus</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>

                <x-ui.modal id="user-edit-{{ $item->id }}" title="Ubah Pengguna" size="md">
                    <form method="POST" action="{{ route('user.update', $item) }}" id="form-user-edit-{{ $item->id }}">
                        @csrf @method('PUT')
                        @include('master.user._form', ['user' => $item, 'opdList' => $opdList])
                    </form>
                    <x-slot:footer>
                        <button type="button" @click='$dispatch("close-modal-user-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Batal</button>
                        <button type="submit" form="form-user-edit-{{ $item->id }}" class="btn-primary btn-sm">Simpan Perubahan</button>
                    </x-slot:footer>
                </x-ui.modal>
            @empty
                <tr><td colspan="5"><x-ui.empty-state title="Belum ada data pengguna" /></td></tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">{{ $items->links() }}</div>
    </x-ui.card>

    <x-ui.modal id="user-create" title="Tambah Pengguna Baru" size="md">
        <form method="POST" action="{{ route('user.store') }}" id="form-user-create">
            @csrf
            @include('master.user._form', ['user' => null, 'opdList' => $opdList])
        </form>
        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal-user-create')" class="btn-secondary btn-sm">Batal</button>
            <button type="submit" form="form-user-create" class="btn-primary btn-sm">Simpan</button>
        </x-slot:footer>
    </x-ui.modal>
@endsection