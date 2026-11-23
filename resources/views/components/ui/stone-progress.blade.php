{{--
    <x-ui.stone-progress>
    Meter capaian bergaya "anak tangga batu" — motif visual khas SICERAH yang
    dipakai berulang di landing page, kelola indikator, dan realisasi kinerja.
    Setiap anak tangga = 10% capaian; makin tinggi & makin terisi, makin dekat/lewat target.

    Props:
    - percent : angka capaian (bisa >100)
    - size    : 'sm' | 'md' | 'lg'
    - showLabel : tampilkan angka % di sebelah kanan meter
--}}
@props([
    'percent' => 0,
    'size' => 'md',
    'showLabel' => true,
])

@php
    $pct = is_numeric($percent) ? (float) $percent : 0;
    $filledSteps = (int) min(10, max(0, ceil($pct / 10)));

    $tone = match(true) {
        $pct >= 100 => 'success',
        $pct >= 75  => 'brand',
        $pct >= 50  => 'warning',
        default     => 'danger',
    };

    $fillClasses = [
        'success' => 'bg-emerald-500',
        'brand'   => 'bg-amber-600',
        'warning' => 'bg-amber-500',
        'danger'  => 'bg-orange-400',
    ];
    $labelClasses = [
        'success' => 'text-emerald-700',
        'brand'   => 'text-amber-700',
        'warning' => 'text-amber-700',
        'danger'  => 'text-orange-600',
    ];

    $sizes = [
        'sm' => ['w' => 'w-1.5', 'gap' => 'gap-[2px]', 'base' => 5, 'step' => 1.6, 'text' => 'text-xs'],
        'md' => ['w' => 'w-2',   'gap' => 'gap-[3px]', 'base' => 7, 'step' => 2.4, 'text' => 'text-sm'],
        'lg' => ['w' => 'w-2.5', 'gap' => 'gap-1',      'base' => 9, 'step' => 3.6, 'text' => 'text-base'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <div class="inline-flex items-end {{ $s['gap'] }}" role="img" aria-label="Capaian {{ number_format($pct, 1) }} persen">
        @for ($i = 1; $i <= 10; $i++)
            <span
                class="{{ $s['w'] }} rounded-[2px] {{ $i <= $filledSteps ? $fillClasses[$tone] : 'bg-slate-200' }}"
                style="height: {{ $s['base'] + $i * $s['step'] }}px"
            ></span>
        @endfor
    </div>

    @if($showLabel)
        <span class="{{ $s['text'] }} {{ $labelClasses[$tone] }} font-semibold tabular-nums">
            {{ number_format($pct, 1) }}%
        </span>
    @endif
</div>
