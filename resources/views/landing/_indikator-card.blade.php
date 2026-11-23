{{--
    Partial kartu capaian satu indikator (IKU/IKK/IKD).
    Dipakai dari landing.blade.php — menerima $c (hasil capaianIndikator()).
--}}
@php
    $ind = $c['indikator'];
    $tone = ($ind->jenis ?? '') === 'IKU' ? 'badge-maroon' : 'badge-info';
@endphp

<div class="reveal card p-5 sm:p-6 hover:shadow-soft transition-all flex flex-col">
    <div class="flex items-start justify-between gap-3 mb-3">
        <span class="{{ $tone }}">{{ $ind->jenis }}</span>
        <x-ui.updated-badge :at="$c['updated_at']" variant="text" />
    </div>

    <h3 class="font-semibold text-slate-800 text-sm leading-snug mb-1 flex-1">{{ $ind->nama }}</h3>
    <p class="text-xs text-slate-500 mb-4">{{ $ind->opd?->nama ?? 'Lintas OPD' }} · Tahun {{ $c['tahun'] }}</p>

    @if($c['capaian'] !== null)
        <x-ui.stone-progress :percent="$c['capaian']" size="md" class="mb-3" />
        <div class="grid grid-cols-2 gap-2 text-xs text-slate-500 pt-3 border-t border-slate-100">
            <div>
                <span class="block text-slate-400">Target</span>
                <span class="font-medium text-slate-700 tabular-nums">{{ number_format($c['target'], 2, ',', '.') }} {{ $ind->satuan }}</span>
            </div>
            <div>
                <span class="block text-slate-400">Realisasi</span>
                <span class="font-medium text-slate-700 tabular-nums">{{ number_format($c['realisasi'], 2, ',', '.') }} {{ $ind->satuan }}</span>
            </div>
        </div>
    @elseif($c['realisasi_raw'])
        <p class="text-sm text-slate-600 pt-3 border-t border-slate-100">
            Realisasi tercatat: <span class="font-medium">{{ $c['realisasi_raw'] }} {{ $ind->satuan }}</span>
            <span class="block text-xs text-slate-400 mt-0.5">Target belum diatur untuk tahun ini</span>
        </p>
    @else
        <p class="text-xs text-slate-400 pt-3 border-t border-slate-100">Target & realisasi belum diinput untuk tahun ini.</p>
    @endif
</div>
