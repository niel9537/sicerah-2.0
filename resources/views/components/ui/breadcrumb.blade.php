@props(['items' => []])

<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center gap-2 text-xs sm:text-sm font-medium">
        <li>
            <a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-amber-800 flex items-center gap-1.5 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Beranda
            </a>
        </li>

        @foreach($items as $label => $url)
            <li class="text-slate-300">/</li>
            <li>
                @if(is_string($url) && $loop->last === false)
                    <a href="{{ $url }}" class="text-slate-500 hover:text-amber-800 transition">{{ $label }}</a>
                @else
                    <span class="text-amber-800 font-bold">{{ is_int($label) ? $url : $label }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
