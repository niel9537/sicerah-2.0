@extends('layouts.app')
@section('title', 'eMonev ProSN Tahun 2026')
@section('content')
@php
    $canEdit = in_array(auth()->user()->role, ['super_admin','admin_bappeda'], true);
    $canValidate = auth()->user()->role === 'inspektorat';
    $total = $indicators->count();
    $sesuai = $indicators->where('status', 'Sudah Sesuai')->count();
    $found = $indicators->where('ketersediaan', 'Ditemukan')->count();
@endphp
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div><p class="text-sm font-semibold text-amber-700">Pelaporan & Kinerja</p><h1 class="text-2xl font-extrabold mt-1">eMonev ProSN Tahun 2026</h1><p class="text-sm text-slate-500 mt-2">Matriks bukti dukung program strategis nasional &middot; {{ $themes->count() }} tema &middot; {{ $all->where('jenis','program')->count() }} program</p></div>
        <a class="btn-secondary" href="{{ route('prosn.export') }}">Cetak / Unduh Excel Matriks</a>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([['Butir pelaporan',$total],['Sudah sesuai',$sesuai],['Belum lapor',$indicators->where('status','Belum Lapor')->count()],['Dokumen ditemukan',$found.' / '.$total]] as [$label,$value])
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-xs text-slate-500">{{ $label }}</p><p class="mt-2 text-3xl font-extrabold">{{ $value }}</p></div>
        @endforeach
    </div>
    <div class="grid lg:grid-cols-2 gap-5">
        <section class="bg-white border border-slate-200 rounded-2xl p-5">
            <h2 class="font-bold">Distribusi status pelaporan</h2><p class="text-xs text-slate-500 mt-1">Seluruh 67 butir tahun 2026, terlepas dari filter matriks.</p>
            <div class="space-y-5 mt-6">
                @foreach(\App\Models\Prosn::STATUS as $status)
                @php($count = $indicators->where('status',$status)->count())
                <div><div class="flex justify-between text-sm mb-2"><span>{{ $status }}</span><strong>{{ $count }} ({{ $total ? round($count/$total*100,1) : 0 }}%)</strong></div>
                <svg viewBox="0 0 100 5" class="w-full" role="img" aria-label="{{ $status }}: {{ $count }} dari {{ $total }} butir"><rect width="100" height="5" rx="2" fill="#f1f5f9"/><rect width="{{ $total ? $count/$total*100 : 0 }}" height="5" rx="2" fill="{{ $status === 'Sudah Sesuai' ? '#059669' : ($status === 'Belum Lapor' ? '#d97706' : '#dc2626') }}"/></svg></div>
                @endforeach
            </div>
        </section>
        <section class="bg-white border border-slate-200 rounded-2xl p-5">
            <h2 class="font-bold">Ketersediaan dokumen per tema</h2><p class="text-xs text-slate-500 mt-1">Jumlah berstatus "Ditemukan" dibandingkan total butir pada setiap tema.</p>
            <div class="space-y-3 mt-4">
                @foreach($themes as $theme)
                @php($group = $indicators->where('tema',$theme->tema))
                @php($n = $group->where('ketersediaan','Ditemukan')->count())
                <div><div class="flex justify-between gap-3 text-xs mb-1"><span>{{ $theme->kode }}. {{ $theme->uraian }}</span><strong class="whitespace-nowrap">{{ $n }}/{{ $group->count() }}</strong></div><svg viewBox="0 0 100 3" class="w-full" role="img" aria-label="{{ $theme->uraian }}: {{ $n }} dari {{ $group->count() }} dokumen ditemukan"><rect width="100" height="3" rx="1" fill="#f1f5f9"/><rect width="{{ $group->count() ? $n/$group->count()*100 : 0 }}" height="3" rx="1" fill="#AD1D30"/></svg></div>
                @endforeach
            </div>
        </section>
    </div>
    <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-900">Status dan ketersediaan awal mengikuti matriks sumber; bukan penilaian capaian numerik. Klik bukti dukung untuk membuka file atau folder Google Drive. Admin dapat melengkapi tautan pada menu Ubah.</div>
    <form method="get" class="bg-white border border-slate-200 rounded-2xl p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48"><label for="q" class="text-xs block mb-1">Cari kode, uraian, indikator atau bukti</label><input id="q" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="200" class="w-full rounded-lg border-slate-300" placeholder="Contoh: A.1.1 atau kemiskinan"></div>
        <div><label for="tema" class="text-xs block mb-1">Tema</label><select id="tema" name="tema" class="rounded-lg border-slate-300 max-w-60"><option value="">Semua tema</option>@foreach($themes as $theme)<option value="{{ $theme->tema }}" @selected(($filters['tema'] ?? '') === $theme->tema)>{{ $theme->kode }}. {{ $theme->uraian }}</option>@endforeach</select></div>
        <div><label for="status" class="text-xs block mb-1">Status</label><select id="status" name="status" class="rounded-lg border-slate-300"><option value="">Semua status</option>@foreach(\App\Models\Prosn::STATUS as $s)<option @selected(($filters['status'] ?? '') === $s)>{{ $s }}</option>@endforeach</select></div>
        <div><label for="dokumen" class="text-xs block mb-1">Dokumen</label><select id="dokumen" name="dokumen" class="rounded-lg border-slate-300"><option value="">Semua dokumen</option>@foreach(\App\Models\Prosn::DOKUMEN as $s)<option @selected(($filters['dokumen'] ?? '') === $s)>{{ $s }}</option>@endforeach</select></div>
        <button class="btn-primary">Tampilkan</button><a href="{{ route('prosn.index') }}" class="btn-secondary">Reset</a>
    </form>
    <div id="matriks" class="bg-white border border-slate-200 rounded-2xl overflow-hidden scroll-mt-24">
        <div class="p-5 border-b border-slate-200"><h2 class="font-bold">Matriks pelaporan dan bukti dukung</h2><p class="text-xs text-slate-500 mt-1">Menampilkan {{ $pagination->firstItem() ?? 0 }}?{{ $pagination->lastItem() ?? 0 }} dari {{ $pagination->total() }} hasil (total {{ $total }} butir), 5 butir per halaman. Geser tabel untuk melihat seluruh kolom.</p></div>
        <div class="overflow-x-auto" tabindex="0" aria-label="Matriks bukti dukung">
            <table class="w-full text-xs leading-relaxed" style="min-width:1450px"><thead class="bg-slate-50 text-left text-xs uppercase"><tr>@foreach(['No','Program / Kegiatan','Status','Indikator Kinerja','Kode','Bukti Dukung','Ketersediaan Dokumen','Catatan','Validasi Inspektorat'] as $h)<th class="p-2">{{ $h }}</th>@endforeach @if($canEdit)<th class="p-2">Kelola</th>@endif</tr></thead>
            <tbody>
            @forelse($rows as $row)
                @if($row->jenis !== 'indikator')
                <tr class="{{ $row->jenis === 'tema' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-800' }}"><th scope="rowgroup" colspan="{{ $canEdit ? 10 : 9 }}" class="p-2 text-left"><div class="flex items-center justify-between gap-4"><span>{{ $row->nomor }} {{ $row->uraian }} @if($row->bukti_1)<span class="font-normal text-xs ml-3">&middot; {{ $row->bukti_1 }}</span>@if(!empty($row->tautan[1]) && preg_match('~^https?://~i',$row->tautan[1])) <a href="{{ $row->tautan[1] }}" target="_blank" rel="noopener noreferrer" class="underline">Buka folder</a>@endif @endif</span>@if($canEdit)<a class="underline text-xs" href="{{ route('prosn.edit',$row) }}">Ubah</a>@endif</div></th></tr>
                @else
                <tr class="border-b border-slate-100 align-top hover:bg-amber-50/30">
                    <td class="p-2">{{ $row->nomor }}</td><td class="p-2" style="min-width:290px;max-width:370px">{{ $row->uraian }}</td>
                    <td class="p-2"><span class="inline-block rounded-lg px-2 py-1 text-xs font-bold {{ $row->status === 'Sudah Sesuai' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $row->status }}</span></td>
                    <td class="p-2" style="min-width:290px;max-width:370px">{{ $row->indikator }}</td><td class="p-2 whitespace-nowrap font-bold">{{ $row->kode }}</td>
                    @foreach([1] as $slot)<td class="p-2" style="min-width:140px">@php($url = $row->tautan[$slot] ?? null) @if($url && preg_match('~^https?://~i',$url))<a class="text-red-700 underline" href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $row->{'bukti_'.$slot} ?: 'Buka dokumen' }}</a>@else{{ $row->{'bukti_'.$slot} ?: '-' }}<span class="block text-xs text-slate-400">Tautan belum diisi</span>@endif</td>@endforeach
                    <td class="p-2">{{ $row->ketersediaan }}</td><td class="p-2 whitespace-pre-line">{{ $row->catatan ?: '-' }}</td>
                    <td class="p-2" style="min-width:200px">
                        <strong>{{ $row->status_validasi }}</strong>
                        @php($v = $row->validasiTerakhir)
                        @if($v && (int) $v->versi === $row->versi)
                            <p class="text-xs mt-1">{{ $v->nama_validator }}<br>{{ $v->validated_at->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') }} WIB</p>
                            <p class="text-xs mt-2 whitespace-pre-line">{{ $v->catatan }}</p>
                        @elseif($v)<p class="text-xs mt-1 text-amber-700">Data berubah setelah validasi sebelumnya.</p>@endif
                        @if($canValidate)<a class="inline-block mt-3 text-red-700 underline" href="{{ route('prosn.validasi',$row) }}">Validasi / Riwayat</a>@endif
                    </td>
                    @if($canEdit)<td class="p-2"><a href="{{ route('prosn.edit',$row) }}" class="text-red-700 underline font-semibold">Ubah</a></td>@endif
                </tr>
                @endif
            @empty<tr><td colspan="{{ $canEdit ? 10 : 9 }}" class="p-10 text-center text-slate-500">Tidak ada butir yang cocok dengan filter.</td></tr>@endforelse
            </tbody></table>
        </div>
        <div class="p-4 border-t border-slate-200">{{ $pagination->onEachSide(1)->links() }}</div>
    </div>
</div>
@endsection
