@props(['title' => null, 'subtitle' => null, 'icon' => null, 'padding' => 'p-6 sm:p-7'])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl sm:rounded-3xl shadow-card border border-slate-100/90 overflow-hidden transition-all duration-200']) }}>
    @if($title || $icon)
        <div class="flex items-center justify-between px-6 sm:px-7 py-4.5 border-b border-slate-100/90 bg-white">
            <div class="flex items-center gap-3.5 min-w-0">
                @if($icon)
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-red-700 via-rose-600 to-red-500 text-white flex items-center justify-center shadow-soft flex-shrink-0">
                        {!! $icon !!}
                    </div>
                @endif
                <div class="min-w-0">
                    @if($title)   <h3 class="font-extrabold text-slate-900 text-base tracking-tight truncate">{{ $title }}</h3> @endif
                    @if($subtitle)<p class="text-xs text-slate-500 mt-0.5 font-medium truncate">{{ $subtitle }}</p> @endif
                </div>
            </div>
            @isset($actions)
                <div class="flex items-center gap-2 flex-shrink-0">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>
