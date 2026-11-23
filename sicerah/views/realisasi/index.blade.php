@extends('layouts.app')

@section('title', 'Realisasi Kinerja')
@section('subtitle', 'Rekap realisasi fisik & keuangan per sub kegiatan')

@section('content')
    <x-ui.breadcrumb :items="['Realisasi Kinerja' => '']" />

    <x-ui.card class="mb-4">
        <form method="GET" class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <select name="tahun" class="select" onchange="this.form.submit()">
                @foreach(range(now()->year + 1, now()->year - 3) as $y)
                    <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                @endforeach
            </select>
            <select name="triwulan" class="select" onchange="this.form.submit()">
                @foreach([1,2,3,4] as $tw)
                    <option value="{{ $tw }}" @selected($triwulan == $tw)>Triwulan {{ ['I','II','III','IV'][$tw-1] }}</option>
                @endforeach
            </select>
            <select name="opd_id" class="select" onchange="this.form.submit()">
                <option value="">Semua OPD</option>
                @foreach($opdList as $o)
                    <option value="{{ $o->id }}" @selected($opdId == $o->id)>{{ $o->nama }}</option>
                @endforeach
            </select>
            <select name="program_id" class="select" onchange="this.form.submit()">
                <option value="">Semua Program</option>
                @foreach($programList as $p)
                    <option value="{{ $p->id }}" @selected($programId == $p->id)>{{ Str::limit($p->nama, 30) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary justify-center">Terapkan</button>
        </form>
    </x-ui.card>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <x-ui.stat-card label="Total Pagu Efektif" value="Rp {{ number_format($summary['total_pagu']/1000000, 0, ',', '.') }} Jt" color="maroon" />
        <x-ui.stat-card label="Total Realisasi Anggaran" value="Rp {{ number_format($summary['total_realisasi']/1000000, 0, ',', '.') }} Jt" color="blue" />
        <x-ui.stat-card label="Rata-rata Capaian Fisik" value="{{ $summary['rata_fisik'] }}%" color="green" />
        <x-ui.stat-card label="Rata-rata Capaian Keuangan" value="{{ $summary['rata_keuangan'] }}%" color="amber" />
    </div>

    <x-ui.card title="Rincian per Sub Kegiatan" subtitle="Triwulan {{ ['I','II','III','IV'][$triwulan-1] }} Tahun {{ $tahun }}">
        <x-ui.table :headers="['Sub Kegiatan', 'OPD', 'Target Fisik', 'Realisasi Fisik', '% Fisik', 'Pagu Efektif', 'Realisasi Anggaran', '% Keuangan', 'Status']">
            @forelse($rekap as $r)
                <tr>
                    <td class="font-medium text-slate-800 max-w-xs">{{ $r->subkegiatan->nama }}</td>
                    <td class="text-xs">{{ $r->opd?->nama ?? '-' }}</td>
                    <td>{{ number_format($r->target_fisik, 2, ',', '.') }}</td>
                    <td>{{ number_format($r->realisasi_fisik, 2, ',', '.') }}</td>
                    <td><span class="badge-{{ $r->pct_fisik >= 100 ? 'success' : ($r->pct_fisik >= 50 ? 'warning' : 'danger') }}">{{ $r->pct_fisik }}%</span></td>
                    <td class="text-xs">Rp {{ number_format($r->pagu_efektif, 0, ',', '.') }}</td>
                    <td class="text-xs">Rp {{ number_format($r->realisasi_anggaran, 0, ',', '.') }}</td>
                    <td><span class="badge-{{ $r->pct_keuangan >= 100 ? 'success' : ($r->pct_keuangan >= 50 ? 'warning' : 'danger') }}">{{ $r->pct_keuangan }}%</span></td>
                    <td>
                        @if($r->status)
                            <span class="badge-default">{{ ucfirst($r->status) }}</span>
                        @else
                            <span class="text-xs text-slate-400">Belum diisi</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9"><x-ui.empty-state title="Tidak ada data" description="Belum ada sub kegiatan yang sesuai filter." /></td></tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
@endsection