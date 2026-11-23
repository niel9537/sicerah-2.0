@extends('layouts.app')

@section('title', 'Impor Evaluasi Renja')
@section('subtitle', 'Unggah realisasi fisik & keuangan banyak sub kegiatan sekaligus')

@section('content')
    <x-ui.breadcrumb :items="['Evaluasi Renja' => route('evaluasi.index'), 'Impor Excel' => '']" />

    @if(session('import_hasil'))
        @php $hasil = session('import_hasil'); @endphp
        <x-ui.card class="mb-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-800">{{ $hasil['berhasil'] }} baris berhasil diimpor sebagai draft</p>

                    @if(count($hasil['dilewati']))
                        <p class="text-sm text-amber-700 mt-1">{{ count($hasil['dilewati']) }} baris perlu dicek:</p>
                        <ul class="text-xs text-slate-500 mt-2 space-y-1 max-h-56 overflow-y-auto pr-2">
                            @foreach($hasil['dilewati'] as $pesan)
                                <li class="flex gap-1.5"><span class="text-amber-500">•</span><span>{{ $pesan }}</span></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </x-ui.card>
    @endif

    <x-ui.alert type="info" :dismissible="false" class="mb-4">
        Data yang diimpor selalu masuk sebagai <strong>draft</strong> — sama seperti input manual,
        tetap perlu diajukan & diverifikasi lewat alur biasa. Baris yang sub kegiatannya sudah
        berstatus <em>diajukan</em>/<em>diverifikasi</em> tidak akan ditimpa (dilewati & dilaporkan),
        dan setiap baris yang tersimpan otomatis ikut memperbarui Dasbor Kinerja & halaman
        Realisasi Kinerja seperti biasa.
    </x-ui.alert>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-ui.card title="1. Unduh Template" subtitle="Terisi seluruh sub kegiatan × 4 triwulan untuk tahun terpilih">
            <form method="GET" action="{{ route('evaluasi.import.template') }}" class="space-y-3">
                <div>
                    <label class="text-xs text-slate-500 block mb-1">Tahun</label>
                    <select name="tahun" class="select w-full">
                        @foreach(range(now()->year, now()->year - 4) as $y)
                            <option value="{{ $y }}" @selected($y == $tahun)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-secondary w-full justify-center inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                    Unduh Template (.xlsx)
                </button>
            </form>
        </x-ui.card>

        <x-ui.card title="2. Unggah File Terisi" subtitle="Format .xlsx, .xls, atau .csv, maksimal 10MB">
            <form method="POST" action="{{ route('evaluasi.import.store') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="input w-full">
                @error('file')<p class="text-xs text-amber-700">{{ $message }}</p>@enderror

                <p class="text-xs text-slate-400">
                    Sub kegiatan harus sudah punya Target Tahunan untuk tahun yang diisi — kalau
                    belum, baris itu dilewati dan diberi tahu lewat menu mana untuk melengkapinya.
                </p>

                <button type="submit" class="btn-primary w-full justify-center inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    Impor Sekarang
                </button>
            </form>
        </x-ui.card>
    </div>

    <x-ui.card title="Format Kolom" subtitle="Header wajib persis seperti ini (urutan bebas)" class="mt-4">
        <div class="overflow-x-auto -mx-6 sm:mx-0 sm:rounded-xl border border-slate-100">
            <table class="table-modern min-w-full text-xs">
                <thead>
                    <tr>
                        <th>Sub Kegiatan Kode</th><th>Sub Kegiatan Nama</th><th>Tahun</th><th>Triwulan</th>
                        <th>Realisasi Fisik</th><th>Realisasi Keuangan</th>
                        <th>Keterangan</th><th>Analisis</th><th>Kendala</th><th>Solusi</th><th>Tindak Lanjut</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1.01.01.01.001</td>
                        <td>Pembangunan Ruang Kelas Baru</td>
                        <td>{{ now()->year }}</td>
                        <td>2</td>
                        <td>3</td>
                        <td>450000000</td>
                        <td class="text-slate-300">—</td>
                        <td class="text-slate-300">—</td>
                        <td class="text-slate-300">—</td>
                        <td class="text-slate-300">—</td>
                        <td class="text-slate-300">—</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <ul class="text-xs text-slate-500 mt-4 space-y-1.5 list-disc list-inside">
            <li><strong>Sub Kegiatan Kode/Nama</strong> — minimal salah satu diisi untuk mencocokkan; Kode diprioritaskan kalau dua-duanya ada.</li>
            <li><strong>Tahun</strong> &amp; <strong>Triwulan</strong> — wajib. Triwulan boleh angka (1–4) atau angka Romawi (I–IV).</li>
            <li><strong>Realisasi Fisik</strong> &amp; <strong>Realisasi Keuangan</strong> — angka kumulatif, sama seperti di form manual. Kosongkan kalau belum ada nilainya.</li>
            <li><strong>Keterangan</strong> s.d. <strong>Tindak Lanjut</strong> — opsional, teks bebas.</li>
        </ul>
    </x-ui.card>
@endsection
