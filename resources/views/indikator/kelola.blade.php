@extends('layouts.app')

@section('title', 'Kelola Capaian Indikator')
@section('subtitle', Str::limit($indikator->nama, 80))

@section('content')
    <x-ui.breadcrumb :items="['Indikator' => route('indikator.index', ['jenis' => $indikator->jenis]), Str::limit($indikator->nama, 40) => '']" />

    {{-- Header info --}}
    <x-ui.card class="mb-4">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-sm flex-1">
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

            <x-ui.updated-badge :at="$terakhirDiperbarui" label="Capaian diperbarui" class="flex-shrink-0" />
        </div>
    </x-ui.card>

    {{-- Grafik capaian tahunan — hanya ditampilkan kalau ada minimal satu tahun dengan
         target/realisasi berupa angka. Nilai berupa teks ("N/A", salah ketik, dsb) dilewati
         dari grafik, bukan dipaksa jadi 0, supaya grafiknya tidak menyesatkan. --}}
    @if($adaDataGrafik)
        <x-ui.card title="Tren Capaian {{ reset($rentangTahun) }}–{{ end($rentangTahun) }}"
                   subtitle="Persentase capaian tahunan (realisasi terakhir yang tercatat ÷ target)"
                   class="mb-4">
            @if($adaNilaiDilewati)
                <p class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 mb-4 flex items-start gap-2">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-8.99 4.5h.01" />
                    </svg>
                    <span>Sebagian tahun berisi target/realisasi berupa teks (bukan angka), jadi titik tahun itu dilewati dari grafik ini. Nilai aslinya tetap tersimpan dan bisa dicek di tabel bawah.</span>
                </p>
            @endif
            <canvas id="chartIndikator" height="80"></canvas>
        </x-ui.card>
    @else
        <x-ui.card class="mb-4">
            <x-ui.empty-state title="Grafik belum bisa ditampilkan"
                               description="Target & realisasi indikator ini belum berupa angka yang valid, jadi belum ada yang bisa digambar. Isi datanya di tabel bawah ini — grafik akan otomatis muncul begitu ada nilai numerik." />
        </x-ui.card>
    @endif

    {{-- Tabel input --}}
    <x-ui.card title="Input Target & Realisasi Triwulanan"
               subtitle="Semua tahun {{ reset($rentangTahun) }}–{{ end($rentangTahun) }} tersedia. Kosongkan nilai lalu simpan untuk menghapus.">
        <div class="overflow-x-auto -mx-6 sm:mx-0 sm:rounded-xl border border-slate-100">
            <table class="table-modern min-w-full">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap w-28">Tahun</th>
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
                            $target    = $baris[$tahun]['target'];
                            $realisasi = $baris[$tahun]['realisasi'];

                            $parser = app(\App\Services\IndicatorValueParser::class);
                            $targetRaw = $target?->getRawOriginal('target');
                            $targetParsed = $parser->parse($targetRaw);
                            $targetVal = $targetParsed['graph'];

                            $realisasiRaw = null;
                            foreach ([4,3,2,1] as $tw) {
                                $val = isset($realisasi[$tw]) ? $realisasi[$tw]->getRawOriginal('realisasi') : null;
                                if ($val !== null && trim((string) $val) !== '') {
                                    $realisasiRaw = $val;
                                    break;
                                }
                            }
                            $realisasiParsed = $parser->parse($realisasiRaw);
                            $realTerakhir = $realisasiParsed['graph'];

                            $capaian = ($targetVal !== null && $targetVal > 0 && $realTerakhir !== null)
                                ? round($realTerakhir / $targetVal * 100, 1)
                                : null;

                            // Ada isinya tapi bukan angka? beda kasusnya dengan "belum diisi sama sekali".
                            $capaianBukanAngka = ($realisasiRaw !== null && $realTerakhir === null)
                                || ($targetRaw !== null && trim((string) $targetRaw) !== '' && $targetVal === null);
                        @endphp
                        <tr class="align-top">
                            {{-- Kolom Tahun --}}
                            <td class="pt-4">
                                <span class="font-semibold text-slate-800 block">{{ $tahun }}</span>
                                <x-ui.updated-badge :at="$baris[$tahun]['updated_at']" variant="text" label="" emptyText="Belum diisi" :icon="false" class="mt-1" />
                            </td>

                            {{-- Kolom Target --}}
                            <td>
                                <form method="POST" action="{{ route('indikator.target.simpan', ['indikator' => $indikator->id, 'tahun' => $tahun]) }}"
                                      class="flex items-center gap-1">
                                    @csrf
                                    <input type="text" inputmode="text" name="target"
                                           value="{{ old('target', $targetRaw) }}"
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
                                            <input type="text" inputmode="text" name="realisasi"
                                                   value="{{ old('realisasi', $r?->getRawOriginal('realisasi') ?? '') }}"
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
                                        @if($r?->updated_at)
                                            <x-ui.updated-badge :at="$r->updated_at" variant="text" label="" :icon="false" />
                                        @endif
                                    </form>
                                </td>
                            @endforeach

                            {{-- Kolom Capaian --}}
                            <td class="pt-4">
                                @if($capaian !== null)
                                    <span class="badge-{{ $capaian >= 100 ? 'success' : ($capaian >= 50 ? 'warning' : 'danger') }}">
                                        {{ number_format($capaian, 1) }}%
                                    </span>
                                @elseif($capaianBukanAngka)
                                    <span class="text-xs text-amber-600" title="Target atau realisasi berisi teks, bukan angka">Nilai teks</span>
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
            Nilai realisasi bersifat <strong>kumulatif</strong> sepanjang tahun. Nilai rentang digambar memakai titik tengah, sedangkan teks biasa tetap tampil di tabel tanpa dipaksa menjadi nol.
            Capaian dihitung dari realisasi triwulan terakhir yang tercatat dibagi target tahun tersebut.
            Tanda <span class="inline-flex items-center gap-1"><svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> waktu</span> di tiap baris/sel menunjukkan kapan nilai itu terakhir disimpan.
        </p>
    </x-ui.card>

    @if($adaDataGrafik)
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        if (typeof Chart !== 'undefined') Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
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
                            // Nilai null (tahun dengan target/realisasi bukan angka) sengaja
                            // TIDAK disambung jadi garis — biar keliatan sebagai celah, bukan
                            // seolah-olah datanya menurun/naik mulus padahal cuma kosong.
                            spanGaps: false,
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
                        tooltip: {
                            callbacks: {
                                label: (c) => c.parsed.y === null
                                    ? c.dataset.label + ': bukan angka'
                                    : c.dataset.label + ': ' + c.parsed.y.toLocaleString('id-ID') + (c.dataset.yAxisID === 'yCapaian' ? '%' : ''),
                            },
                        },
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
    @endif
@endsection
