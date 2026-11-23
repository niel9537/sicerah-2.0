@extends('layouts.public')
@section('title', 'RPJMD — Visi, Misi dan Tujuan')
@section('content')
<section class="max-w-6xl mx-auto px-4 py-10 sm:py-14">
    <p class="text-sm font-bold uppercase tracking-widest text-red-800">Arah pembangunan daerah</p>
    <h1 class="mt-2 mb-8 text-3xl font-extrabold">RPJMD Kabupaten Nias Barat</h1>
    @include('rpjmd._hierarchy')
</section>
@endsection
