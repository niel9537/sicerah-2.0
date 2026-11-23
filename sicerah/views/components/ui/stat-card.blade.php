@props(['label', 'value', 'icon' => null, 'color' => 'maroon', 'trend' => null])

@php
    $colors = [
        'maroon'  => 'from-maroon-500 to-maroon-700',
        'blue'    => 'from-sky-500 to-sky-700',
        'green'   => 'from-emerald-500 to-emerald-700',
        'amber'   => 'from-amber-500 to-amber-700',
        'purple'  => 'from-purple-500 to-purple-700',
    ];
    $gradient = $colors[$color] ?? $colors['maroon'];
@endphp

<div {{ $attributes->merge(['class' => 'card p-6 relative overflow-hidden group hover:shadow-soft transition-all']) }}>
    <div class="absolute -right-6 -top-6 w-28 h-28 rounded-full bg-gradient-to-br {{ $gradient }} opacity-10 group-hover:opacity-20 transition"></div>

    <div class="relative">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-500">{{ $label }}</p>
            @if($icon)
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $gradient }} text-white flex items-center justify-center">
                    {!! $icon !!}
                </div>
            @endif
        </div>
        <p class="text-3xl font-bold text-slate-800 tracking-tight">{{ $value }}</p>
        @if($trend)
            <p class="text-xs text-slate-500 mt-2">{{ $trend }}</p>
        @endif
    </div>
</div>