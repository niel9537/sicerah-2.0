@extends('layouts.app')

@section('title', 'Sub Kegiatan')
@section('subtitle', 'Kelola data sub kegiatan beserta indikator')

@section('content')
    <x-ui.breadcrumb :items="['Master Data' => '', 'Sub Kegiatan' => '']" />

    <x-ui.card>
        <x-slot:actions>
            <button @click="$dispatch('open-modal-subkegiatan-create')" class="btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Sub Kegiatan
            </button>
        </x-slot:actions>

        <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari sub kegiatan..." class="input">
            <select name="kegiatan_id" class="select" onchange="this.form.submit()">
                <option value="">Semua Kegiatan</option>
                @foreach($kegiatanList as $k)
                    <option value="{{ $k->id }}" @selected($kegiatanId == $k->id)>{{ Str::limit($k->nama, 40) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary justify-center">Filter</button>
        </form>

        <x-ui.table :headers="['Kode', 'Nama Sub Kegiatan', 'Kegiatan', 'Indikator', 'Satuan', 'Aksi']">
            @forelse($items as $item)
                <tr>
                    <td><span class="badge-maroon">{{ $item->kode ?? '-' }}</span></td>
                    <td class="font-medium text-slate-800 max-w-xs">{{ $item->nama }}</td>
                    <td class="text-xs max-w-xs">{{ Str::limit($item->kegiatan?->nama, 40) }}</td>
                    <td class="text-xs">{{ $item->indikator ?? '-' }}</td>
                    <td><span class="badge-info">{{ $item->satuan ?? '-' }}</span></td>
                    <td>
                        <div class="flex items-center gap-1">
                            <button @click='$dispatch("open-modal-subkegiatan-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Ubah</button>
                            <form method="POST" action="{{ route('subkegiatan.destroy', $item) }}" onsubmit="return confirm('Hapus data sub kegiatan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>

                <x-ui.modal id="subkegiatan-edit-{{ $item->id }}" title="Ubah Sub Kegiatan" size="lg">
                    <form method="POST" action="{{ route('subkegiatan.update', $item) }}" id="form-subkegiatan-edit-{{ $item->id }}">
                        @csrf @method('PUT')
                        @include('master.subkegiatan._form', ['subkegiatan' => $item, 'kegiatanList' => $kegiatanList])
                    </form>
                    <x-slot:footer>
                        <button type="button" @click='$dispatch("close-modal-subkegiatan-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Batal</button>
                        <button type="submit" form="form-subkegiatan-edit-{{ $item->id }}" class="btn-primary btn-sm">Simpan Perubahan</button>
                    </x-slot:footer>
                </x-ui.modal>
            @empty
                <tr><td colspan="6"><x-ui.empty-state title="Belum ada data sub kegiatan" /></td></tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">{{ $items->links() }}</div>
    </x-ui.card>

    <x-ui.modal id="subkegiatan-create" title="Tambah Sub Kegiatan Baru" size="lg">
        <form method="POST" action="{{ route('subkegiatan.store') }}" id="form-subkegiatan-create">
            @csrf
            @include('master.subkegiatan._form', ['subkegiatan' => null, 'kegiatanList' => $kegiatanList])
        </form>
        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal-subkegiatan-create')" class="btn-secondary btn-sm">Batal</button>
            <button type="submit" form="form-subkegiatan-create" class="btn-primary btn-sm">Simpan</button>
        </x-slot:footer>
    </x-ui.modal>
@endsection