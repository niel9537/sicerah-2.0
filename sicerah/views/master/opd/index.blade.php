@extends('layouts.app')

@section('title', 'SKPD / OPD')
@section('subtitle', 'Kelola data Perangkat Daerah')

@section('content')
    <x-ui.breadcrumb :items="['Master Data' => '', 'SKPD / OPD' => '']" />

    <x-ui.card>
        <x-slot:actions>
            <button @click="$dispatch('open-modal-opd-create')" class="btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah OPD
            </button>
        </x-slot:actions>

        {{-- Search --}}
        <form method="GET" class="mb-5">
            <div class="relative max-w-sm">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                </svg>
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama atau kode OPD..."
                       class="input pl-9" onchange="this.form.submit()">
            </div>
        </form>

        <x-ui.table :headers="['Kode', 'Nama OPD', 'Kontak', 'Jml Program', 'Aksi']">
            @forelse($items as $item)
                <tr>
                    <td><span class="badge-maroon">{{ $item->kode ?? '-' }}</span></td>
                    <td class="font-medium text-slate-800">{{ $item->nama }}</td>
                    <td class="text-xs text-slate-500">
                        {{ $item->telepon ?? '-' }}<br>
                        {{ $item->email ?? '-' }}
                    </td>
                    <td>{{ $item->programs_count }}</td>
                    <td>
                        <div class="flex items-center gap-1">
                            <button @click='$dispatch("open-modal-opd-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Ubah</button>
                            <form method="POST" action="{{ route('opd.destroy', $item) }}"
                                  onsubmit="return confirm('Hapus data OPD ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>

                {{-- Modal Edit per item --}}
                <x-ui.modal id="opd-edit-{{ $item->id }}" title="Ubah Data OPD" size="md">
                    <form method="POST" action="{{ route('opd.update', $item) }}" id="form-opd-edit-{{ $item->id }}">
                        @csrf @method('PUT')
                        @include('master.opd._form', ['opd' => $item])
                    </form>
                    <x-slot:footer>
                        <button type="button" @click='$dispatch("close-modal-opd-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Batal</button>
                        <button type="submit" form="form-opd-edit-{{ $item->id }}" class="btn-primary btn-sm">Simpan Perubahan</button>
                    </x-slot:footer>
                </x-ui.modal>
            @empty
                <tr><td colspan="5"><x-ui.empty-state title="Belum ada data OPD" /></td></tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">{{ $items->links() }}</div>
    </x-ui.card>

    {{-- Modal Create --}}
    <x-ui.modal id="opd-create" title="Tambah OPD Baru" size="md">
        <form method="POST" action="{{ route('opd.store') }}" id="form-opd-create">
            @csrf
            @include('master.opd._form', ['opd' => null])
        </form>
        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal-opd-create')" class="btn-secondary btn-sm">Batal</button>
            <button type="submit" form="form-opd-create" class="btn-primary btn-sm">Simpan</button>
        </x-slot:footer>
    </x-ui.modal>
@endsection