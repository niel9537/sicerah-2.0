@extends('layouts.app')

@section('title', 'Impor Data Indikator')
@section('subtitle', 'Sinkronisasi Bank Data dan pembaruan capaian')

@section('content')
    <x-ui.breadcrumb :items="['Indikator' => route('indikator.index', ['jenis' => $jenis]), 'Impor Excel' => '']" />

    @if(session('import_hasil'))
        @php $hasil = session('import_hasil'); @endphp
        <x-ui.card class="mb-4">
            <p class="font-semibold text-slate-800">{{ $hasil['berhasil'] }} baris berhasil diimpor</p>
            @if(count($hasil['dilewati']))
                <ul class="text-xs text-slate-600 mt-3 space-y-1 max-h-56 overflow-y-auto">
                    @foreach($hasil['dilewati'] as $pesan)<li>{{ $pesan }}</li>@endforeach
                </ul>
            @endif
        </x-ui.card>
    @endif

    <x-ui.card title="Sinkronkan Bank Data.xlsx" subtitle="Mengambil tepat 119 baris yang kolom ASPEK-nya terisi. Bagian IKK tidak diproses." class="mb-4">
        <form method="POST" action="{{ route('indikator.import.bank-data') }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">File Bank Data</label>
                <input type="file" name="bank_data" accept=".xlsx,.xls" required class="input w-full">
                @error('bank_data')<p class="text-xs text-amber-700 mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn-primary justify-center">Sinkronkan 119 IKD</button>
        </form>
        <p class="text-xs text-slate-500 mt-3">Capaian 2020–2024 disimpan sebagai realisasi TW IV. Target 2025–2030 disimpan sebagai target tahunan. Nilai angka, desimal koma/titik, rentang, dan teks tetap dipertahankan sesuai sumber.</p>
    </x-ui.card>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-ui.card title="Unduh Template Capaian" subtitle="Untuk pembaruan target dan realisasi per indikator">
            <form method="GET" action="{{ route('indikator.import.template') }}" class="space-y-3">
                <select name="jenis" class="select w-full">
                    @foreach(['IKU' => 'Indikator Kinerja Utama', 'IKK' => 'Indikator Kinerja Kunci', 'IKD' => 'Indikator Kinerja Daerah'] as $code => $label)
                        <option value="{{ $code }}" @selected($jenis === $code)>{{ $code }} — {{ $label }}</option>
                    @endforeach
                </select>
                <select name="tahun" class="select w-full">
                    @foreach(range(2020, 2030) as $y)<option value="{{ $y }}" @selected($y == now()->year)>{{ $y }}</option>@endforeach
                </select>
                <button type="submit" class="btn-secondary w-full justify-center">Unduh Template</button>
            </form>
        </x-ui.card>

        <x-ui.card title="Unggah Template Terisi" subtitle="Menerima angka, rentang, atau teks hingga 100 karakter">
            <form method="POST" action="{{ route('indikator.import.store') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="input w-full">
                @error('file')<p class="text-xs text-amber-700">{{ $message }}</p>@enderror
                <button type="submit" class="btn-primary w-full justify-center">Impor Pembaruan</button>
            </form>
        </x-ui.card>
    </div>

    <x-ui.card title="Format Template Capaian" subtitle="Nama header boleh berurutan bebas, tetapi harus sama" class="mt-4">
        <div class="overflow-x-auto -mx-6 sm:mx-0 sm:rounded-xl border border-slate-100">
            <table class="table-modern min-w-[900px] text-xs">
                <thead><tr><th>ID</th><th>Jenis</th><th>Indikator</th><th>Satuan</th><th>Tahun</th><th>Target</th><th>Realisasi TW1</th><th>Realisasi TW2</th><th>Realisasi TW3</th><th>Realisasi TW4</th></tr></thead>
                <tbody><tr><td>64</td><td>IKD</td><td>Usia Harapan Hidup</td><td>Tahun</td><td>2026</td><td>72,49–72,67</td><td>—</td><td>—</td><td>—</td><td>72,55</td></tr></tbody>
            </table>
        </div>
    </x-ui.card>
@endsection
