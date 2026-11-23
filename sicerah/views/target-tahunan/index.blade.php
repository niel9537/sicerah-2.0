@extends('layouts.app')

@section('title', 'Target Tahunan')
@section('subtitle', 'Kelola target kinerja & pagu anggaran per sub kegiatan')

@section('content')
    <x-ui.breadcrumb :items="['Master Data' => '', 'Target Tahunan' => '']" />

    <x-ui.card>
        <x-slot:actions>
            <button @click="$dispatch('open-modal-target-create')" class="btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Target
            </button>
        </x-slot:actions>

        <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari sub kegiatan..." class="input">
            <select name="tahun" class="select" onchange="this.form.submit()">
                @foreach(range(now()->year + 1, now()->year - 3) as $y)
                    <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary justify-center">Filter</button>
        </form>

        <x-ui.table :headers="['Sub Kegiatan', 'OPD', 'Target Kinerja', 'Pagu Renstra', 'Pagu Renja (Murni)', 'Pagu Perubahan', 'Pagu Efektif', 'Aksi']">
            @forelse($items as $item)
                <tr>
                    <td class="font-medium text-slate-800 max-w-xs">{{ $item->subKegiatan?->nama ?? '-' }}</td>
                    <td class="text-xs">{{ $item->subKegiatan?->kegiatan?->program?->opd?->nama ?? '-' }}</td>
                    <td>{{ number_format($item->target_kinerja, 2, ',', '.') }} <span class="text-xs text-slate-400">{{ $item->subKegiatan?->satuan }}</span></td>
                    <td class="text-xs">Rp {{ number_format($item->pagu_renstra ?? 0, 0, ',', '.') }}</td>
                    <td class="text-xs">Rp {{ number_format($item->pagu_renja ?? 0, 0, ',', '.') }}</td>
                    <td class="text-xs">
                        @if($item->pagu_perubahan)
                            Rp {{ number_format($item->pagu_perubahan, 0, ',', '.') }}
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge-{{ $item->pagu_perubahan ? 'warning' : 'success' }}">
                            Rp {{ number_format($item->paguEfektif(), 0, ',', '.') }}
                        </span>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $item->sumberPaguLabel() }}</p>
                    </td>
                    <td>
                        <div class="flex items-center gap-1">
                            <button @click='$dispatch("open-modal-target-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Ubah</button>
                            <form method="POST" action="{{ route('target-tahunan.destroy', $item) }}" onsubmit="return confirm('Hapus target ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>

                <x-ui.modal id="target-edit-{{ $item->id }}" title="Ubah Target Tahunan" size="lg">
                    <form method="POST" action="{{ route('target-tahunan.update', $item) }}" id="form-target-edit-{{ $item->id }}">
                        @csrf @method('PUT')
                        @include('target-tahunan._form', ['target' => $item, 'subKegiatanList' => $subKegiatanList])
                    </form>
                    <x-slot:footer>
                        <button type="button" @click='$dispatch("close-modal-target-edit-{{ $item->id }}")' class="btn-secondary btn-sm">Batal</button>
                        <button type="submit" form="form-target-edit-{{ $item->id }}" class="btn-primary btn-sm">Simpan Perubahan</button>
                    </x-slot:footer>
                </x-ui.modal>
            @empty
                <tr><td colspan="8"><x-ui.empty-state title="Belum ada target tahun {{ $tahun }}" description="Tambahkan target kinerja & pagu untuk sub kegiatan." /></td></tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">{{ $items->links() }}</div>
    </x-ui.card>

    <x-ui.modal id="target-create" title="Tambah Target Tahunan" size="lg">
        <form method="POST" action="{{ route('target-tahunan.store') }}" id="form-target-create">
            @csrf
            @include('target-tahunan._form', ['target' => null, 'subKegiatanList' => $subKegiatanList, 'tahunDefault' => $tahun])
        </form>
        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal-target-create')" class="btn-secondary btn-sm">Batal</button>
            <button type="submit" form="form-target-create" class="btn-primary btn-sm">Simpan</button>
        </x-slot:footer>
    </x-ui.modal>
@endsection