@extends('layouts.app')

@section('title', 'Kegiatan')
@section('subtitle', 'Kelola data kegiatan per program')

@section('content')
    <x-ui.breadcrumb :items="['Master Data' => '', 'Kegiatan' => '']" />

    <x-ui.card>
        <x-slot:actions>
            <div class="flex items-center gap-2">
                <a href="{{ route('struktur.import.form') }}" class="btn-secondary btn-sm inline-flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Impor Excel
                </a>
                <button @click="$dispatch('open-modal-kegiatan-create')" class="btn-primary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Kegiatan
                </button>
            </div>
        </x-slot:actions>

        <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari kegiatan..." class="input">
            <select name="program_id" class="select" onchange="this.form.submit()">
                <option value="">Semua Program</option>
                @foreach($programList as $p)
                    <option value="{{ $p->id }}" @selected($programId == $p->id)>{{ $p->nama }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary justify-center">Filter</button>
        </form>

        <x-ui.table :headers="['Kode', 'Nama Kegiatan', 'Program', 'Jml Sub Kegiatan', 'Aksi']">
            @forelse($items as $item)
                <tr>
                    <td><span class="badge-maroon">{{ $item->kode ?? '-' }}</span></td>
                    <td class="font-medium text-slate-800 max-w-sm">{{ $item->nama }}</td>
                    <td class="text-xs">{{ $item->program?->nama ?? '-' }}</td>
                    <td>{{ $item->sub_kegiatans_count }}</td>
                    <td>
                        <div class="flex items-center gap-1">
                            <button @click='$dispatch("open-modal-kegiatan-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Ubah</button>
                            <form method="POST" action="{{ route('kegiatan.destroy', $item) }}" onsubmit="return confirm('Hapus data kegiatan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>

                <x-ui.modal id="kegiatan-edit-{{ $item->id }}" title="Ubah Kegiatan" size="lg">
                    <form method="POST" action="{{ route('kegiatan.update', $item) }}" id="form-kegiatan-edit-{{ $item->id }}">
                        @csrf @method('PUT')
                        @include('master.kegiatan._form', ['kegiatan' => $item, 'programList' => $programList])
                    </form>
                    <x-slot:footer>
                        <button type="button" @click='$dispatch("close-modal-kegiatan-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Batal</button>
                        <button type="submit" form="form-kegiatan-edit-{{ $item->id }}" class="btn-primary btn-sm">Simpan Perubahan</button>
                    </x-slot:footer>
                </x-ui.modal>
            @empty
                <tr><td colspan="5"><x-ui.empty-state title="Belum ada data kegiatan" /></td></tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">{{ $items->links() }}</div>
    </x-ui.card>

    <x-ui.modal id="kegiatan-create" title="Tambah Kegiatan Baru" size="lg">
        <form method="POST" action="{{ route('kegiatan.store') }}" id="form-kegiatan-create">
            @csrf
            @include('master.kegiatan._form', ['kegiatan' => null, 'programList' => $programList])
        </form>
        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal-kegiatan-create')" class="btn-secondary btn-sm">Batal</button>
            <button type="submit" form="form-kegiatan-create" class="btn-primary btn-sm">Simpan</button>
        </x-slot:footer>
    </x-ui.modal>
@endsection