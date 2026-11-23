@extends('layouts.app')

@section('title', 'Tinjau Evaluasi')
@section('subtitle', $evaluasi->subKegiatan?->nama)

@section('content')
    <x-ui.breadcrumb :items="['Verifikasi' => route('verifikasi.index'), 'Tinjau' => '']" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-1 space-y-4">
            <x-ui.card title="Informasi">
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-xs text-slate-500">Sub Kegiatan</dt><dd class="font-medium">{{ $evaluasi->subKegiatan?->nama }}</dd></div>
                    <div><dt class="text-xs text-slate-500">OPD</dt><dd>{{ $evaluasi->subKegiatan?->kegiatan?->program?->opd?->nama }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Periode</dt><dd>{{ $evaluasi->periode?->label }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Diajukan Oleh</dt><dd>{{ $evaluasi->creator?->nama ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Status</dt><dd><span class="{{ $evaluasi->statusBadgeClass() }}">{{ $evaluasi->statusLabel() }}</span></dd></div>
                </dl>
            </x-ui.card>

            <x-ui.card title="Target vs Realisasi">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Target Fisik</dt><dd class="font-semibold">{{ number_format($evaluasi->target_fisik, 2, ',', '.') }} {{ $evaluasi->subKegiatan?->satuan }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Realisasi Fisik</dt><dd class="font-semibold">{{ number_format($evaluasi->realisasi_fisik, 2, ',', '.') }} {{ $evaluasi->subKegiatan?->satuan }}</dd></div>
                    <div class="flex justify-between text-maroon-700"><dt>Capaian Fisik</dt><dd class="font-bold">{{ number_format($evaluasi->persentaseFisik(), 1) }}%</dd></div>
                    <hr class="border-slate-100">
                    <div class="flex justify-between"><dt class="text-slate-500">Target Keuangan</dt><dd class="font-semibold">Rp {{ number_format($evaluasi->target_keuangan, 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Realisasi Keuangan</dt><dd class="font-semibold">Rp {{ number_format($evaluasi->realisasi_keuangan, 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between text-red-700"><dt>Capaian Keuangan</dt><dd class="font-bold">{{ number_format($evaluasi->persentaseKeuangan(), 1) }}%</dd></div>
                </dl>
            </x-ui.card>
        </div>

        <div class="lg:col-span-2 space-y-4">
            <x-ui.card title="Keterangan & Analisis">
                <dl class="space-y-4 text-sm">
                    <div><dt class="text-xs font-semibold text-slate-500 uppercase mb-1">Keterangan</dt><dd class="text-slate-700">{{ $evaluasi->keterangan ?: '-' }}</dd></div>
                    <div><dt class="text-xs font-semibold text-slate-500 uppercase mb-1">Analisis</dt><dd class="text-slate-700">{{ $evaluasi->analisis ?: '-' }}</dd></div>
                    <div><dt class="text-xs font-semibold text-slate-500 uppercase mb-1">Kendala</dt><dd class="text-slate-700">{{ $evaluasi->kendala ?: '-' }}</dd></div>
                    <div><dt class="text-xs font-semibold text-slate-500 uppercase mb-1">Solusi</dt><dd class="text-slate-700">{{ $evaluasi->solusi ?: '-' }}</dd></div>
                    <div><dt class="text-xs font-semibold text-slate-500 uppercase mb-1">Tindak Lanjut</dt><dd class="text-slate-700">{{ $evaluasi->tindak_lanjut ?: '-' }}</dd></div>
                </dl>
            </x-ui.card>

            @if($evaluasi->status === 'diajukan')
                <x-ui.card title="Keputusan Verifikasi">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <form method="POST" action="{{ route('verifikasi.approve', $evaluasi) }}" class="flex-1" onsubmit="return confirm('Setujui evaluasi ini?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-success w-full justify-center">✓ Verifikasi & Setujui</button>
                        </form>
                        <button type="button" @click="$dispatch('open-modal-reject')" class="btn-danger flex-1 justify-center">✕ Tolak</button>
                    </div>
                </x-ui.card>

                <x-ui.modal id="reject" title="Tolak Evaluasi" size="md">
                    <form method="POST" action="{{ route('verifikasi.reject', $evaluasi) }}" id="form-reject">
                        @csrf @method('PATCH')
                        <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Penolakan <span class="text-red-500">*</span></label>
                        <textarea name="catatan_verifikasi" rows="4" required class="textarea" placeholder="Jelaskan apa yang perlu diperbaiki OPD..."></textarea>
                    </form>
                    <x-slot:footer>
                        <button type="button" @click="$dispatch('close-modal-reject')" class="btn-secondary btn-sm">Batal</button>
                        <button type="submit" form="form-reject" class="btn-danger btn-sm">Kirim Penolakan</button>
                    </x-slot:footer>
                </x-ui.modal>
            @elseif($evaluasi->status === 'ditolak' && $evaluasi->catatan_verifikasi)
                <x-ui.alert type="error" :dismissible="false">
                    <strong>Catatan Penolakan:</strong> {{ $evaluasi->catatan_verifikasi }}
                </x-ui.alert>
            @elseif($evaluasi->status === 'diverifikasi')
                <x-ui.alert type="success" :dismissible="false">
                    Evaluasi ini telah diverifikasi oleh {{ $evaluasi->verifier?->nama ?? '-' }} pada {{ $evaluasi->verified_at?->format('d/m/Y H:i') }}.
                </x-ui.alert>
            @endif
        </div>
    </div>
@endsection