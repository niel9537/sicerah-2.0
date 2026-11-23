@extends('layouts.app')

@section('title', 'Detail Evaluasi Renja')
@section('subtitle', $evaluasi->subKegiatan?->nama)

@section('content')
    <x-ui.breadcrumb :items="['Evaluasi Renja' => route('evaluasi.index'), $evaluasi->subKegiatan?->nama ?? 'Detail' => '']" />

    @php $editable = in_array($evaluasi->status, ['draft', 'ditolak']); @endphp

    @if($evaluasi->status === 'ditolak' && $evaluasi->catatan_verifikasi)
        <x-ui.alert type="error" :dismissible="false">
            <strong>Evaluasi ini ditolak oleh verifikator:</strong> {{ $evaluasi->catatan_verifikasi }}
            <br><span class="text-xs">Silakan perbaiki data di bawah, lalu ajukan kembali.</span>
        </x-ui.alert>
    @endif

    @if(!$target)
        <x-ui.alert type="warning" :dismissible="false">
            Target tahunan untuk sub kegiatan ini pada tahun {{ $evaluasi->periode?->tahun }} tidak ditemukan. Persentase capaian mungkin tidak akurat.
        </x-ui.alert>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-1 space-y-4">
            <x-ui.card title="Informasi">
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-xs text-slate-500">Sub Kegiatan</dt><dd class="font-medium text-slate-800">{{ $evaluasi->subKegiatan?->nama }}</dd></div>
                    <div><dt class="text-xs text-slate-500">OPD</dt><dd>{{ $evaluasi->subKegiatan?->kegiatan?->program?->opd?->nama }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Periode</dt><dd>{{ $evaluasi->periode?->label }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Status</dt><dd><span class="{{ $evaluasi->statusBadgeClass() }}">{{ $evaluasi->statusLabel() }}</span></dd></div>
                </dl>
            </x-ui.card>

            <x-ui.card title="Target Tahunan {{ $evaluasi->periode?->tahun }}">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Target Fisik</dt><dd class="font-semibold">{{ number_format($evaluasi->target_fisik, 2, ',', '.') }} {{ $evaluasi->subKegiatan?->satuan }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Target Keuangan</dt><dd class="font-semibold">Rp {{ number_format($evaluasi->target_keuangan, 0, ',', '.') }}</dd></div>
                    @if($target)
                        <div class="pt-2 border-t border-slate-100 text-xs text-slate-400">Sumber: {{ $target->sumberPaguLabel() }}</div>
                    @endif
                </dl>
            </x-ui.card>
        </div>

        <div class="lg:col-span-2">
            <x-ui.card title="Realisasi & Analisis">
                <form method="POST" action="{{ route('evaluasi.update', $evaluasi) }}" id="form-evaluasi" class="space-y-5">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" id="method-field">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Realisasi Fisik (Kumulatif)</label>
                            <input type="number" step="0.01" name="realisasi_fisik"
                                   value="{{ old('realisasi_fisik', $evaluasi->realisasi_fisik) }}"
                                   class="input" {{ $editable ? '' : 'disabled' }}>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Realisasi Keuangan (Kumulatif, Rp)</label>
                            <input type="number" step="0.01" name="realisasi_keuangan"
                                   value="{{ old('realisasi_keuangan', $evaluasi->realisasi_keuangan) }}"
                                   class="input" {{ $editable ? '' : 'disabled' }}>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 p-3 bg-slate-50 rounded-xl">
                        <div class="text-center">
                            <p class="text-xs text-slate-500">Capaian Fisik</p>
                            <p class="text-xl font-bold text-maroon-700">{{ number_format($evaluasi->persentaseFisik(), 1) }}%</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-slate-500">Capaian Keuangan</p>
                            <p class="text-xl font-bold text-red-700">{{ number_format($evaluasi->persentaseKeuangan(), 1) }}%</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan <span class="text-red-500">*</span></label>
                        <textarea name="keterangan" rows="2" class="textarea" {{ $editable ? '' : 'disabled' }}>{{ old('keterangan', $evaluasi->keterangan) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Analisis</label>
                        <textarea name="analisis" rows="2" class="textarea" {{ $editable ? '' : 'disabled' }}>{{ old('analisis', $evaluasi->analisis) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Kendala</label>
                            <textarea name="kendala" rows="2" class="textarea" {{ $editable ? '' : 'disabled' }}>{{ old('kendala', $evaluasi->kendala) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Solusi</label>
                            <textarea name="solusi" rows="2" class="textarea" {{ $editable ? '' : 'disabled' }}>{{ old('solusi', $evaluasi->solusi) }}</textarea>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tindak Lanjut</label>
                        <textarea name="tindak_lanjut" rows="2" class="textarea" {{ $editable ? '' : 'disabled' }}>{{ old('tindak_lanjut', $evaluasi->tindak_lanjut) }}</textarea>
                    </div>

                    @if($editable)
                        <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100">
                            <button type="submit" class="btn-secondary">Simpan Draft</button>
                            <button type="button" class="btn-primary" onclick="submitAsAjukan()">Ajukan untuk Verifikasi</button>
                            <a href="{{ route('evaluasi.index') }}" class="text-sm text-slate-500 hover:text-slate-700 ml-auto">Kembali ke daftar</a>
                        </div>
                    @else
                        <div class="pt-2 border-t border-slate-100">
                            <p class="text-xs text-slate-500">Data terkunci karena status evaluasi adalah <strong>{{ $evaluasi->statusLabel() }}</strong>.</p>
                            <a href="{{ route('evaluasi.index') }}" class="text-sm text-maroon-700 hover:text-maroon-900 font-medium">← Kembali ke daftar</a>
                        </div>
                    @endif
                </form>
            </x-ui.card>
        </div>
    </div>

    @if($editable)
    <script>
        function submitAsAjukan() {
            if (!confirm('Ajukan evaluasi ini untuk diverifikasi? Setelah diajukan, data tidak dapat diubah sampai proses verifikasi selesai.')) return;
            const form = document.getElementById('form-evaluasi');
            document.getElementById('method-field').value = 'PATCH';
            form.action = '{{ route('evaluasi.submit', $evaluasi) }}';
            form.submit();
        }
    </script>
    @endif
@endsection