@extends('layouts.app')

@section('title', 'LKPJ')
@section('subtitle', 'Laporan Keterangan Pertanggungjawaban per urusan')

@section('content')
    <x-ui.breadcrumb :items="['LKPJ' => '']" />

    <x-ui.card>
        <x-slot:actions>
            <button @click="$dispatch('open-modal-lkpj-create')" class="btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah LKPJ
            </button>
        </x-slot:actions>

        <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari uraian program..." class="input">
            <select name="tahun" class="select" onchange="this.form.submit()">
                @foreach(range(now()->year + 1, now()->year - 3) as $y)
                    <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary justify-center">Filter</button>
        </form>

        <x-ui.table :headers="['Urusan', 'OPD', 'Uraian Program', 'Status', 'Aksi']">
            @forelse($items as $item)
                <tr>
                    <td class="text-xs">{{ $item->urusan?->nama ?? '-' }}</td>
                    <td class="text-xs">{{ $item->opd?->nama ?? '-' }}</td>
                    <td class="max-w-sm text-sm">{{ Str::limit($item->uraian_program, 80) ?: '-' }}</td>
                    <td>
                        <span class="badge-{{ $item->status === 'diverifikasi' ? 'success' : ($item->status === 'diajukan' ? 'warning' : 'default') }}">
                            {{ ucfirst($item->status ?? 'draft') }}
                        </span>
                    </td>
                    <td>
                        <div class="flex items-center gap-1">
                            <button @click='$dispatch("open-modal-lkpj-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Ubah</button>
                            <form method="POST" action="{{ route('lkpj.destroy', $item) }}" onsubmit="return confirm('Hapus LKPJ ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>

                <x-ui.modal id="lkpj-edit-{{ $item->id }}" title="Ubah LKPJ" size="2xl">
                    <form method="POST" action="{{ route('lkpj.update', $item) }}" id="form-lkpj-edit-{{ $item->id }}">
                        @csrf @method('PUT')
                        @include('lkpj._form', ['lkpj' => $item, 'urusanList' => $urusanList, 'opdList' => $opdList])
                    </form>
                    <x-slot:footer>
                        <button type="button" @click='$dispatch("close-modal-lkpj-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Batal</button>
                        <button type="submit" form="form-lkpj-edit-{{ $item->id }}" class="btn-primary btn-sm">Simpan Perubahan</button>
                    </x-slot:footer>
                </x-ui.modal>
            @empty
                <tr><td colspan="5"><x-ui.empty-state title="Belum ada LKPJ tahun {{ $tahun }}" /></td></tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">{{ $items->links() }}</div>
    </x-ui.card>

    <x-ui.modal id="lkpj-create" title="Tambah LKPJ Baru" size="2xl">
        <form method="POST" action="{{ route('lkpj.store') }}" id="form-lkpj-create">
            @csrf
            @include('lkpj._form', ['lkpj' => null, 'urusanList' => $urusanList, 'opdList' => $opdList, 'tahunDefault' => $tahun])
        </form>
        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal-lkpj-create')" class="btn-secondary btn-sm">Batal</button>
            <button type="submit" form="form-lkpj-create" class="btn-primary btn-sm">Simpan</button>
        </x-slot:footer>
    </x-ui.modal>
@endsection