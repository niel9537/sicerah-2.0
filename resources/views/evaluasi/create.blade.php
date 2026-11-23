@extends('layouts.app')

@section('title', 'Buat Evaluasi Renja')
@section('subtitle', 'Pilih sub kegiatan dan periode yang akan dievaluasi')

@section('content')
    <x-ui.breadcrumb :items="['Evaluasi Renja' => route('evaluasi.index'), 'Buat Baru' => '']" />

    <x-ui.card>
        <form method="POST" action="{{ route('evaluasi.store') }}" class="space-y-5 max-w-2xl">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Sub Kegiatan <span class="text-red-500">*</span></label>
                <select name="subkegiatan_id" required class="select">
                    <option value="">— Pilih Sub Kegiatan —</option>
                    @foreach($subKegiatanList as $s)
                        <option value="{{ $s->id }}" @selected(old('subkegiatan_id') == $s->id)>
                            {{ $s->kode }} — {{ $s->nama }} ({{ $s->kegiatan?->program?->opd?->nama }})
                        </option>
                    @endforeach
                </select>
                @if($subKegiatanList->isEmpty())
                    <p class="text-xs text-amber-600 mt-1">Belum ada sub kegiatan terdaftar untuk OPD Anda. Hubungi Admin Bappeda.</p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Periode <span class="text-red-500">*</span></label>
                <select name="periode_id" required class="select">
                    <option value="">— Pilih Periode —</option>
                    @foreach($periodeList as $p)
                        <option value="{{ $p->id }}" @selected(old('periode_id') == $p->id)>{{ $p->label }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-400 mt-1">Hanya periode berstatus Aktif yang ditampilkan.</p>
            </div>

            <x-ui.alert type="info" :dismissible="false">
                Setelah menyimpan, Anda akan diarahkan ke form lengkap untuk mengisi realisasi, analisis, kendala, solusi, dan tindak lanjut sebelum mengajukan verifikasi.
            </x-ui.alert>

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="btn-primary">Lanjutkan</button>
                <a href="{{ route('evaluasi.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </x-ui.card>
@endsection