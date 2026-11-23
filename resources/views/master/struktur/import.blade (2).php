@extends('layouts.app')

@section('title', 'Impor Struktur Program')
@section('subtitle', 'Unggah Program, Kegiatan & Sub Kegiatan sekaligus')

@section('content')
    <x-ui.breadcrumb :items="['Master Data' => '', 'Impor Struktur Program' => '']" />

    @if(session('import_hasil'))
        @php $hasil = session('import_hasil'); @endphp
        <x-ui.card class="mb-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-800">{{ $hasil['berhasil'] }} baris berhasil diproses</p>
                    @if(!empty($hasil['ringkasan']))
                        <p class="text-sm text-slate-500 mt-1">{{ $hasil['ringkasan'] }}</p>
                    @endif

                    @if(count($hasil['dilewati']))
                        <p class="text-sm text-amber-700 mt-2">{{ count($hasil['dilewati']) }} baris perlu dicek:</p>
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
        Satu baris = satu Sub Kegiatan beserta induknya (Urusan, Program, Kegiatan). Kalau
        Program/Kegiatan/Sub Kegiatan belum ada, <strong>otomatis dibuat</strong>; kalau sudah
        ada (dicocokkan lewat kode, atau nama kalau kode kosong), datanya diperbarui. OPD harus
        sudah terdaftar lebih dulu lewat menu OPD — tidak dibuat otomatis dari sini.
    </x-ui.alert>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-ui.card title="1. Unduh Template" subtitle="Terisi seluruh struktur yang sudah ada saat ini">
            <form method="GET" action="{{ route('struktur.import.template') }}" class="space-y-3">
                <div>
                    <label class="text-xs text-slate-500 block mb-1">OPD (opsional — kosongkan untuk semua OPD)</label>
                    <select name="opd_id" class="select w-full">
                        <option value="">— Semua OPD —</option>
                        @foreach($opdList as $o)
                            <option value="{{ $o->id }}">{{ $o->nama }}</option>
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
            <form method="POST" action="{{ route('struktur.import.store') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="input w-full">
                @error('file')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

                <p class="text-xs text-slate-400">
                    Baris tanpa OPD/Program/Kegiatan/Sub Kegiatan yang lengkap akan dilewati dan
                    dilaporkan — baris lain tetap diproses.
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
                        <th>Urusan Kode</th><th>Urusan Nama</th><th>OPD</th>
                        <th>Program Kode</th><th>Program Nama</th><th>Sasaran PD</th>
                        <th>Kegiatan Kode</th><th>Kegiatan Nama</th>
                        <th>Sub Kegiatan Kode</th><th>Sub Kegiatan Nama</th><th>Indikator Kinerja</th><th>Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1.01</td>
                        <td>Pendidikan</td>
                        <td>Dinas Pendidikan</td>
                        <td>1.01.01</td>
                        <td>Program Pengelolaan Pendidikan</td>
                        <td class="text-slate-300">—</td>
                        <td>1.01.01.01</td>
                        <td>Pengelolaan Pendidikan Dasar</td>
                        <td>1.01.01.01.001</td>
                        <td>Pembangunan Ruang Kelas Baru</td>
                        <td>Jumlah ruang kelas terbangun</td>
                        <td>Unit</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <ul class="text-xs text-slate-500 mt-4 space-y-1.5 list-disc list-inside">
            <li><strong>OPD</strong>, <strong>Program Nama</strong>, <strong>Kegiatan Nama</strong>, <strong>Sub Kegiatan Nama</strong> — wajib diisi di setiap baris.</li>
            <li><strong>OPD</strong> harus sudah ada di sistem (dicocokkan persis by nama); tidak dibuat otomatis.</li>
            <li><strong>Urusan</strong> — kalau kode & nama sama-sama diisi dan belum ada yang cocok, dibuat otomatis. Kalau dikosongkan, Program tetap dibuat tanpa Urusan (bisa dilengkapi belakangan lewat menu Program).</li>
            <li><strong>Program / Kegiatan / Sub Kegiatan</strong> — dicocokkan lewat Kode dulu (kalau diisi), baru Nama. Tidak ketemu → dibuat baru secara otomatis dalam struktur induknya masing-masing.</li>
            <li><strong>Sasaran PD</strong>, <strong>Indikator Kinerja</strong>, <strong>Satuan</strong> — opsional.</li>
        </ul>
    </x-ui.card>
@endsection
