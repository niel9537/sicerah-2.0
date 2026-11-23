@props(['variant' => 'default'])

@php
    $class = "badge-{$variant}";
@endphp

<span {{ $attributes->merge(['class' => $class]) }}>
    {{ $slot }}
</span>