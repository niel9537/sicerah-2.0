@php
    $performance = request()->routeIs('indikator.publik', 'publik.iku-opd');
    $menuGroups = [
        ['label' => 'Struktur Hierarki', 'icon' => 'structure', 'active' => request()->routeIs('publik.struktur'), 'links' => [['Struktur Hierarki', route('publik.struktur')]]],
        ['label' => 'Kinerja', 'icon' => 'chart', 'active' => $performance, 'links' => [['IKD', route('indikator.publik', ['jenis' => 'IKD'])], ['IKU Daerah', route('indikator.publik', ['jenis' => 'IKU'])], ['IKU OPD', route('publik.iku-opd')], ['IKK', route('indikator.publik', ['jenis' => 'IKK'])]]],
        ['label' => 'Spasial', 'icon' => 'map', 'active' => request()->routeIs('webgis.*'), 'links' => [['Peta WebGIS', route('webgis.index')]]],
        ['label' => 'JDIH', 'icon' => 'law', 'active' => request()->routeIs('jdih.*'), 'links' => [['Dokumen Hukum', route('jdih.index')]]],
    ];
@endphp
<header class="portal-header" x-data="{ mobile: false, scrolled: window.scrollY > 40 }" :class="{ 'is-scrolled': scrolled }" @scroll.window.throttle.100ms="scrolled = window.scrollY > 40">
    <div class="portal-masthead">
        <a class="portal-brand" href="{{ route('landing') }}"><img src="{{ asset('images/logo.png') }}" alt="Lambang Kabupaten Nias Barat"><span><strong>BAPPELITBANGDA</strong><b>Kabupaten Nias Barat</b></span></a>
        <div class="portal-identity"><span>Badan Perencanaan Pembangunan,<br>Penelitian dan Pengembangan Daerah</span></div><a class="portal-sicerah logo-shimmer-wrapper" href="{{ route('landing') }}"><img src="{{ asset('images/logo-sicerah.png') }}" alt="SICERAH"></a>
        <form class="portal-search" action="{{ route('indikator.publik') }}" method="get" role="search">
            <label for="portal-search" class="sr-only">Cari indikator</label>
            <input id="portal-search" name="q" type="search" placeholder="Cari informasi..." value="{{ request('q') }}">
            <button type="submit" aria-label="Cari informasi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="m15 15 6 6"/></svg></button>
        </form>
        <a class="portal-login" href="{{ auth()->check() ? route('dashboard') : route('login') }}">{{ auth()->check() ? 'Dashboard' : 'Masuk' }} <span aria-hidden="true">&#8599;</span></a>
        <button class="portal-mobile-toggle" @click="mobile = !mobile" :aria-expanded="mobile" aria-controls="portal-navigation" :aria-label="mobile ? 'Tutup menu navigasi' : 'Buka menu navigasi'"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
    </div>
    <nav id="portal-navigation" class="portal-navigation" :class="{ 'is-open': mobile }" aria-label="Navigasi publik">
        <div class="portal-nav-inner">
            <a class="portal-nav-item {{ request()->routeIs('landing') ? 'is-active' : '' }}" href="{{ route('landing') }}" @if(request()->routeIs('landing')) aria-current="page" @endif>@include('layouts.partials.public-icon', ['icon' => 'home'])<span>Beranda</span></a>
            @foreach($menuGroups as $group)
            <div class="portal-nav-group" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                <button class="portal-nav-item {{ $group['active'] ? 'is-active' : '' }}" @click="open = !open" :aria-expanded="open" aria-controls="portal-submenu-{{ $loop->index }}">@include('layouts.partials.public-icon', ['icon' => $group['icon']])<span>{{ $group['label'] }}</span></button>
                <div id="portal-submenu-{{ $loop->index }}" class="portal-dropdown" x-show="open" x-cloak x-transition>
                    @foreach($group['links'] as [$label, $url])<a href="{{ $url }}"><span class="portal-menu-dot" aria-hidden="true"></span>{{ $label }}</a>@endforeach
                </div>
            </div>
            @endforeach
            <div class="portal-nav-group" x-data="{ open: false, dataOpen: false }" @click.outside="open = false; dataOpen = false" @keydown.escape.window="open = false; dataOpen = false">
                <button type="button" class="portal-nav-item {{ request()->routeIs('publik.publikasi*', 'bps.*', 'publik.rpjmd') ? 'is-active' : '' }}" @click="open = !open; if (!open) dataOpen = false" :aria-expanded="open" aria-controls="portal-publications-menu">@include('layouts.partials.public-icon', ['icon' => 'publication'])<span>Publikasi</span></button>
                <div id="portal-publications-menu" class="portal-dropdown" x-show="open" x-cloak x-transition>
                    <a href="{{ route('publik.publikasi') }}" @if(request()->routeIs('publik.publikasi*')) aria-current="page" @endif><span class="portal-menu-dot" aria-hidden="true"></span>Publikasi &amp; Berita</a>
                    <button type="button" class="portal-dropdown-toggle" @click="dataOpen = !dataOpen" :aria-expanded="dataOpen" aria-controls="portal-publications-data"><span class="portal-menu-dot" aria-hidden="true"></span>Data <span class="portal-chevron" :class="{ 'rotated': dataOpen }" aria-hidden="true">&#8964;</span></button>
                    <div id="portal-publications-data" class="portal-nested-links" x-show="dataOpen" x-cloak x-transition>
                        <a href="{{ route('bps.index') }}" @if(request()->routeIs('bps.*')) aria-current="page" @endif><span class="portal-menu-dot" aria-hidden="true"></span>Data BPS</a>
                        <a href="{{ route('publik.rpjmd') }}" @if(request()->routeIs('publik.rpjmd')) aria-current="page" @endif><span class="portal-menu-dot" aria-hidden="true"></span>RPJMD</a>
                    </div>
                </div>
            </div>
            <a class="portal-nav-item portal-mobile-account" href="{{ auth()->check() ? route('dashboard') : route('login') }}">{{ auth()->check() ? 'Dashboard' : 'Masuk' }}</a>
            @include('layouts.partials.weather')
        </div>
    </nav>
    <div class="portal-motivation" role="region" aria-label="Motivasi perencanaan pembangunan">
        <p class="sr-only">Perencanaan yang baik adalah langkah awal perubahan. Data yang akurat, arah yang tepat, pembangunan yang berdampak. Bersama merencanakan, bersama mewujudkan Nias Barat CERAH: Cerdas, Sejahtera, dan Sehat.</p>
        <div class="portal-motivation-track" aria-hidden="true">
            @for($copy = 0; $copy < 2; $copy++)
            <div class="portal-motivation-copy">
                <span>Perencanaan yang baik adalah langkah awal perubahan.</span><b>&#10022;</b>
                <span>Data yang akurat, arah yang tepat, pembangunan yang berdampak.</span><b>&#10022;</b>
                <span>Bersama merencanakan, bersama mewujudkan Nias Barat CERAH: Cerdas, Sejahtera, dan Sehat.</span><b>&#10022;</b>
            </div>
            @endfor
        </div>
    </div>
</header>
