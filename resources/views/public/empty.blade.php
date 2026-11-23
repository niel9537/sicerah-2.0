@extends('layouts.public')
@section('title', $title)
@section('content')
<section class="portal-page">
    <div class="portal-breadcrumb"><a href="{{ route('landing') }}">Beranda</a><span>/</span>{{ $section }}</div>
    <p class="portal-eyebrow">SICERAH &middot; KABUPATEN NIAS BARAT</p>
    <h1>{{ $title }}</h1>
    <div class="portal-empty" aria-label="Konten {{ $title }}"><span class="portal-empty-symbol" aria-hidden="true">&#9671;</span><h2>Belum ada konten</h2><p>Informasi {{ $title }} akan ditampilkan di halaman ini.</p></div>
</section>
@endsection
