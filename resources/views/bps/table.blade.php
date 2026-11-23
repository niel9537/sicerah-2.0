@extends('layouts.public')
@section('title', $table['title'] ?? 'Tabel BPS')
@section('content')
<section class="portal-page bps-page">
    <div class="portal-breadcrumb"><a href="{{ route('landing') }}">Beranda</a><span>/</span><a href="{{ route('bps.index') }}">Data BPS</a><span>/</span>Tabel Statistik</div>
    @if($error)<div class="bps-state" role="alert"><h1>Tabel belum dapat dimuat</h1><p>{{ $error }}</p><a class="btn-primary" href="{{ request()->url() }}">Coba Lagi</a></div>
    @else
    <p class="portal-eyebrow">BPS KABUPATEN NIAS BARAT</p><h1>{{ $table['title'] }}</h1>
    <div class="bps-result-meta"><span>{{ $table['date'] ? 'Diperbarui BPS: '.$table['date'] : 'Sumber: BPS Kabupaten Nias Barat' }}</span>@if($table['download'])<a class="btn-primary" href="{{ $table['download'] }}" target="_blank" rel="noopener noreferrer">Unduh Excel &#8599;</a>@endif</div>
    @if($table['rows'])<div class="bps-table-wrap" role="region" aria-label="Tabel {{ $table['title'] }}" tabindex="0"><table class="bps-data-table"><caption class="sr-only">{{ $table['title'] }}</caption><tbody>@foreach($table['rows'] as $row)<tr>@foreach($row as $cell)@if($cell['header'])<th colspan="{{ $cell['colspan'] }}" rowspan="{{ $cell['rowspan'] }}">{{ $cell['text'] }}</th>@else<td colspan="{{ $cell['colspan'] }}" rowspan="{{ $cell['rowspan'] }}">{{ $cell['text'] }}</td>@endif @endforeach</tr>@endforeach</tbody></table></div>
    @else<div class="bps-state"><h2>Pratinjau tabel belum tersedia</h2><p>Gunakan unduhan Excel untuk melihat data lengkap dari BPS.</p></div>@endif
    <p class="bps-footnote">Sumber: Badan Pusat Statistik Kabupaten Nias Barat &middot; Diambil {{ \Carbon\Carbon::parse($table['retrieved_at'])->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</p>
    @endif
    <a class="publication-read" href="{{ route('bps.index') }}">&#8592; Kembali ke Data BPS</a>
</section>
@endsection
