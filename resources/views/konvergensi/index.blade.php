@extends('layouts.public')
@section('title', 'Konvergensi Stunting Nias Barat')
@section('meta_description', 'Progres konvergensi penurunan stunting Kabupaten Nias Barat dan tabel rincian kecamatan dari e-Monev Ditjen Bina Bangda Kemendagri.')
@section('content')
<section class="portal-page">
    <div class="portal-breadcrumb"><a href="{{ route('landing') }}">Beranda</a><span>/</span><a href="{{ route('publik.publikasi') }}">Publikasi</a><span>/</span>Konvergensi Stunting</div>
    <div class="bps-heading"><div><p class="portal-eyebrow">E-MONEV KONVERGENSI KEMENDAGRI</p><h1>Konvergensi Stunting Nias Barat</h1><p>Progres pengisian data dan pelaksanaan aksi konvergensi penurunan stunting.</p></div><a class="bps-source" href="{{ \App\Services\KonvergensiService::BASE.'/dashboard/'.\App\Services\KonvergensiService::PERIODS[$year] }}" target="_blank" rel="noopener noreferrer">Dashboard resmi Kemendagri &#8599;</a></div>
    <div class="bps-region"><span>Kabupaten Nias Barat</span><span>Kode wilayah: 1225</span><span>Tahun {{ $year }}</span></div>
    <form class="bps-filters" method="get" action="{{ route('konvergensi.index') }}"><div><label for="konvergensi-year">Tahun data</label><select id="konvergensi-year" name="tahun">@foreach(\App\Services\KonvergensiService::PERIODS as $availableYear => $period)<option value="{{ $availableYear }}" @selected($year === $availableYear)>{{ $availableYear }}</option>@endforeach</select></div><button class="btn-primary" type="submit">Tampilkan Data</button></form>
    @if($error)
        <div class="bps-state" role="alert"><h2>Data konvergensi belum dapat dimuat</h2><p>{{ $error }}</p><a class="btn-primary" href="{{ route('konvergensi.index', ['tahun' => $year]) }}">Coba Lagi</a></div>
    @else
        @if($data['is_fallback'] ?? false)
        <div class="konvergensi-notice" role="status"><strong>Menampilkan data cadangan terakhir.</strong> Koneksi langsung ke Kemendagri belum berhasil. Tabel berasal dari pengambilan resmi pada {{ \Carbon\Carbon::parse($data['retrieved_at'])->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB. Data terbaru akan dicoba kembali saat halaman dibuka.</div>
        @endif
        <div class="bps-result-meta"><p>Data Kabupaten Nias Barat dan {{ count($data['subdistricts']) }} kecamatan</p><span>Diambil dari sumber: {{ \Carbon\Carbon::parse($data['retrieved_at'])->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</span></div>
        <div class="bps-grid">
            <article class="bps-card"><div class="bps-card-content"><span class="bps-category">PROGRES KABUPATEN</span><h2>Kabupaten Nias Barat</h2><p class="bps-value">{{ $data['district']['cells'][22] !== '' ? $data['district']['cells'][22].'%' : 'Belum tersedia' }}</p><p class="bps-description">Progres aksi konvergensi menurut e-Monev Kemendagri.</p></div></article>
            <article class="bps-card"><div class="bps-card-content"><span class="bps-category">CAKUPAN WILAYAH</span><h2>Jumlah Desa</h2><p class="bps-value">{{ $data['district']['cells'][1] !== '' ? $data['district']['cells'][1] : 'Belum tersedia' }}</p><p class="bps-description">Jumlah desa yang tercatat pada dashboard sumber.</p></div></article>
        </div>
        <p class="bps-footnote">Persentase progres menunjukkan pelaporan aksi konvergensi, bukan prevalensi stunting. Tanda — berarti sel kosong pada sumber; nilai 0 ditampilkan sebagai 0. Pembaruan otomatis menggunakan cache satu jam. Waktu di atas adalah waktu pengambilan, bukan tanggal pembaruan data oleh Kemendagri.</p>
        <section class="konvergensi-section" aria-labelledby="konvergensi-chart-title"><h2 id="konvergensi-chart-title">Progres per Kecamatan</h2>
            <div class="konvergensi-chart">@forelse($data['subdistricts'] as $row)<div class="konvergensi-bar-row"><span>{{ $row['cells'][0] }}</span><div class="konvergensi-bar-track"><div style="width: {{ $row['progress'] ?? 0 }}%"></div></div><strong>{{ $row['cells'][18] !== '' ? $row['cells'][18].'%' : '—' }}</strong></div>@empty<p>Rincian kecamatan belum tersedia pada sumber untuk tahun ini.</p>@endforelse</div>
        </section>
        @foreach([
            ['title' => 'Tabel Progres Kabupaten', 'columns' => \App\Services\KonvergensiService::DISTRICT_COLUMNS, 'rows' => [$data['district']]],
            ['title' => 'Tabel Rincian Kecamatan', 'columns' => \App\Services\KonvergensiService::SUBDISTRICT_COLUMNS, 'rows' => $data['subdistricts']],
        ] as $table)
        <section class="konvergensi-section"><h2>{{ $table['title'] }}</h2><p class="bps-footnote">Geser tabel ke samping untuk melihat seluruh kolom.</p><div class="bps-table-wrap" role="region" aria-label="{{ $table['title'] }}" tabindex="0"><table class="bps-data-table"><caption class="sr-only">{{ $table['title'] }} Kabupaten Nias Barat tahun {{ $year }}</caption><thead><tr>@foreach($table['columns'] as $column)<th scope="col">{{ $column }}</th>@endforeach</tr></thead><tbody>@forelse($table['rows'] as $row)<tr>@foreach($row['cells'] as $cell)@if($loop->first)<th scope="row">{{ $cell !== '' ? $cell : '—' }}</th>@else<td>{{ $cell !== '' ? $cell : '—' }}</td>@endif
@endforeach</tr>@empty<tr><td colspan="{{ count($table['columns']) }}">Data belum tersedia pada sumber.</td></tr>@endforelse</tbody></table></div></section>
        @endforeach
    @endif
    <p class="bps-footnote">Sumber: <a href="{{ \App\Services\KonvergensiService::BASE.'/dashboard/'.\App\Services\KonvergensiService::PERIODS[$year] }}" target="_blank" rel="noopener noreferrer">e-Monev Konvergensi, Ditjen Bina Pembangunan Daerah, Kementerian Dalam Negeri</a>. Tampilan ini hanya memuat Kabupaten Nias Barat.</p>
</section>
@endsection
