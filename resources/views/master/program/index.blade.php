@extends('layouts.app')

@section('title', 'Program')
@section('subtitle', 'Kelola data program pembangunan daerah')

@section('content')
    <x-ui.breadcrumb :items="['Master Data' => '', 'Program' => '']" />

    <x-ui.card>
        <x-slot:actions>
            <div class="flex items-center gap-2">
                <a href="{{ route('struktur.import.form') }}" class="btn-secondary btn-sm inline-flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Impor Excel
                </a>
                <button @click="$dispatch('open-modal-program-create')" class="btn-primary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Program
                </button>
            </div>
        </x-slot:actions>

        {{-- Filter --}}
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-5">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari program..." class="input">
            <select name="urusan_id" class="select" onchange="this.form.submit()">
                <option value="">Semua Urusan</option>
                @foreach($urusanList as $u)
                    <option value="{{ $u->id }}" @selected($urusanId == $u->id)>{{ $u->nama }}</option>
                @endforeach
            </select>
            <select name="opd_id" class="select" onchange="this.form.submit()">
                <option value="">Semua OPD</option>
                @foreach($opdList as $o)
                    <option value="{{ $o->id }}" @selected($opdId == $o->id)>{{ $o->nama }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary justify-center">Filter</button>
        </form>

        <x-ui.table :headers="['Kode', 'Nama Program', 'Urusan', 'OPD', 'Jml Kegiatan', 'Aksi']">
            @forelse($items as $item)
                <tr>
                    <td><span class="badge-maroon">{{ $item->kode ?? '-' }}</span></td>
                    <td class="font-medium text-slate-800 max-w-xs">{{ $item->nama }}</td>
                    <td class="text-xs">{{ $item->urusan?->nama ?? '-' }}</td>
                    <td class="text-xs">{{ $item->opd?->nama ?? '-' }}</td>
                    <td>{{ $item->kegiatans_count }}</td>
                    <td>
                        <div class="flex items-center gap-1">
                            <button @click='$dispatch("open-modal-program-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Ubah</button>
                            <form method="POST" action="{{ route('program.destroy', $item) }}" onsubmit="return confirm('Hapus data program ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>

                <x-ui.modal id="program-edit-{{ $item->id }}" title="Ubah Program" size="lg">
                    <form method="POST" action="{{ route('program.update', $item) }}" id="form-program-edit-{{ $item->id }}">
                        @csrf @method('PUT')
                        @include('master.program._form', ['program' => $item, 'urusanList' => $urusanList, 'opdList' => $opdList, 'sasaranList' => $sasaranList])
                    </form>
                    <x-slot:footer>
                        <button type="button" @click='$dispatch("close-modal-program-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Batal</button>
                        <button type="submit" form="form-program-edit-{{ $item->id }}" class="btn-primary btn-sm">Simpan Perubahan</button>
                    </x-slot:footer>
                </x-ui.modal>
            @empty
                <tr><td colspan="6"><x-ui.empty-state title="Belum ada data program" /></td></tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">{{ $items->links() }}</div>
    </x-ui.card>

    <x-ui.modal id="program-create" title="Tambah Program Baru" size="lg">
        <form method="POST" action="{{ route('program.store') }}" id="form-program-create">
            @csrf
            @include('master.program._form', ['program' => null, 'urusanList' => $urusanList, 'opdList' => $opdList, 'sasaranList' => $sasaranList])
        </form>
        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal-program-create')" class="btn-secondary btn-sm">Batal</button>
            <button type="submit" form="form-program-create" class="btn-primary btn-sm">Simpan</button>
        </x-slot:footer>
    </x-ui.modal>
@endsection