@props(['label', 'value', 'icon' => null, 'color' => 'maroon', 'trend' => null])

@php
    $themeMap = [
        'maroon' => [
            'iconBg' => 'bg-amber-50 text-amber-700 border border-amber-200/80',
            'glow'   => 'from-amber-500/10 to-transparent',
            'val'    => 'text-slate-900',
        ],
        'blue' => [
            'iconBg' => 'bg-red-50 text-red-600 border border-red-100/80',
            'glow'   => 'from-red-500/10 to-transparent',
            'val'    => 'text-slate-900',
        ],
        'green' => [
            'iconBg' => 'bg-amber-50 text-amber-600 border border-amber-100/80',
            'glow'   => 'from-amber-500/10 to-transparent',
            'val'    => 'text-slate-900',
        ],
        'amber' => [
            'iconBg' => 'bg-amber-50 text-amber-600 border border-amber-100/80',
            'glow'   => 'from-amber-500/10 to-transparent',
            'val'    => 'text-slate-900',
        ],
        'purple' => [
            'iconBg' => 'bg-red-50 text-red-600 border border-red-100/80',
            'glow'   => 'from-red-500/10 to-transparent',
            'val'    => 'text-slate-900',
        ],
    ];
    $theme = $themeMap[$color] ?? $themeMap['maroon'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-slate-100/90 shadow-card hover:shadow-card-hover hover:-translate-y-0.5 transition-all duration-200 relative overflow-hidden group']) }}>
    <div class="absolute -right-8 -top-8 w-28 h-28 rounded-full bg-gradient-to-br {{ $theme['glow'] }} pointer-events-none group-hover:scale-125 transition-transform duration-300"></div>

    <div class="relative">
        <div class="flex items-center justify-between gap-3 mb-3">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 truncate">{{ $label }}</p>
            @if($icon)
                <div class="w-10 h-10 rounded-2xl {{ $theme['iconBg'] }} flex items-center justify-center flex-shrink-0 shadow-xs transition-transform group-hover:scale-105">
                    {!! $icon !!}
                </div>
            @endif
        </div>
        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $value }}</p>
        @if($trend)
            <div class="mt-2.5 flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                {{ $trend }}
            </div>
        @endif
    </div>
</div>
