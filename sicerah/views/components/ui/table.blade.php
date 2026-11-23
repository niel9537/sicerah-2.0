@props(['headers' => []])

<div class="overflow-x-auto -mx-6 sm:mx-0 sm:rounded-xl border border-slate-100">
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
        <tbody>{{ $slot }}</tbody>
    </table>
</div>