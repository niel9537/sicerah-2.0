@extends('layouts.public')
@section('title', $indikator->nama . ' — RPJMD')
@section('content')
<section class="max-w-6xl mx-auto px-4 py-10">
    <a href="{{ route('publik.rpjmd') }}" class="text-red-800 underline">Kembali ke hierarki RPJMD</a>
    <h1 class="text-2xl sm:text-3xl font-extrabold mt-5">{{ $indikator->nama }}</h1>
    <p class="mt-3 text-slate-600">{{ $indikator->jenis }} · {{ $indikator->satuan }} · Kode {{ $indikator->kode ?: $indikator->id }}</p>
    <div class="mt-6 rounded-2xl border border-slate-200 bg-white overflow-x-auto">
        <table class="w-full text-sm text-left"><thead class="bg-slate-100"><tr><th class="p-4">Sumber target</th>@foreach(range(2025,2030) as $year)<th class="p-4">{{ $year }}</th>@endforeach</tr></thead><tbody>
            <tr class="border-t border-slate-200"><th class="p-4">Tabel indikator aktif</th>@foreach(range(2025,2030) as $year)<td class="p-4 whitespace-nowrap">{{ $indikator->targets->firstWhere('tahun', $year)?->target ?? '—' }}</td>@endforeach</tr>
            @foreach($sources as $source)
            <tr class="border-t border-slate-200"><th class="p-4 min-w-64 font-medium">{{ $source->file }} · Baris {{ $source->baris }}<span class="block mt-1 text-slate-500">{{ $source->perangkat_daerah ?: 'Daerah' }}</span></th>@foreach(range(2025,2030) as $year)<td class="p-4 whitespace-nowrap">{{ $source->targets[$year] ?? '—' }}</td>@endforeach</tr>
            @endforeach
        </tbody></table>
    </div>
    <p class="mt-4 text-sm text-slate-500">Data terhubung langsung ke tabel indikator dan sumber RPJMD yang sudah tersedia.</p>
</section>
@endsection
