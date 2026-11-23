@extends('layouts.app')

@section('title', 'Verifikasi Evaluasi Renja')
@section('subtitle', 'Tinjau dan verifikasi evaluasi yang diajukan OPD')

@section('content')
    <x-ui.breadcrumb :items="['Verifikasi' => '']" />

    <x-ui.card>
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-5">
            <select name="status" class="select" onchange="this.form.submit()">
                <option value="diajukan" @selected($status === 'diajukan')>Menunggu Verifikasi</option>
                <option value="diverifikasi" @selected($status === 'diverifikasi')>Sudah Diverifikasi</option>
                <option value="ditolak" @selected($status === 'ditolak')>Ditolak</option>
                <option value="" @selected($status === '')>Semua Status</option>
            </select>
            <select name="periode_id" class="select" onchange="this.form.submit()">
                <option value="">Semua Periode</option>
                @foreach($periodeList as $p)
                    <option value="{{ $p->id }}" @selected($periodeId == $p->id)>{{ $p->label }}</option>
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

        <x-ui.table :headers="['Sub Kegiatan', 'OPD', 'Periode', 'Diajukan Oleh', 'Capaian', 'Status', 'Aksi']">
            @forelse($items as $item)
                <tr>
                    <td class="font-medium text-slate-800 max-w-xs">{{ $item->subKegiatan?->nama }}</td>
                    <td class="text-xs">{{ $item->subKegiatan?->kegiatan?->program?->opd?->nama }}</td>
                    <td>{{ $item->periode?->label }}</td>
                    <td class="text-xs">{{ $item->creator?->nama ?? '-' }}</td>
                    <td class="text-xs">Fisik: {{ number_format($item->persentaseFisik(), 1) }}% · Keu: {{ number_format($item->persentaseKeuangan(), 1) }}%</td>
                    <td><span class="{{ $item->statusBadgeClass() }}">{{ $item->statusLabel() }}</span></td>
                    <td><a href="{{ route('verifikasi.show', $item) }}" class="btn-secondary btn-sm">Tinjau</a></td>
                </tr>
            @empty
                <tr><td colspan="7"><x-ui.empty-state title="Tidak ada data" description="Belum ada evaluasi yang sesuai dengan filter ini." /></td></tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">{{ $items->links() }}</div>
    </x-ui.card>
@endsection