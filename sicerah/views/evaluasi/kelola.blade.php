@extends('layouts.app')

@section('title', 'Kelola Evaluasi Renja')
@section('subtitle', $subkegiatan->nama)

@section('content')
    <x-ui.breadcrumb :items="['Evaluasi Renja' => route('evaluasi.index'), $subkegiatan->nama => '']" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
        <x-ui.card title="Informasi Sub Kegiatan">
            <dl class="space-y-3 text-sm">
                <div><dt class="text-xs text-slate-500">Kode</dt><dd class="font-medium">{{ $subkegiatan->kode ?? '-' }}</dd></div>
                <div><dt class="text-xs text-slate-500">OPD</dt><dd>{{ $subkegiatan->kegiatan?->program?->opd?->nama ?? '-' }}</dd></div>
                <div><dt class="text-xs text-slate-500">Satuan</dt><dd>{{ $subkegiatan->satuan ?? '-' }}</dd></div>
                <div><dt class="text-xs text-slate-500">Tahun</dt><dd class="font-semibold">{{ $tahun }}</dd></div>
            </dl>
        </x-ui.card>

        <x-ui.card title="Target Tahunan {{ $tahun }}" class="lg:col-span-2">
            @if($target)
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div><p class="text-xs text-slate-500">Target Fisik</p><p class="font-semibold">{{ number_format($target->target_kinerja, 2, ',', '.') }} {{ $subkegiatan->satuan }}</p></div>
                    <div><p class="text-xs text-slate-500">Pagu Renstra</p><p class="font-semibold">Rp {{ number_format($target->pagu_renstra ?? 0, 0, ',', '.') }}</p></div>
                    <div><p class="text-xs text-slate-500">Pagu Renja</p><p class="font-semibold">Rp {{ number_format($target->pagu_renja ?? 0, 0, ',', '.') }}</p></div>
                    <div><p class="text-xs text-slate-500">Pagu Efektif</p><p class="font-semibold text-maroon-700">Rp {{ number_format($target->paguEfektif(), 0, ',', '.') }}</p><p class="text-[10px] text-slate-400">{{ $target->sumberPaguLabel() }}</p></div>
                </div>
            @else
                <x-ui.alert type="warning" :dismissible="false">
                    Target tahunan untuk tahun {{ $tahun }} belum diatur. Hubungi Admin Bappeda sebelum mengisi realisasi.
                </x-ui.alert>
            @endif
        </x-ui.card>
    </div>

    <div x-data="{ tab: {{ $activeTriwulan }} }">
        {{-- Tab headers --}}
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach($slots as $tw => $slot)
                @php $ev = $slot['evaluasi']; @endphp
                <button type="button" @click="tab = {{ $tw }}"
                        :class="tab === {{ $tw }} ? 'bg-brand-gradient text-white shadow-soft' : 'bg-white text-slate-600 border border-slate-200'"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition flex items-center gap-2">
                    Triwulan {{ ['I', 'II', 'III', 'IV'][$tw - 1] }}
                    @if($ev)
                        <span class="{{ $ev->statusBadgeClass() }} !py-0 !px-2">{{ $ev->statusLabel() }}</span>
                    @else
                        <span class="badge-default !py-0 !px-2">Kosong</span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Tab panels --}}
        @foreach($slots as $tw => $slot)
            @php
                $periode  = $slot['periode'];
                $ev       = $slot['evaluasi'];
                $editable = ! $ev || in_array($ev->status, ['draft', 'ditolak']);
                $romawi   = ['I', 'II', 'III', 'IV'][$tw - 1];
            @endphp
            <div x-show="tab === {{ $tw }}" x-cloak>
                <x-ui.card title="Triwulan {{ $romawi }} {{ $tahun }}">
                    @if(! $periode)
                        <x-ui.empty-state title="Periode belum tersedia"
                            description="Admin Bappeda belum membuat periode Triwulan {{ $romawi }} tahun {{ $tahun }} di menu Master Data → Periode." />
                    @else
                        @if($ev && $ev->status === 'ditolak' && $ev->catatan_verifikasi)
                            <x-ui.alert type="error" :dismissible="false">
                                <strong>Ditolak verifikator:</strong> {{ $ev->catatan_verifikasi }}
                            </x-ui.alert>
                        @endif

                        <form method="POST"
                              action="{{ route('evaluasi.simpan', ['subkegiatan' => $subkegiatan->id, 'tahun' => $tahun, 'triwulan' => $tw]) }}"
                              data-ajukan-url="{{ route('evaluasi.ajukan', ['subkegiatan' => $subkegiatan->id, 'tahun' => $tahun, 'triwulan' => $tw]) }}"
                              class="space-y-5" id="form-tw-{{ $tw }}">
                            @csrf
                            <input type="hidden" name="_method" value="POST" class="method-field">

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Realisasi Fisik (Kumulatif)</label>
                                    <input type="number" step="0.01" name="realisasi_fisik"
                                           value="{{ old('realisasi_fisik', $ev->realisasi_fisik ?? '') }}"
                                           class="input" {{ $editable ? '' : 'disabled' }}>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Realisasi Keuangan (Kumulatif, Rp)</label>
                                    <input type="number" step="0.01" name="realisasi_keuangan"
                                           value="{{ old('realisasi_keuangan', $ev->realisasi_keuangan ?? '') }}"
                                           class="input" {{ $editable ? '' : 'disabled' }}>
                                </div>
                            </div>

                            @if($ev)
                                <div class="grid grid-cols-2 gap-4 p-3 bg-slate-50 rounded-xl">
                                    <div class="text-center">
                                        <p class="text-xs text-slate-500">Capaian Fisik</p>
                                        <p class="text-xl font-bold text-maroon-700">{{ number_format($ev->persentaseFisik(), 1) }}%</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xs text-slate-500">Capaian Keuangan</p>
                                        <p class="text-xl font-bold text-sky-700">{{ number_format($ev->persentaseKeuangan(), 1) }}%</p>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan</label>
                                <textarea name="keterangan" rows="2" class="textarea" {{ $editable ? '' : 'disabled' }}>{{ old('keterangan', $ev->keterangan ?? '') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Analisis</label>
                                <textarea name="analisis" rows="2" class="textarea" {{ $editable ? '' : 'disabled' }}>{{ old('analisis', $ev->analisis ?? '') }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Kendala</label>
                                    <textarea name="kendala" rows="2" class="textarea" {{ $editable ? '' : 'disabled' }}>{{ old('kendala', $ev->kendala ?? '') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Solusi</label>
                                    <textarea name="solusi" rows="2" class="textarea" {{ $editable ? '' : 'disabled' }}>{{ old('solusi', $ev->solusi ?? '') }}</textarea>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Tindak Lanjut</label>
                                <textarea name="tindak_lanjut" rows="2" class="textarea" {{ $editable ? '' : 'disabled' }}>{{ old('tindak_lanjut', $ev->tindak_lanjut ?? '') }}</textarea>
                            </div>

                            @if($editable)
                                <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100">
                                    <button type="submit" class="btn-secondary">Simpan Draft</button>
                                    <button type="button" class="btn-primary" onclick="ajukanTriwulan(this)">Ajukan untuk Verifikasi</button>
                                    @if($ev && $ev->status === 'draft')
                                        <span class="ml-auto"></span>
                                    @endif
                                </div>
                            @else
                                <p class="text-xs text-slate-500 pt-2 border-t border-slate-100">
                                    Data terkunci karena status <strong>{{ $ev->statusLabel() }}</strong>.
                                </p>
                            @endif
                        </form>

                        @if($ev && $ev->status === 'draft')
                            <form method="POST" action="{{ route('evaluasi.hapus', ['subkegiatan' => $subkegiatan->id, 'tahun' => $tahun, 'triwulan' => $tw]) }}"
                                  onsubmit="return confirm('Hapus draft Triwulan {{ $romawi }} ini?')" class="mt-2">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">Hapus Draft Triwulan {{ $romawi }}</button>
                            </form>
                        @endif
                    @endif
                </x-ui.card>
            </div>
        @endforeach
    </div>

    <script>
        function ajukanTriwulan(btn) {
            if (!confirm('Ajukan triwulan ini untuk verifikasi? Data tidak dapat diubah sampai proses verifikasi selesai.')) return;
            const form = btn.closest('form');
            form.querySelector('.method-field').value = 'PATCH';
            form.action = form.dataset.ajukanUrl;
            form.submit();
        }
    </script>
@endsection