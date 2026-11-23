@extends('layouts.app')

@section('title', 'Buat Evaluasi Renja')

@section('content')
    <x-ui.breadcrumb :items="['Evaluasi Renja' => route('evaluasi.index'), 'Buat Baru' => '']" />

    <x-ui.card>
        @if(isset($noPeriode) && $noPeriode)
            <x-ui.alert type="warning" :dismissible="false">
                Belum ada <strong>periode aktif</strong>. Admin Bappeda perlu mengaktifkan periode pelaporan di menu Master Data → Periode sebelum evaluasi bisa diisi.
            </x-ui.alert>
        @else
            <x-ui.alert type="warning" :dismissible="false">
                Belum ada <strong>sub kegiatan</strong> yang terdaftar untuk OPD Anda, atau akun Anda belum dikaitkan ke OPD mana pun. Hubungi Admin Bappeda untuk memastikan data master sudah lengkap.
            </x-ui.alert>
        @endif

        <div class="mt-4">
            <a href="{{ route('evaluasi.index') }}" class="btn-secondary">← Kembali ke daftar evaluasi</a>
        </div>
    </x-ui.card>
@endsection