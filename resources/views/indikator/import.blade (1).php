@extends('layouts.app')

@section('title', 'Impor Capaian dari Excel')
@section('subtitle', 'Unggah target & realisasi banyak indikator sekaligus')

@section('content')
    <x-ui.breadcrumb :items="['Indikator' => route('indikator.index', ['jenis' => $jenis]), 'Impor Excel' => '']" />

    @if(session('import_hasil'))
        @php $hasil = session('import_hasil'); @endphp
        <x-ui.card class="mb-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-800">{{ $hasil['berhasil'] }} baris berhasil diimpor</p>

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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Langkah 1: unduh template --}}
        <x-ui.card title="1. Unduh Template" subtitle="Sudah terisi daftar indikator & data yang ada saat ini">
            <form method="GET" action="{{ route('indikator.import.template') }}" class="space-y-3">
                <div>
                    <label class="text-xs text-slate-500 block mb-1">Jenis Indikator</label>
                    <select name="jenis" class="select w-full">
                        @foreach(['IKU' => 'Indikator Kinerja Utama', 'IKK' => 'Indikator Kinerja Kunci', 'IKD' => 'Indikator Kinerja Daerah'] as $code => $label)
                            <option value="{{ $code }}" @selected($jenis === $code)>{{ $code }} — {{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-slate-500 block mb-1">Tahun</label>
                    <select name="tahun" class="select w-full">
                        @foreach(range(now()->year, now()->year - 4) as $y)
                            <option value="{{ $y }}" @selected($y == now()->year)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-secondary w-full justify-center inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                    Unduh Template (.xlsx)
                </button>
            </form>
        </x-ui.card>

        {{-- Langkah 2: upload --}}
        <x-ui.card title="2. Unggah File Terisi" subtitle="Format .xlsx, .xls, atau .csv, maksimal 10MB">
            <form method="POST" action="{{ route('indikator.import.store') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="input w-full">
                @error('file')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

                <p class="text-xs text-slate-400">
                    Baris yang cocok akan langsung diperbarui. Baris dengan indikator tidak ditemukan
                    atau nilai bukan angka akan dilewati dan dilaporkan — sisanya tetap tersimpan.
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
                        <th>ID</th><th>Jenis</th><th>Indikator</th><th>Satuan</th><th>Tahun</th>
                        <th>Target</th><th>Realisasi TW1</th><th>Realisasi TW2</th><th>Realisasi TW3</th><th>Realisasi TW4</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-slate-400">64</td>
                        <td>IKD</td>
                        <td>Indeks Pembangunan Manusia</td>
                        <td>Indeks</td>
                        <td>2026</td>
                        <td>68.50</td>
                        <td class="text-slate-300">—</td>
                        <td class="text-slate-300">—</td>
                        <td class="text-slate-300">—</td>
                        <td>68.50</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <ul class="text-xs text-slate-500 mt-4 space-y-1.5 list-disc list-inside">
            <li><strong>ID</strong> — kalau diisi (otomatis ada di template), dipakai mencocokkan indikator secara pasti. Boleh dikosongkan.</li>
            <li><strong>Jenis</strong> &amp; <strong>Indikator</strong> — dipakai mencari indikator kalau ID kosong. Nama harus sama persis dengan yang sudah terdaftar; impor <strong>tidak</strong> membuat indikator baru.</li>
            <li><strong>Tahun</strong> — wajib diisi, berupa angka.</li>
            <li><strong>Target</strong> &amp; <strong>Realisasi TW1–TW4</strong> — kosongkan sel yang tidak ingin diubah. Isi harus berupa angka; kalau berupa teks, hanya sel itu yang dilewati (baris lain & sel lain di baris yang sama tetap tersimpan).</li>
        </ul>
    </x-ui.card>
@endsection
