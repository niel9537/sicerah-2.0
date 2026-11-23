@props(['dark' => false])

<img src="{{ asset($dark ? 'images/logo-sicerah-dark.png' : 'images/logo-sicerah.png') }}"
     alt="SICERAH Nias Barat"
     {{ $attributes->merge(['class' => 'h-10 w-auto object-contain']) }}>
