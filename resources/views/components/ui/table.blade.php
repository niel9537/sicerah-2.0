@props(['headers' => []])

<div class="overflow-x-auto -mx-6 sm:mx-0 rounded-2xl border border-slate-200/70 shadow-xs bg-white">
    <table {{ $attributes->merge(['class' => 'table-modern min-w-full']) }}>
        @if(count($headers))
            <thead>
                <tr>
                    @foreach($headers as $h)
                        <th class="whitespace-nowrap">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-slate-100">{{ $slot }}</tbody>
    </table>
</div>
