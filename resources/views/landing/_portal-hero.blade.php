@php
$slides = ($carouselPublications ?? collect())->map(fn ($publication) => [
    'tag' => $publication->kategori.' | '.$publication->published_at->format('d/m/Y'),
    'title' => $publication->judul,
    'accent' => '',
    'description' => \Illuminate\Support\Str::limit($publication->ringkasan, 220),
    'button' => 'Baca Selengkapnya',
    'url' => route('publik.publikasi.show', $publication),
    'image_url' => $publication->cover_url,
])->all();
if (empty($slides)) {
    $slides = [[
        'tag' => 'Portal Data & Kinerja Daerah',
        'title' => 'Pembangunan Terarah,',
        'accent' => 'Nias Barat Semakin Cerah',
        'description' => 'SICERAH mengintegrasikan data perencanaan, kinerja, dan informasi spasial untuk pembangunan Kabupaten Nias Barat yang transparan dan akuntabel.',
        'button' => 'Jelajahi Data',
        'url' => route('indikator.publik'),
        'image_url' => asset('images/bappelitbangda-hero.png'),
    ]];
}
@endphp
<section class="portal-hero-wrap" aria-label="Sorotan SICERAH" aria-roledescription="karusel" x-data="portalCarousel({{ count($slides) }})" @mouseenter="paused = true" @mouseleave="paused = false" @focusin="focused = true" @focusout="focused = $el.contains($event.relatedTarget)" @keydown.arrow-right="slide = (slide + 1) % total" @keydown.arrow-left="slide = (slide + total - 1) % total">
    <div class="portal-hero {{ empty($carouselPublications) || $carouselPublications->isEmpty() ? 'portal-hero-scenic' : '' }}">
        @foreach($slides as $slide)
        <article class="portal-slide" x-show="slide === {{ $loop->index }}" x-transition.opacity.duration.500ms @if(!$loop->first) x-cloak @endif>
            <img class="portal-hero-image" src="{{ $slide['image_url'] }}" alt="{{ $slide['title'] }}" @if($loop->first) fetchpriority="high" @else loading="lazy" @endif>
            <div class="portal-hero-overlay" aria-hidden="true"></div>
            <div class="portal-hero-copy"><span class="portal-hero-tag"><span aria-hidden="true">&#9638;</span> {{ $slide['tag'] }}</span>
                <h1>{{ $slide['title'] }}
                    @if($slide['accent'])
                        <br><span>{{ $slide['accent'] }}</span>
                    @endif
                </h1>
                <p>{{ $slide['description'] }}</p><a class="portal-gold-button" href="{{ $slide['url'] }}">{{ $slide['button'] }} <span aria-hidden="true">&#8594;</span></a>
            </div>
        </article>
        @endforeach
        @if(count($slides) > 1)
        <button class="portal-slide-arrow previous" @click="slide = (slide + total - 1) % total" aria-label="Sorotan sebelumnya"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m15 5-7 7 7 7"/></svg></button>
        <button class="portal-slide-arrow next" @click="slide = (slide + 1) % total" aria-label="Sorotan berikutnya"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m9 5 7 7-7 7"/></svg></button>
        <div class="portal-slide-dots">@foreach($slides as $slide)<button @click="slide = {{ $loop->index }}" :class="{ 'is-active': slide === {{ $loop->index }} }" :aria-pressed="slide === {{ $loop->index }}" aria-label="Tampilkan sorotan {{ $loop->iteration }}"></button>@endforeach</div>
        @endif
        <div class="portal-hero-clouds" aria-hidden="true">
            <svg class="portal-cloud portal-cloud-one" viewBox="0 0 640 240" focusable="false"><path d="M76 208C-4 208-10 111 65 98 58 38 148 15 181 69 213-12 338-6 363 83 421 31 502 65 501 122 599 90 656 199 566 215 439 234 190 229 76 208Z" fill="white"/></svg>
            <svg class="portal-cloud portal-cloud-two" viewBox="0 0 640 240" focusable="false"><path d="M53 210C-9 196 3 125 63 119 42 58 132 27 172 76 202 4 302 8 333 87 387 50 451 73 463 126 539 84 630 123 617 184 607 232 389 226 309 226 208 227 110 226 53 210Z" fill="white"/></svg>
            <svg class="portal-cloud portal-cloud-three" viewBox="0 0 640 240" focusable="false"><path d="M76 208C-4 208-10 111 65 98 58 38 148 15 181 69 213-12 338-6 363 83 421 31 502 65 501 122 599 90 656 199 566 215 439 234 190 229 76 208Z" fill="white"/></svg>
        </div>
        <svg class="portal-hero-curve" viewBox="0 0 1920 120" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path d="M0 0 Q960 220 1920 0 V120 H0 Z" fill="#fff"/></svg>
    </div>
    <div class="portal-service-connections" aria-labelledby="portal-connections-title">
        <div class="portal-connections-heading">
            <span class="portal-connections-line" aria-hidden="true"></span>
            <div><h2 id="portal-connections-title">Data dan Layanan Pemerintahan</h2><p>Mendukung perencanaan pembangunan daerah</p></div>
        </div>
        <div class="portal-connections-links">
            <a class="portal-service-link" href="{{ route('bps.index') }}">
                <div class="portal-service-logo"><img src="{{ asset('images/logo-bps.png') }}" alt="Badan Pusat Statistik" width="150" height="117"></div>
                <div class="portal-service-text"><strong>BPS Kabupaten Nias Barat</strong><span>Integrasi data statistik</span></div>
                <svg class="portal-service-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
            </a>
            <a class="portal-service-link" href="https://sipd-ri.kemendagri.go.id/" target="_blank" rel="noopener noreferrer" aria-label="SIPD Republik Indonesia, akses portal resmi Kemendagri, buka tab baru">
                <div class="portal-service-logo portal-service-logo-sipd"><img src="{{ asset('images/logo-sipd.png') }}" alt="SIPD Republik Indonesia" width="1316" height="506"></div>
                <div class="portal-service-text"><strong>SIPD Republik Indonesia</strong><span>Akses portal resmi Kemendagri</span></div>
                <svg class="portal-service-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M7 17 17 7M7 7h10v10"/></svg>
            </a>
        </div>
    </div>
</section>
