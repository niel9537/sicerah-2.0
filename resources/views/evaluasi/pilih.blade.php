@extends('layouts.app')

@section('title', 'Isi Evaluasi Renja')
@section('subtitle', 'Pilih sub kegiatan dan tahun yang akan diisi')

@section('content')
    <x-ui.breadcrumb :items="['Evaluasi Renja' => route('evaluasi.index'), 'Isi Evaluasi' => '']" />

    <x-ui.card>
        <div x-data="{ subkegiatan: '', tahun: {{ now()->year }} }" class="space-y-5 max-w-2xl">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Sub Kegiatan <span class="text-red-500">*</span></label>
                <select x-model="subkegiatan" class="select">
                    <option value="">— Pilih Sub Kegiatan —</option>
                    @foreach($subKegiatanList as $s)
                        <option value="{{ $s->id }}">{{ $s->kode }} — {{ $s->nama }} ({{ $s->kegiatan?->program?->opd?->nama }})</option>
                    @endforeach
                </select>
                @if($subKegiatanList->isEmpty())
                    <p class="text-xs text-amber-600 mt-1">Belum ada sub kegiatan terdaftar untuk OPD Anda. Hubungi Admin Bappeda.</p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tahun <span class="text-red-500">*</span></label>
                <select x-model="tahun" class="select">
                    @foreach(range(now()->year + 1, now()->year - 3) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <x-ui.alert type="info" :dismissible="false">
                Anda akan diarahkan ke halaman kelola berisi 4 tab Triwulan (I–IV). Setiap triwulan bisa disimpan sebagai draft atau langsung diajukan untuk verifikasi secara terpisah, semua di halaman yang sama.
            </x-ui.alert>

            <div class="flex items-center gap-2 pt-2">
                <button type="button"
                        @click="if(subkegiatan) window.location.href = '{{ url('/evaluasi/kelola') }}/' + subkegiatan + '/' + tahun"
                        :disabled="!subkegiatan"
                        class="btn-primary disabled:opacity-50">
                    Lanjutkan
                </button>
                <a href="{{ route('evaluasi.index') }}" class="btn-secondary">Batal</a>
            </div>
        </div>
    </x-ui.card>
@endsection