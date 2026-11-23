@extends('layouts.public')
@section('title', 'Data BPS Nias Barat')
@section('meta_description', 'Tabel statistik, publikasi, dan indikator strategis dari BPS Kabupaten Nias Barat dalam portal SICERAH.')
@section('content')
<section class="portal-page bps-page">
    <div class="portal-breadcrumb"><a href="{{ route('landing') }}">Beranda</a><span>/</span>Data BPS</div>
    <div class="bps-heading"><div><p class="portal-eyebrow">STATISTIK RESMI DAERAH</p><h1>Data BPS Nias Barat</h1><p>Jelajahi data statistik dan publikasi Badan Pusat Statistik Kabupaten Nias Barat.</p></div><a class="bps-source" href="https://niasbaratkab.bps.go.id" target="_blank" rel="noopener noreferrer">BPS Nias Barat &#8599;</a></div>
    <div class="bps-region"><span>Kabupaten Nias Barat</span><span>Kode wilayah: {{ config('services.bps.domain') }}</span><span>Sumber: Badan Pusat Statistik</span></div>
    <nav class="bps-tabs" aria-label="Kategori data BPS">@foreach(\App\Services\BpsService::SECTIONS as $key => $label)<a class="{{ $category === $key ? 'is-active' : '' }}" href="{{ route('bps.index', ['kategori' => $key]) }}" @if($category === $key) aria-current="page" @endif>{{ $label }}</a>@endforeach</nav>
    @if($category !== 'indicators')
    <form class="bps-filters" action="{{ route('bps.index') }}" method="get"><input name="kategori" type="hidden" value="{{ $category }}"><div><label for="bps-q">Cari judul</label><input id="bps-q" name="q" maxlength="150" value="{{ $keyword }}" placeholder="Contoh: penduduk, kemiskinan, dalam angka"></div><div><label for="bps-year">Tahun</label><input id="bps-year" type="number" name="tahun" min="1900" max="{{ now()->year + 1 }}" value="{{ $year }}" placeholder="Semua tahun"></div><button type="submit" class="btn-primary">Cari Data</button><a class="bps-reset" href="{{ route('bps.index', ['kategori' => $category]) }}">Reset</a></form>
    @endif
    @if($error)
        <div class="bps-state" role="alert"><h2>Data BPS belum dapat dimuat</h2><p>{{ $error }}</p><a class="btn-primary" href="{{ request()->fullUrl() }}">Coba Lagi</a></div>
    @else
        <div class="bps-result-meta"><p>{{ number_format($result['total'], 0, ',', '.') }} {{ $category === 'publication' ? 'publikasi' : ($category === 'indicators' ? 'indikator' : 'tabel') }} tersedia</p><span>Diambil dari BPS: {{ \Carbon\Carbon::parse($result['retrieved_at'])->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</span></div>
        <div class="bps-grid {{ $category === 'publication' ? 'bps-publications' : '' }}">
        @forelse($result['items'] as $item)
            <article class="bps-card">
                @if($category === 'publication' && $item['cover'])<img class="bps-cover" src="{{ $item['cover'] }}" alt="Sampul {{ $item['title'] }}" loading="lazy" referrerpolicy="no-referrer">@endif
                <div class="bps-card-content">
                    <span class="bps-category">{{ $item['subject'] ?: \App\Services\BpsService::SECTIONS[$category] }}</span>
                    <h2>@if($category === 'statictable' && $item['id'])<a href="{{ route('bps.table', $item['id']) }}">{{ $item['title'] }}</a>@else{{ $item['title'] }}@endif</h2>
                    @if($item['date'])<p class="bps-date">{{ $category === 'publication' ? 'Terbit' : 'Diperbarui' }}: {{ $item['date'] }}</p>@endif
                    @if($category === 'indicators')<p class="bps-value">{{ $item['value'] !== '' ? $item['value'] : '-' }} <small>{{ $item['unit'] }}</small></p>@endif
                    @if($item['description'])<p class="bps-description">{{ $item['description'] }}</p>@endif
                    @if($item['source'])<p class="bps-date">Sumber data: {{ $item['source'] }}</p>@endif
                    <div class="bps-actions">@if($category === 'statictable' && $item['id'])<a href="{{ route('bps.table', $item['id']) }}">Lihat Tabel &#8594;</a>@endif @if($item['download'])<a href="{{ $item['download'] }}" target="_blank" rel="noopener noreferrer">Unduh {{ $category === 'publication' ? 'PDF' : 'Excel' }} &#8599;</a>@endif @if($item['size'])<span>{{ $item['size'] }}</span>@endif</div>
                </div>
            </article>
        @empty
            <div class="bps-state"><h2>{{ $category === 'indicators' ? 'Indikator strategis belum tersedia' : 'Tidak ada hasil' }}</h2><p>{{ $category === 'indicators' ? 'BPS belum menyediakan indikator strategis melalui layanan ini untuk Kabupaten Nias Barat. Silakan telusuri Tabel Statistik atau Publikasi BPS.' : 'Coba kata kunci atau tahun lain untuk menemukan data BPS.' }}</p></div>
        @endforelse
        </div>
        @if($result['pages'] > 1)<nav class="bps-pagination" aria-label="Halaman hasil BPS">@if($result['page'] > 1)<a href="{{ route('bps.index', array_filter(['kategori' => $category, 'q' => $keyword, 'tahun' => $year, 'page' => $result['page'] - 1])) }}">&#8592; Sebelumnya</a>@endif<span>Halaman {{ $result['page'] }} dari {{ $result['pages'] }}</span>@if($result['page'] < $result['pages'])<a href="{{ route('bps.index', array_filter(['kategori' => $category, 'q' => $keyword, 'tahun' => $year, 'page' => $result['page'] + 1])) }}">Berikutnya &#8594;</a>@endif</nav>@endif
    @endif
    <p class="bps-footnote">Data ditampilkan sesuai ketersediaan layanan BPS. Tahun data dapat berbeda antar tabel. Pembaruan diambil otomatis dengan cache satu jam.</p>
</section>
@endsection
