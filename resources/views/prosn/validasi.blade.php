@extends('layouts.app')
@section('title', 'Validasi eMonev ProSN 2026')
@section('content')
<div class="max-w-5xl mx-auto space-y-5">
    <a href="{{ route('prosn.index',['tema'=>$prosn->tema]) }}" class="text-red-700 text-sm">Kembali ke matriks</a>
    <h1 class="text-2xl font-extrabold">Validasi {{ $prosn->kode }}</h1>
    <section class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4">
        <div><h2 class="font-bold">Program / Kegiatan</h2><p class="mt-1">{{ $prosn->uraian }}</p></div>
        <div><h2 class="font-bold">Indikator kinerja</h2><p class="mt-1">{{ $prosn->indikator }}</p></div>
        <p><strong>Status:</strong> {{ $prosn->status }} | <strong>Dokumen:</strong> {{ $prosn->ketersediaan }}</p>
        <div><h2 class="font-bold">Bukti dukung</h2>
            @php($url = $prosn->tautan[1] ?? null)
            @if($url && preg_match('~^https?://~i',$url))<a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="text-red-700 underline">{{ $prosn->bukti_1 ?: 'Buka file / folder Drive' }}</a>
            @else<p>{{ $prosn->bukti_1 ?: '-' }} (tautan belum diisi)</p>@endif
        </div>
        <p class="whitespace-pre-line"><strong>Catatan tindak lanjut:</strong> {{ $prosn->catatan ?: '-' }}</p>
        <p><strong>Validasi saat ini:</strong> {{ $prosn->status_validasi }} (versi data {{ $prosn->versi }})</p>
    </section>
    <form method="post" action="{{ route('prosn.validate',$prosn) }}" class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4">
        @csrf
        <input type="hidden" name="versi" value="{{ $prosn->versi }}">
        <p class="text-sm">Validator: <strong>{{ auth()->user()->nama }}</strong>. Tanggal dan jam WIB dicatat otomatis saat keputusan disimpan.</p>
        <div><label for="keputusan" class="block text-sm font-bold mb-2">Keputusan</label><select id="keputusan" name="keputusan" required class="w-full rounded-lg border-slate-300"><option value="">Pilih keputusan</option><option value="setuju" @selected(old('keputusan') === 'setuju')>Setuju</option><option value="revisi" @selected(old('keputusan') === 'revisi')>Revisi</option></select></div>
        <div><label for="catatan" class="block text-sm font-bold mb-2">Catatan validasi (wajib untuk Revisi)</label><textarea id="catatan" name="catatan" rows="4" maxlength="10000" class="w-full rounded-lg border-slate-300">{{ old('catatan') }}</textarea></div>
        <button class="btn-primary">Simpan keputusan</button>
    </form>
    <section class="bg-white border border-slate-200 rounded-2xl p-6"><h2 class="font-bold mb-4">Riwayat validasi</h2>
        @forelse($prosn->riwayatValidasi as $v)<div class="border-t border-slate-100 py-4"><p><strong>{{ ucfirst($v->keputusan) }}</strong> - versi {{ $v->versi }} - {{ $v->nama_validator }}</p><p class="text-sm text-slate-500">{{ $v->validated_at->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') }} WIB</p><p class="mt-2 whitespace-pre-line">{{ $v->catatan ?: '-' }}</p></div>@empty<p class="text-slate-500">Belum ada validasi.</p>@endforelse
    </section>
</div>
@endsection
