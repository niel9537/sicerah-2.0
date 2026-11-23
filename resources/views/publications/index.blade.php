@extends('layouts.public')
@section('title', 'Publikasi & Berita')
@section('content')
<section class="portal-page publication-page">
    <div class="portal-breadcrumb"><a href="{{ route('landing') }}">Beranda</a><span>/</span>Publikasi</div>
    <div class="publication-heading"><div><p class="portal-eyebrow">KABAR PEMBANGUNAN NIAS BARAT</p><h1>Publikasi & Berita</h1><p class="publication-intro">Informasi kegiatan, perencanaan, dan perkembangan pembangunan Kabupaten Nias Barat.</p></div><span class="publication-count">{{ $publications->total() }} publikasi</span></div>
    <div class="publication-grid">
        @forelse($publications as $publication)
        <article class="publication-card"><a href="{{ route('publik.publikasi.show', $publication) }}" class="publication-cover"><img src="{{ $publication->cover_url }}" alt="{{ $publication->judul }}" loading="lazy"><span>{{ $publication->kategori }}</span></a><div class="publication-card-body"><time datetime="{{ $publication->published_at->toIso8601String() }}">{{ $publication->published_at->translatedFormat('d F Y') }}</time><h2><a href="{{ route('publik.publikasi.show', $publication) }}">{{ $publication->judul }}</a></h2><p>{{ $publication->ringkasan }}</p><a class="publication-read" href="{{ route('publik.publikasi.show', $publication) }}">Baca selengkapnya <span aria-hidden="true">&#8594;</span></a></div></article>
        @empty
        <div class="portal-empty publication-empty"><span class="portal-empty-symbol" aria-hidden="true">&#9671;</span><h2>Belum ada publikasi</h2><p>Berita dan informasi terbaru akan hadir di sini.</p></div>
        @endforelse
    </div>
    <div class="mt-8">{{ $publications->links() }}</div>
</section>
@endsection
