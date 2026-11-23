@extends('layouts.app')

@section('title', 'RPJMD — Indikator Daerah')
@section('subtitle', 'IKD, IKK dan IKU • Target RPJMD 2025–2030')

@section('content')
    <x-ui.breadcrumb :items="['RPJMD 2025–2030' => '']" />

    <x-ui.card class="mb-5">
        <p class="text-sm text-slate-500 mb-4 leading-relaxed">
            Target indikator daerah dan perangkat daerah disajikan sesuai dokumen RPJMD 2025–2030. Baseline 2024 disimpan sebagai rujukan dasar, nilai rentang dan tanda khusus dipertahankan sesuai data resmi.
        </p>

        <form method="GET" action="{{ route('rpjmd.index') }}" onchange="if (event.target.tagName === 'SELECT') this.requestSubmit();" class="flex flex-wrap items-center gap-3">
            <select name="jenis" aria-label="Jenis indikator" class="select sm:w-auto">
                <option value="">Semua jenis</option>
                @foreach(['IKD','IKK','IKU'] as $jenis)
                    <option value="{{ $jenis }}" @selected(($filters['jenis'] ?? '') === $jenis)>{{ $jenis }}</option>
                @endforeach
            </select>

            <select name="lingkup" aria-label="Lingkup indikator" class="select sm:w-auto">
                <option value="">Semua lingkup</option>
                <option value="daerah" @selected(($filters['lingkup'] ?? '') === 'daerah')>Daerah</option>
                <option value="opd" @selected(($filters['lingkup'] ?? '') === 'opd')>Per perangkat daerah</option>
            </select>

            <select name="perangkat_daerah" aria-label="Perangkat daerah" class="select sm:max-w-xs">
                <option value="">Semua perangkat daerah</option>
                @foreach($opds as $opd)
                    <option value="{{ $opd }}" @selected(($filters['perangkat_daerah'] ?? '') === $opd)>{{ $opd }}</option>
                @endforeach
            </select>

            <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama indikator…" class="input sm:w-64">

            <button class="btn-primary">Tampilkan</button>
            <a href="{{ route('rpjmd.index') }}" class="btn-secondary">Reset Filter</a>
        </form>
    </x-ui.card>

    <x-ui.card padding="p-0">
        <div class="overflow-x-auto">
            <table class="table-modern min-w-full">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">Jenis / Indikator</th>
                        <th class="whitespace-nowrap">Satuan / Arah</th>
                        <th class="whitespace-nowrap">Baseline 2024</th>
                        @foreach(range(2025,2030) as $year)
                            <th class="whitespace-nowrap">{{ $year }}</th>
                        @endforeach
                        <th class="whitespace-nowrap">Sumber</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-amber-50/30 transition-colors">
                            <td class="min-w-80 font-medium">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="badge-maroon">{{ $item->jenis }}</span>
                                    <span class="font-extrabold text-slate-900">{{ $item->nama }}</span>
                                </div>
                                <div class="text-xs text-slate-500 font-normal">
                                    {{ $item->kelompok }} · <span class="font-semibold text-slate-700">{{ $item->perangkat_daerah ?? 'Kabupaten Nias Barat' }}</span>
                                </div>
                                @if($item->indikator_id)
                                    <a class="inline-flex items-center gap-1 text-xs font-bold text-amber-800 hover:text-amber-950 mt-1 transition" href="{{ route('indikator.kelola', $item->indikator_id) }}">
                                        <span>Kelola capaian daerah</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                @endif
                            </td>
                            <td class="text-xs">
                                <span class="font-bold text-slate-800">{{ $item->satuan }}</span>
                                <span class="block text-slate-400 mt-0.5">{{ $item->arah }}</span>
                            </td>
                            <td class="font-extrabold text-slate-900">
                                {{ $item->baseline_2024 !== null && $item->baseline_2024 !== '' ? $item->baseline_2024 : '—' }}
                            </td>
                            @foreach(range(2025,2030) as $year)
                                <td class="whitespace-nowrap font-bold text-slate-800">
                                    {{ ($item->targets[$year] ?? '') !== '' ? $item->targets[$year] : '—' }}
                                </td>
                            @endforeach
                            <td class="text-xs text-slate-500 max-w-xs">
                                <span class="font-medium text-slate-700">{{ $item->file }}</span>
                                @if($item->baris)<span class="block text-[11px] text-slate-400">Baris {{ $item->baris }}</span>@endif
                                @if($item->keterangan)<span class="block text-[11px] text-slate-400 mt-0.5">{{ $item->keterangan }}</span>@endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-8 text-center text-slate-500 font-medium">
                                Belum ada indikator RPJMD yang sesuai filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $items->links() }}
        </div>
    </x-ui.card>
@endsection
