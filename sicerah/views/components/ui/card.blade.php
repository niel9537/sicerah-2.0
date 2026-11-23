@props(['title' => null, 'subtitle' => null, 'icon' => null, 'padding' => 'p-6'])

<div {{ $attributes->merge(['class' => 'card overflow-hidden']) }}>
    @if($title || $icon)
        <div class="flex items-start justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-start gap-3">
                @if($icon)
                    <div class="w-10 h-10 rounded-xl bg-brand-gradient text-white flex items-center justify-center shadow-soft">
                        {!! $icon !!}
                    </div>
                @endif
                <div>
                    @if($title)   <h3 class="font-semibold text-slate-800">{{ $title }}</h3> @endif
                    @if($subtitle)<p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p> @endif
                </div>
            </div>
            @isset($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>