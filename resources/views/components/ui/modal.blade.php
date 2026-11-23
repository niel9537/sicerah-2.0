@props(['id', 'title' => 'Modal', 'size' => 'lg'])

@php
    $sizes = [
        'sm' => 'max-w-md',
        'md' => 'max-w-lg',
        'lg' => 'max-w-3xl',
        'xl' => 'max-w-5xl',
        '2xl'=> 'max-w-7xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['lg'];
@endphp

<div x-data="{ open: false }"
     x-on:open-modal-{{ $id }}.window="open = true"
     x-on:close-modal-{{ $id }}.window="open = false"
     x-on:keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <div x-show="open" x-transition.opacity
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-md"
         @click="open = false"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full {{ $sizeClass }} bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden">

            <div class="flex items-center justify-between px-6 py-4.5 border-b border-slate-100 bg-amber-50/50">
                <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">{{ $title }}</h3>
                <button type="button" @click="open = false"
                        class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-slate-700 hover:bg-slate-50 flex items-center justify-center transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 sm:p-7 max-h-[72vh] overflow-y-auto">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/70 flex items-center justify-end gap-2.5">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
