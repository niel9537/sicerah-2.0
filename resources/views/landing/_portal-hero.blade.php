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
    <div class="portal-hero">
        @foreach($slides as $slide)
        <article class="portal-slide" x-show="slide === {{ $loop->index }}" x-transition.opacity.duration.500ms @if(!$loop->first) x-cloak @endif>
            <img class="portal-hero-image" src="{{ $slide['image_url'] }}" alt="{{ $slide['title'] }}" @if($loop->first) fetchpriority="high" @else loading="lazy" @endif>
            <div class="portal-hero-overlay"></div><div class="portal-hero-pattern" aria-hidden="true"></div>
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
    </div>
</section>
