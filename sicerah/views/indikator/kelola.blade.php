@extends('layouts.app')

@section('title', 'Kelola Capaian Indikator')
@section('subtitle', Str::limit($indikator->nama, 80))

@section('content')
    <x-ui.breadcrumb :items="['Indikator' => route('indikator.index', ['jenis' => $indikator->jenis]), Str::limit($indikator->nama, 40) => '']" />

    {{-- Header info --}}
    <x-ui.card class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-xs text-slate-500">Jenis</p>
                <p><span class="badge-maroon">{{ $indikator->jenis }}</span></p>
            </div>
            <div>
                <p class="text-xs text-slate-500">OPD Pengampu</p>
                <p class="font-medium">{{ $indikator->opd?->nama ?? '— Tidak terikat OPD —' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Satuan</p>
                <p class="font-medium">{{ $indikator->satuan ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Sumber Data</p>
                <p class="font-medium">{{ $indikator->sumber_data ?? '-' }}</p>
            </div>
        </div>
    </x-ui.card>

    {{-- Grafik capaian tahunan --}}
    <x-ui.card title="Tren Capaian {{ reset($rentangTahun) }}–{{ end($rentangTahun) }}"
               subtitle="Persentase capaian tahunan (realisasi terakhir yang tercatat ÷ target)"
               class="mb-4">
        <canvas id="chartIndikator" height="80"></canvas>
    </x-ui.card>

    {{-- Tabel input --}}
    <x-ui.card title="Input Target & Realisasi Triwulanan"
               subtitle="Semua tahun {{ reset($rentangTahun) }}–{{ end($rentangTahun) }} tersedia. Kosongkan nilai lalu simpan untuk menghapus.">
        <div class="overflow-x-auto -mx-6 sm:mx-0 sm:rounded-xl border border-slate-100">
            <table class="table-modern min-w-full">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap w-24">Tahun</th>
                        <th class="whitespace-nowrap">Target</th>
                        <th class="whitespace-nowrap">Realisasi TW I</th>
                        <th class="whitespace-nowrap">Realisasi TW II</th>
                        <th class="whitespace-nowrap">Realisasi TW III</th>
                        <th class="whitespace-nowrap">Realisasi TW IV</th>
                        <th class="whitespace-nowrap w-32">Capaian TW IV</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rentangTahun as $tahun)
                        @php
                        
                            $target       = $baris[$tahun]['target'];
                            $realisasi    = $baris[$tahun]['realisasi'];
                            $targetVal    = $target?->target;
                            $realTerakhir = null;
                            foreach ([4,3,2,1] as $tw) {
                                if (($realisasi[$tw] ?? null)?->realisasi !== null) {
                                    $realTerakhir = (float) $realisasi[$tw]->realisasi;
                                    break;
                                }
                            }
                            $capaian = ($targetVal > 0 && $realTerakhir !== null)
                                ? round($realTerakhir / (float) $targetVal * 100, 1)
                                : null;
                        @endphp
                        <tr class="align-top">
                            {{-- Kolom Tahun --}}
                            <td class="font-semibold text-slate-800 pt-4">{{ $tahun }}</td>

                            {{-- Kolom Target --}}
                            <td>
                                <form method="POST" action="{{ route('indikator.target.simpan', ['indikator' => $indikator->id, 'tahun' => $tahun]) }}"
                                      class="flex items-center gap-1">
                                    @csrf
                                    <input type="number" step="0.0001" name="target"
                                           value="{{ old('target', $targetVal) }}"
                                           class="input !py-1.5 !text-xs w-28" placeholder="—">
                                    <button type="submit" class="btn-primary btn-sm !px-2" title="Simpan target">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>

                            {{-- 4 kolom Realisasi Triwulan --}}
                            @foreach([1, 2, 3, 4] as $tw)
                                @php $r = $realisasi[$tw] ?? null; @endphp
                                <td>
                                    <form method="POST" action="{{ route('indikator.realisasi.simpan', ['indikator' => $indikator->id, 'tahun' => $tahun, 'triwulan' => $tw]) }}"
                                          class="space-y-1">
                                        @csrf
                                        <div class="flex items-center gap-1">
                                            <input type="number" step="0.0001" name="realisasi"
                                                   value="{{ old('realisasi', $r->realisasi ?? '') }}"
                                                   class="input !py-1.5 !text-xs w-24" placeholder="—">
                                            <button type="submit" class="btn-primary btn-sm !px-2" title="Simpan TW {{ ['I','II','III','IV'][$tw-1] }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <select name="status" class="select !py-1 !text-[10px] !pr-6">
                                            <option value="draft" @selected(($r->status ?? 'draft') === 'draft')>Draft</option>
                                            <option value="diajukan" @selected(($r->status ?? '') === 'diajukan')>Diajukan</option>
                                            <option value="diverifikasi" @selected(($r->status ?? '') === 'diverifikasi')>Diverifikasi</option>
                                            <option value="ditolak" @selected(($r->status ?? '') === 'ditolak')>Ditolak</option>
                                        </select>
                                    </form>
                                </td>
                            @endforeach

                            {{-- Kolom Capaian --}}
                            <td class="pt-4">
                                @if($capaian !== null)
                                    <span class="badge-{{ $capaian >= 100 ? 'success' : ($capaian >= 50 ? 'warning' : 'danger') }}">
                                        {{ number_format($capaian, 1) }}%
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p class="text-xs text-slate-400 mt-3">
            💡 Nilai realisasi bersifat <strong>kumulatif</strong> sepanjang tahun.
            Capaian dihitung dari realisasi triwulan terakhir yang tercatat ÷ target tahun tersebut.
        </p>
    </x-ui.card>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const ctx = document.getElementById('chartIndikator');
            if (!ctx) return;

            const grad = ctx.getContext('2d').createLinearGradient(0, 0, 0, 260);
            grad.addColorStop(0, 'rgba(139,30,48,.35)');
            grad.addColorStop(1, 'rgba(139,30,48,0)');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json(array_map('strval', $rentangTahun)),
                    datasets: [
                        {
                            type: 'line',
                            label: 'Capaian (%)',
                            data: @json(array_values($grafikCapaian)),
                            borderColor: '#8b1e30',
                            backgroundColor: grad,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 5,
                            pointBackgroundColor: '#8b1e30',
                            yAxisID: 'yCapaian',
                            order: 0,
                        },
                        {
                            type: 'bar',
                            label: 'Target',
                            data: @json(array_values($grafikTarget)),
                            backgroundColor: 'rgba(148, 163, 184, .5)',
                            borderRadius: 6,
                            yAxisID: 'yNilai',
                            order: 2,
                        },
                        {
                            type: 'bar',
                            label: 'Realisasi',
                            data: @json(array_values($grafikRealisasi)),
                            backgroundColor: 'rgba(220, 72, 85, .8)',
                            borderRadius: 6,
                            yAxisID: 'yNilai',
                            order: 1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } },
                    },
                    scales: {
                        yNilai: {
                            beginAtZero: true,
                            position: 'left',
                            title: { display: true, text: 'Nilai ({{ $indikator->satuan ?? '' }})' },
                        },
                        yCapaian: {
                            beginAtZero: true,
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: { callback: v => v + '%' },
                            title: { display: true, text: 'Capaian (%)' },
                        },
                    },
                },
            });
        })();
    </script>
@endsection