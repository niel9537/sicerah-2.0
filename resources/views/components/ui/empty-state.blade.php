@props(['title' => 'Data belum tersedia', 'description' => null])

<div class="text-center py-12 px-4">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-maroon-50 text-maroon-500 mb-4">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
    </div>
    <h4 class="font-semibold text-slate-700">{{ $title }}</h4>
    @if($description)<p class="text-sm text-slate-500 mt-1">{{ $description }}</p>@endif
    @isset($actions)<div class="mt-4">{{ $actions }}</div>@endisset
</div>