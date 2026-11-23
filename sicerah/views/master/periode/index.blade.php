@extends('layouts.app')

@section('title', 'Periode')
@section('subtitle', 'Kelola periode tahun dan triwulan pelaporan')

@section('content')
    <x-ui.breadcrumb :items="['Master Data' => '', 'Periode' => '']" />

    <x-ui.card>
        <x-slot:actions>
            <button @click="$dispatch('open-modal-periode-create')" class="btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Periode
            </button>
        </x-slot:actions>

        <x-ui.table :headers="['Tahun', 'Triwulan', 'Nama', 'Status', 'Aksi']">
            @forelse($items as $item)
                <tr>
                    <td class="font-semibold">{{ $item->tahun }}</td>
                    <td>Triwulan {{ $item->triwulanRomawi() }}</td>
                    <td>{{ $item->nama ?? '-' }}</td>
                    <td>
                        <form method="POST" action="{{ route('periode.toggle', $item) }}" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="{{ $item->status === 'aktif' ? 'badge-success' : 'badge-default' }} cursor-pointer">
                                {{ ucfirst($item->status) }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <div class="flex items-center gap-1">
                            <button @click='$dispatch("open-modal-periode-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Ubah</button>
                            <form method="POST" action="{{ route('periode.destroy', $item) }}" onsubmit="return confirm('Hapus periode ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>

                <x-ui.modal id="periode-edit-{{ $item->id }}" title="Ubah Periode" size="sm">
                    <form method="POST" action="{{ route('periode.update', $item) }}" id="form-periode-edit-{{ $item->id }}">
                        @csrf @method('PUT')
                        @include('master.periode._form', ['periode' => $item])
                    </form>
                    <x-slot:footer>
                        <button type="button" @click='$dispatch("close-modal-periode-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Batal</button>
                        <button type="submit" form="form-periode-edit-{{ $item->id }}" class="btn-primary btn-sm">Simpan Perubahan</button>
                    </x-slot:footer>
                </x-ui.modal>
            @empty
                <tr><td colspan="5"><x-ui.empty-state title="Belum ada data periode" /></td></tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">{{ $items->links() }}</div>
    </x-ui.card>

    <x-ui.modal id="periode-create" title="Tambah Periode Baru" size="sm">
        <form method="POST" action="{{ route('periode.store') }}" id="form-periode-create">
            @csrf
            @include('master.periode._form', ['periode' => null])
        </form>
        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal-periode-create')" class="btn-secondary btn-sm">Batal</button>
            <button type="submit" form="form-periode-create" class="btn-primary btn-sm">Simpan</button>
        </x-slot:footer>
    </x-ui.modal>
@endsection