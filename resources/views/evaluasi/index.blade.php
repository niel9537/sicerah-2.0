@extends('layouts.app')

@section('title', 'Evaluasi Renja')
@section('subtitle', 'Kelola realisasi kinerja per sub kegiatan, triwulanan')

@section('content')
    <x-ui.breadcrumb :items="['Evaluasi Renja' => '']" />

    <x-ui.card>
        <x-slot:actions>
            <div class="flex items-center gap-2">
                @if(in_array(auth()->user()->role, ['super_admin', 'admin_bappeda'], true))
                    <a href="{{ route('evaluasi.import.form') }}" class="btn-secondary btn-sm inline-flex items-center gap-2 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Impor Excel
                    </a>
                @endif
                <a href="{{ route('evaluasi.pilih') }}" class="btn-primary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Isi Evaluasi
                </a>
            </div>
        </x-slot:actions>

        <form method="GET" class="mb-5 max-w-xs">
            <select name="tahun" class="select" onchange="this.form.submit()">
                @foreach(range(now()->year + 1, now()->year - 3) as $y)
                    <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                @endforeach
            </select>
        </form>

        <x-ui.table :headers="['Sub Kegiatan', 'OPD', 'TW I', 'TW II', 'TW III', 'TW IV', 'Aksi']">
            @forelse($subKegiatanList as $sub)
                @php $byTw = $sub->evaluasiRenjas->keyBy(fn ($e) => $e->periode->triwulan ?? null); @endphp
                <tr>
                    <td class="font-medium text-slate-800 max-w-xs">{{ $sub->nama }}</td>
                    <td class="text-xs">{{ $sub->kegiatan?->program?->opd?->nama ?? '-' }}</td>
                    @foreach([1, 2, 3, 4] as $tw)
                        @php $ev = $byTw->get($tw); @endphp
                        <td>
                            @if($ev)
                                <span class="{{ $ev->statusBadgeClass() }}">{{ $ev->statusLabel() }}</span>
                            @else
                                <span class="badge-default">Kosong</span>
                            @endif
                        </td>
                    @endforeach
                    <td>
                        <a href="{{ route('evaluasi.kelola', ['subkegiatan' => $sub->id, 'tahun' => $tahun]) }}" class="btn-secondary btn-sm">Kelola</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><x-ui.empty-state title="Belum ada sub kegiatan" description="Hubungi Admin Bappeda untuk melengkapi data master." /></td></tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">{{ $subKegiatanList->links() }}</div>
    </x-ui.card>
@endsection