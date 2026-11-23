@extends('layouts.app')

@section('title', 'Urusan')
@section('subtitle', 'Kelola data urusan pemerintahan')

@section('content')
    <x-ui.breadcrumb :items="['Master Data' => '', 'Urusan' => '']" />

    <x-ui.card>
        <x-slot:actions>
            <button @click="$dispatch('open-modal-urusan-create')" class="btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Urusan
            </button>
        </x-slot:actions>

        <form method="GET" class="mb-5">
            <div class="relative max-w-sm">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                </svg>
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama atau kode urusan..."
                       class="input pl-9" onchange="this.form.submit()">
            </div>
        </form>

        <x-ui.table :headers="['Kode', 'Nama Urusan', 'Jml Program', 'Aksi']">
            @forelse($items as $item)
                <tr>
                    <td><span class="badge-maroon">{{ $item->kode }}</span></td>
                    <td class="font-medium text-slate-800">{{ $item->nama }}</td>
                    <td>{{ $item->programs_count }}</td>
                    <td>
                        <div class="flex items-center gap-1">
                            <button @click='$dispatch("open-modal-urusan-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Ubah</button>
                            <form method="POST" action="{{ route('urusan.destroy', $item) }}" onsubmit="return confirm('Hapus data urusan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>

                <x-ui.modal id="urusan-edit-{{ $item->id }}" title="Ubah Urusan" size="md">
                    <form method="POST" action="{{ route('urusan.update', $item) }}" id="form-urusan-edit-{{ $item->id }}">
                        @csrf @method('PUT')
                        @include('master.urusan._form', ['urusan' => $item])
                    </form>
                    <x-slot:footer>
                        <button type="button" @click='$dispatch("close-modal-urusan-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Batal</button>
                        <button type="submit" form="form-urusan-edit-{{ $item->id }}" class="btn-primary btn-sm">Simpan Perubahan</button>
                    </x-slot:footer>
                </x-ui.modal>
            @empty
                <tr><td colspan="4"><x-ui.empty-state title="Belum ada data urusan" /></td></tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">{{ $items->links() }}</div>
    </x-ui.card>

    <x-ui.modal id="urusan-create" title="Tambah Urusan Baru" size="md">
        <form method="POST" action="{{ route('urusan.store') }}" id="form-urusan-create">
            @csrf
            @include('master.urusan._form', ['urusan' => null])
        </form>
        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal-urusan-create')" class="btn-secondary btn-sm">Batal</button>
            <button type="submit" form="form-urusan-create" class="btn-primary btn-sm">Simpan</button>
        </x-slot:footer>
    </x-ui.modal>
@endsection