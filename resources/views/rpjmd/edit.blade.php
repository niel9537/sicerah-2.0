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
        <button type="submit" class="btn-primary">Simpan RPJMD</button>
    </form>
</div>
@endsection
