@extends('layouts.public')
@section('title', $publication->judul)
@section('meta_description', $publication->ringkasan)
@section('meta_image', $publication->cover_url)
@section('content')
<article class="portal-page publication-detail">
    <div class="portal-breadcrumb"><a href="{{ route('landing') }}">Beranda</a><span>/</span><a href="{{ route('publik.publikasi') }}">Publikasi</a><span>/</span>Berita</div>
    <p class="portal-eyebrow">{{ $publication->kategori }}</p><h1>{{ $publication->judul }}</h1>
    <div class="publication-meta"><span>Bappelitbangda Nias Barat</span><span>&middot;</span><time datetime="{{ $publication->published_at->toIso8601String() }}">{{ $publication->published_at->translatedFormat('d F Y, H:i') }} WIB</time></div>
    <img class="publication-detail-cover" src="{{ $publication->cover_url }}" alt="{{ $publication->judul }}">
    <p class="publication-lead">{{ $publication->ringkasan }}</p>
    <div class="publication-content">{{ $publication->konten }}</div>
    <a class="publication-read" href="{{ route('publik.publikasi') }}">&#8592; Kembali ke publikasi</a>
    @if($related->isNotEmpty())<section class="publication-related"><h2>Publikasi Lainnya</h2>@foreach($related as $item)<a href="{{ route('publik.publikasi.show', $item) }}"><span>{{ $item->published_at->translatedFormat('d F Y') }}</span><strong>{{ $item->judul }}</strong><span aria-hidden="true">&#8594;</span></a>@endforeach</section>@endif
</article>
@endsection
