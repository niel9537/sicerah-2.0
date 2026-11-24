@extends('layouts.app')
@section('title', 'CMS RPJMD')
@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-wrap justify-between gap-4"><div><h1 class="text-2xl font-extrabold">Kelola RPJMD</h1><p class="mt-2 text-slate-600">Atur visi dan lima pasangan misi–tujuan untuk portal publik.</p></div><a class="btn-secondary" href="{{ route('publik.rpjmd') }}" target="_blank" rel="noopener">Lihat halaman publik</a></div>
    @if(session('success'))<p role="status" class="rounded-xl bg-green-50 p-4 text-green-800">{{ session('success') }}</p>@endif
    @if($errors->any())<ul role="alert" class="rounded-xl bg-red-50 p-4 text-red-800">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
    <form method="POST" action="{{ route('cms.rpjmd.update') }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="bg-white border border-slate-200 rounded-2xl p-6"><label for="visi" class="block font-bold mb-3">Visi</label><textarea id="visi" name="visi" rows="2" required maxlength="1000" class="w-full rounded-xl border-slate-300">{{ old('visi', $rpjmd['visi']) }}</textarea></div>
        @foreach($rpjmd['pairs'] as $i => $pair)
        <fieldset class="bg-white border border-slate-200 rounded-2xl p-6"><legend class="px-2 font-bold text-red-800">Pasangan {{ $i + 1 }}</legend><div class="grid md:grid-cols-2 gap-5">
            @foreach(['misi' => 'Misi', 'tujuan' => 'Tujuan'] as $field => $label)
            <div><label for="{{ $field }}-{{ $i }}" class="block font-bold mb-3">{{ $label }} {{ $i + 1 }}</label><textarea id="{{ $field }}-{{ $i }}" name="pairs[{{ $i }}][{{ $field }}]" rows="5" required maxlength="2000" class="w-full rounded-xl border-slate-300">{{ old("pairs.$i.$field", $pair[$field]) }}</textarea></div>
            @endforeach
        </div></fieldset>
        @endforeach
        <section class="rounded-2xl bg-white border border-slate-200 p-6 space-y-4">
            <h2 class="text-xl font-bold">Hubungkan indikator dan target</h2>
            <p class="text-sm text-slate-600">Pilih indikator yang sudah ada untuk setiap baris Tabel 3.4. Pencocokan otomatis mengutamakan IKD, sumber daerah dan Bank Data. Pilihan manual menggantikan hasil otomatis. Target resmi mengikuti Bab III Tabel 3.4 PERDA RPJMD 2025–2029. Tautan indikator memakai data yang sudah ada untuk pengisian capaian; target aktif ditampilkan terpisah bila berbeda.</p>
            @foreach($hierarchyRows as $row)
            <div class="border-t border-slate-100 pt-4"><label class="block text-sm font-semibold mb-2" for="link-{{ $row['row'] }}">Misi {{ $row['pair'] + 1 }} · Baris {{ $row['row'] }} — {{ $row['nama'] }}</label>
                <select id="link-{{ $row['row'] }}" name="links[{{ $row['row'] }}]" class="w-full rounded-xl border-slate-300">
                    <option value="">Otomatis berdasarkan nama</option>
                    @foreach($indicators as $indicator)<option value="{{ $indicator->id }}" @selected((string)old("links.{$row['row']}", $rpjmd['links'][$row['row']] ?? '') === (string)$indicator->id)>{{ $indicator->jenis }} · {{ $indicator->kode ?: $indicator->id }} · {{ $indicator->nama }}</option>@endforeach
                </select>
                <p class="mt-2 text-sm text-slate-500">{{ $row['indikator'] ? 'Terhubung: '.$row['indikator']->nama : ($row['ambiguous'] ? 'Ada beberapa kecocokan; pilih indikator yang sesuai.' : 'Belum ada kecocokan otomatis; pilih indikator yang sesuai.') }}</p>
            </div>
            @endforeach
        </section>
        <button type="submit" class="btn-primary">Simpan RPJMD</button>
    </form>
</div>
@endsection
