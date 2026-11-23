@extends('layouts.app')

@section('title', 'Dasbor')
@section('subtitle', 'Ringkasan Sistem Evaluasi Kinerja & Pembangunan')

@section('content')
    {{-- ================= HERO BANNER (Inspired by EGOVERN reference) ================= --}}
    <div class="relative overflow-hidden rounded-3xl bg-white border border-slate-100/90 shadow-card p-6 sm:p-8 mb-6 transition-all">
        <div class="absolute -right-12 -bottom-12 w-80 h-80 rounded-full bg-gradient-to-br from-amber-500/15 via-yellow-500/5 to-transparent pointer-events-none"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="max-w-2xl">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <div class="logo-shimmer-wrapper">
                        <img src="{{ asset('images/logo-sicerah.png') }}" alt="SICERAH" class="h-10 w-auto max-w-[200px] object-contain">
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200/80 text-amber-800 text-xs font-bold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Portal Perencanaan & Evaluasi
                    </span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Penguatan Akuntabilitas Kinerja Melalui <span class="text-amber-600">Teknologi Terpadu</span>
                </h2>
                <p class="text-sm text-slate-500 mt-2.5 leading-relaxed font-normal">
                    Selamat datang, <span class="font-bold text-slate-800">{{ auth()->user()->nama }}</span>. Sistem Cerdas Integrasi, Koordinasi, Evaluasi, Rekomendasi, Analisis, dan Harmonisasi Pembangunan Kabupaten Nias Barat.
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <a href="{{ route('evaluasi.index') }}" class="btn-primary">
                        Buka Evaluasi Renja →
                    </a>
                    <a href="{{ route('form81.index') }}" class="btn-secondary">
                        Cetak Form 81 (E60)
                    </a>
                    <a href="{{ route('webgis.index') }}" target="_blank" class="btn-secondary">
                        Buka WebGIS ↗
                    </a>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row lg:flex-col gap-3 justify-end lg:items-end">
                <x-ui.updated-badge :at="$terakhirDiperbarui" label="Kinerja Diperbarui" emptyText="Realisasi tahun ini belum diinput" class="!bg-slate-100 !text-slate-700 shadow-xs text-xs py-1.5 px-3.5" />
                <div class="flex items-center gap-2 text-xs text-slate-600 bg-amber-50/70 border border-amber-100 px-3.5 py-2 rounded-2xl">
                    <span class="font-bold text-amber-900">Tahun Anggaran:</span>
                    <span class="font-extrabold text-slate-900">{{ $tahun }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= STAT CARDS ================= --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <x-ui.stat-card label="Total RPJMD" :value="$stats['total_rpjmd']" color="maroon"
                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>' />

        <x-ui.stat-card label="Indikator IKU" :value="$stats['iku']" color="maroon"
                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>' />

        <x-ui.stat-card label="Indikator IKD" :value="$stats['ikd']" color="blue"
                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19V9m5 10V5m5 14v-7m5 7V3"/></svg>' />

        <x-ui.stat-card label="Indikator IKK" :value="$stats['ikk']" color="green"
                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>' />

        <x-ui.stat-card label="Dokumen LKPJ" :value="$stats['lkpj']" color="amber"
                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>' />

        <x-ui.stat-card label="Evaluasi Renja" :value="$stats['evaluasi']" color="green"
                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>' />
    </div>

    {{-- ================= OVERVIEW + HIGHLIGHTS ================= --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">
        <div class="xl:col-span-2">
            <x-ui.card title="Sebaran Indikator RPJMD 2025–2030" subtitle="Berdasarkan dokumen resmi RPJMD Kabupaten Nias Barat">
                <div class="grid grid-cols-3 gap-3.5 mb-5">
                    <div class="rounded-2xl bg-amber-50/80 border border-amber-200/80 p-4">
                        <p class="text-2xl sm:text-3xl font-extrabold text-amber-900">{{ number_format($indicatorOverview['total']) }}</p>
                        <p class="text-xs text-amber-800 font-semibold mt-1">Total Indikator RPJMD</p>
                    </div>
                    <div class="rounded-2xl bg-red-50/80 border border-red-100 p-4">
                        <p class="text-2xl sm:text-3xl font-extrabold text-red-800">{{ number_format($indicatorOverview['daerah']) }}</p>
                        <p class="text-xs text-red-700 font-semibold mt-1">Tingkat Daerah</p>
                    </div>
                    <div class="rounded-2xl bg-amber-50/80 border border-amber-100 p-4">
                        <p class="text-2xl sm:text-3xl font-extrabold text-amber-800">{{ number_format($indicatorOverview['opd']) }}</p>
                        <p class="text-xs text-amber-700 font-semibold mt-1">Per Perangkat Daerah</p>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-2.5">
                    @foreach($indicatorOverview['groups'] as $group => $count)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100/90 bg-slate-50/50 px-4 py-3 text-sm">
                            <span class="font-medium text-slate-700 truncate mr-2">{{ Str::headline(Str::lower(str_replace(['ASPEK ', 'BIDANG '], '', $group))) }}</span>
                            <span class="font-extrabold text-amber-800 bg-amber-50 px-2.5 py-0.5 rounded-lg border border-amber-200/60">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap gap-2.5">
                    <a href="{{ route('rpjmd.index') }}" class="btn-primary btn-sm">Kelola Indikator RPJMD</a>
                    <a href="{{ route('rpjmd.index', ['jenis' => 'IKU']) }}" class="btn-secondary btn-sm">Lihat 12 IKU Daerah</a>
                </div>
            </x-ui.card>
        </div>

        <x-ui.card title="12 IKU Daerah RPJMD" subtitle="Target & Capaian Tahun {{ $tahun }}">
            <div class="space-y-3.5 max-h-[380px] overflow-y-auto pr-1">
                @forelse($macroHighlights as $item)
                    <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                        <div class="min-w-0">
                            <p class="text-xs leading-5 text-slate-700 font-semibold">{{ $item['nama'] }}</p>
                            @if(!empty($item['satuan']))
                                <span class="text-[10px] text-slate-400 font-medium">{{ $item['satuan'] }}</span>
                            @endif
                        </div>
                        <p class="text-sm font-extrabold text-slate-900 text-right whitespace-nowrap bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
                            {{ $item['selected']['raw'] ?: '—' }}
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 py-4 text-center">Indikator RPJMD belum dimuat.</p>
                @endforelse
            </div>
        </x-ui.card>
    </div>

    {{-- ================= GRAFIK + PROGRESS ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <div class="lg:col-span-2">
            <x-ui.card title="Anggaran vs Realisasi Triwulanan"
                       subtitle="Total pagu RENJA {{ $tahun }} dibanding realisasi (dalam juta rupiah)">
                <canvas id="chartAnggaran" height="110"></canvas>
            </x-ui.card>
        </div>

        <x-ui.card title="Progress Pengisian" subtitle="Sub kegiatan yang telah diisi realisasi">
            <div class="space-y-4.5">
                @foreach([1,2,3,4] as $tw)
                    @php
                        $jumlah = $sudahIsiTw[$tw] ?? 0;
                        $pct = $totalSub ? round($jumlah / $totalSub * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="font-bold text-slate-700">Triwulan {{ ['I','II','III','IV'][$tw-1] }}</span>
                            <span class="text-xs font-semibold text-slate-500">{{ $jumlah }} / {{ $totalSub }} (<span class="text-amber-700 font-bold">{{ $pct }}%</span>)</span>
                        </div>
                        <div class="h-2.5 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-amber-600 to-yellow-500 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.card>
    </div>

    {{-- ================= SHORTCUT + AKTIVITAS (Matching Image 1 Lists) ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2">
            <x-ui.card title="Aktivitas Terbaru" subtitle="Evaluasi yang baru saja diinput / diperbarui">
                @if($aktivitas->isEmpty())
                    <x-ui.empty-state title="Belum ada aktivitas"
                                      description="Aktivitas pengisian evaluasi renja akan tampil di sini." />
                @else
                    <ul class="divide-y divide-slate-100 -mx-6 sm:-mx-7">
                        @foreach($aktivitas as $a)
                            <li class="px-6 sm:px-7 py-3.5 flex items-start gap-3.5 hover:bg-slate-50/80 transition-colors">
                                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200/80 flex items-center justify-center flex-shrink-0 shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-800 truncate">
                                        {{ $a->subKegiatan?->nama ?? '—' }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5 font-medium">
                                        {{ $a->periode?->label }} • oleh <span class="font-semibold text-slate-700">{{ $a->creator?->nama ?? 'sistem' }}</span>
                                        · <x-ui.updated-badge :at="$a->updated_at" variant="text" label="" :icon="false" />
                                    </p>
                                </div>
                                <span class="{{ $a->statusBadgeClass() }}">{{ $a->statusLabel() }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>
        </div>

        <x-ui.card title="Aksi Cepat" subtitle="Jalan pintas ke modul utama">
            <div class="space-y-2.5">
                <a href="{{ route('evaluasi.index') }}" class="flex items-center gap-3.5 p-3.5 rounded-2xl border border-slate-100/90 bg-white hover:border-amber-300 hover:bg-amber-50/40 hover:-translate-y-0.5 shadow-xs transition-all duration-200 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 text-slate-950 font-bold flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 group-hover:text-amber-800 transition-colors">Input Evaluasi Renja</p>
                        <p class="text-xs text-slate-500 font-medium">Isi target & realisasi triwulan</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-amber-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <a href="{{ route('form81.index') }}" class="flex items-center gap-3.5 p-3.5 rounded-2xl border border-slate-100/90 bg-white hover:border-amber-300 hover:bg-amber-50/40 hover:-translate-y-0.5 shadow-xs transition-all duration-200 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 text-slate-950 font-bold flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 group-hover:text-amber-800 transition-colors">Cetak Form 81 (E60)</p>
                        <p class="text-xs text-slate-500 font-medium">Preview & export ke Excel / PDF</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-amber-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                @if(in_array(auth()->user()->role, ['super_admin', 'admin_bappeda'], true))
                <a href="{{ route('webgis.admin.index') }}" class="flex items-center gap-3.5 p-3.5 rounded-2xl border border-slate-100/90 bg-white hover:border-amber-300 hover:bg-amber-50/40 hover:-translate-y-0.5 shadow-xs transition-all duration-200 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 text-slate-950 font-bold flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18l-6 3V6l6-3 6 3 6-3v15l-6 3-6-3zm0 0V3m6 18V6"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 group-hover:text-amber-800 transition-colors">Kelola WebGIS</p>
                        <p class="text-xs text-slate-500 font-medium">Layer dan data peta tematik</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-amber-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endif

                <a href="{{ route('subkegiatan.index') }}" class="flex items-center gap-3.5 p-3.5 rounded-2xl border border-slate-100/90 bg-white hover:border-amber-300 hover:bg-amber-50/40 hover:-translate-y-0.5 shadow-xs transition-all duration-200 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 text-slate-950 font-bold flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 group-hover:text-amber-800 transition-colors">Master Sub Kegiatan</p>
                        <p class="text-xs text-slate-500 font-medium">Kelola daftar sub kegiatan daerah</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-amber-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </x-ui.card>
    </div>

    {{-- ================= SCRIPT CHART ================= --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        if (typeof Chart !== 'undefined') Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        (function () {
            const el = document.getElementById('chartAnggaran');
            if (!el) return;
            const ctx = el.getContext('2d');
            const grad = ctx.createLinearGradient(0, 0, 0, 300);
            grad.addColorStop(0, 'rgba(217, 119, 6, 0.25)');
            grad.addColorStop(1, 'rgba(217, 119, 6, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chart['labels']),
                    datasets: [
                        {
                            label: 'Target (Rp Juta)',
                            data: @json($chart['target']),
                            borderColor: '#94a3b8',
                            borderDash: [6, 6],
                            fill: false,
                            tension: 0.35,
                            pointRadius: 3,
                            borderWidth: 2,
                        },
                        {
                            label: 'Realisasi (Rp Juta)',
                            data: @json($chart['realisasi']),
                            borderColor: '#d97706',
                            backgroundColor: grad,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointBackgroundColor: '#d97706',
                            borderWidth: 2.5,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600' } } },
                        tooltip: {
                            callbacks: {
                                label: (c) => c.dataset.label + ': Rp ' + c.parsed.y.toLocaleString('id-ID') + ' Juta',
                            },
                        },
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(226, 232, 240, 0.6)' }, ticks: { callback: v => v.toLocaleString('id-ID') } },
                        x: { grid: { display: false } },
                    },
                },
            });
        })();
    </script>
@endsection
