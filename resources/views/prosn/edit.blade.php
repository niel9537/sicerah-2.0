@extends('layouts.app')
@section('title', 'Ubah eMonev ProSN 2026')
@section('content')
<div class="max-w-4xl mx-auto space-y-5">
    <a href="{{ route('prosn.index', ['tema'=>$prosn->tema]) }}" class="text-red-700 text-sm">Kembali ke matriks</a>
    <h1 class="text-2xl font-extrabold">Ubah {{ $prosn->kode ?: $prosn->nomor }} &middot; ProSN 2026</h1>
    <form action="{{ route('prosn.update',$prosn) }}" method="post" class="bg-white border border-slate-200 rounded-2xl p-6 space-y-5">
        @csrf @method('PUT')
        <div><label for="uraian" class="block font-semibold text-sm mb-2">Program / Kegiatan</label><textarea id="uraian" name="uraian" rows="5" required maxlength="15000" class="w-full rounded-lg border-slate-300">{{ old('uraian',$prosn->uraian) }}</textarea></div>
        @if($prosn->jenis === 'indikator')
        <div><label for="indikator" class="block font-semibold text-sm mb-2">Indikator kinerja</label><textarea id="indikator" name="indikator" rows="5" required maxlength="15000" class="w-full rounded-lg border-slate-300">{{ old('indikator',$prosn->indikator) }}</textarea></div>
        <div class="grid sm:grid-cols-2 gap-4">@foreach(['status'=>\App\Models\Prosn::STATUS,'ketersediaan'=>\App\Models\Prosn::DOKUMEN] as $field=>$options)<div><label for="{{ $field }}" class="block font-semibold text-sm mb-2">{{ $field === 'status' ? 'Status pelaporan' : 'Ketersediaan dokumen' }}</label><select id="{{ $field }}" name="{{ $field }}" class="w-full rounded-lg border-slate-300" required>@foreach($options as $option)<option @selected(old($field,$prosn->$field) === $option)>{{ $option }}</option>@endforeach</select></div>@endforeach</div>
        @endif
        <p class="text-xs text-slate-500">Isi nama bukti dukung dan tautan langsung ke file atau folder Google Drive. Perubahan data akan memerlukan validasi Inspektorat kembali.</p>
        @foreach([1] as $slot)<div class="grid sm:grid-cols-2 gap-4"><div><label for="bukti_{{ $slot }}" class="block text-sm mb-2">Bukti dukung</label><input id="bukti_{{ $slot }}" name="bukti_{{ $slot }}" value="{{ old('bukti_'.$slot,$prosn->{'bukti_'.$slot}) }}" maxlength="2000" class="w-full rounded-lg border-slate-300"></div><div><label for="tautan_{{ $slot }}" class="block text-sm mb-2">Tautan file / folder Google Drive</label><input id="tautan_{{ $slot }}" type="url" name="tautan[{{ $slot }}]" value="{{ old('tautan.'.$slot,$prosn->tautan[$slot] ?? '') }}" maxlength="2000" placeholder="https://" class="w-full rounded-lg border-slate-300"></div></div>@endforeach
        <div><label for="catatan" class="block text-sm font-semibold mb-2">Catatan tindak lanjut</label><textarea id="catatan" name="catatan" rows="3" maxlength="10000" class="w-full rounded-lg border-slate-300">{{ old('catatan',$prosn->catatan) }}</textarea></div>
        <button class="btn-primary">Simpan perubahan</button>
    </form>
</div>
@endsection
