@extends('layouts.app')

@section('title', 'Dasbor')
@section('subtitle', 'Ringkasan Sistem Evaluasi Kinerja')

@section('content')
    {{-- ================= HERO BANNER ================= --}}
    <div class="relative overflow-hidden rounded-2xl bg-brand-gradient text-white p-6 sm:p-8 mb-6 shadow-soft">
        <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-white/10"></div>
        <div class="absolute -right-6 -bottom-16 w-40 h-40 rounded-full bg-white/10"></div>

        <div class="relative">
            <p class="text-xs uppercase tracking-widest opacity-80">Selamat Datang, {{ auth()->user()->nama }}</p>
            <h2 class="text-2xl sm:text-3xl font-bold mt-1">SICERAH Nias Barat</h2>
            <p class="text-sm opacity-90 mt-2 max-w-2xl">
                Sistem Informasi Capaian, Evaluasi, dan Perencanaan  Pembangunan Daerah Kabupaten Nias Barat — kelola evaluasi RENJA, indikator kinerja, hingga cetak Form 81 dengan mudah.
            </p>

            <div class="mt-5 flex flex-wrap gap-2">
                <a href="{{ route('evaluasi.index') }}" class="inline-flex items-center gap-2 bg-white/95 text-maroon-800 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-white transition">
                    Buka Evaluasi Renja →
                </a>
                <a href="{{ route('form81.index') }}" class="inline-flex items-center gap-2 bg-white/10 backdrop-blur border border-white/30 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-white/20 transition">
                    Cetak Form 81
                </a>
            </div>
        </div>
    </div>

    {{-- ================= STAT CARDS ================= --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.stat-card label="Indikator Kinerja Utama" :value="$stats['iku']" color="maroon"
                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>' />

        <x-ui.stat-card label="Indikator Kinerja Kunci" :value="$stats['ikk']" color="blue"
                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>' />

        <x-ui.stat-card label="Dokumen LKPJ" :value="$stats['lkpj']" color="amber"
                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/></svg>' />

        <x-ui.stat-card label="Evaluasi Renja" :value="$stats['evaluasi']" color="green"
                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>' />
    </div>

    {{-- ================= GRAFIK + PROGRESS ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="lg:col-span-2">
            <x-ui.card title="Anggaran vs Realisasi Triwulanan"
                       subtitle="Total pagu RENJA {{ $tahun }} dibanding realisasi (dalam juta rupiah)">
                <canvas id="chartAnggaran" height="110"></canvas>
            </x-ui.card>
        </div>

        <x-ui.card title="Progress Pengisian" subtitle="Sub kegiatan yang telah diisi realisasi">
            <div class="space-y-4">
                @foreach([1,2,3,4] as $tw)
                    @php
                        $jumlah = $sudahIsiTw[$tw] ?? 0;
                        $pct = $totalSub ? round($jumlah / $totalSub * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="font-medium text-slate-700">Triwulan {{ ['I','II','III','IV'][$tw-1] }}</span>
                            <span class="text-slate-500">{{ $jumlah }} / {{ $totalSub }} ({{ $pct }}%)</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-brand-gradient rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.card>
    </div>

    {{-- ================= SHORTCUT + AKTIVITAS ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2">
            <x-ui.card title="Aktivitas Terbaru" subtitle="Evaluasi yang baru saja diinput / diubah">
                @if($aktivitas->isEmpty())
                    <x-ui.empty-state title="Belum ada aktivitas"
                                      description="Aktivitas evaluasi akan tampil di sini." />
                @else
                    <ul class="divide-y divide-slate-100 -mx-6">
                        @foreach($aktivitas as $a)
                            <li class="px-6 py-3 flex items-start gap-3 hover:bg-slate-50">
                                <div class="w-10 h-10 rounded-xl bg-maroon-100 text-maroon-700 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate">
                                        {{ $a->subKegiatan?->nama ?? '—' }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ $a->periode?->label }} • oleh {{ $a->creator?->nama ?? 'sistem' }}
                                    </p>
                                </div>
                                <span class="{{ $a->statusBadgeClass() }}">{{ $a->statusLabel() }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>
        </div>

        <x-ui.card title="Aksi Cepat">
            <div class="space-y-2">
                <a href="{{ route('evaluasi.index') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-maroon-300 hover:bg-maroon-50 transition group">
                    <div class="w-10 h-10 rounded-xl bg-brand-gradient text-white flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800">Input Evaluasi</p>
                        <p class="text-xs text-slate-500">Isi target & realisasi triwulan</p>
                    </div>
                </a>
                <a href="{{ route('form81.index') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-maroon-300 hover:bg-maroon-50 transition group">
                    <div class="w-10 h-10 rounded-xl bg-brand-gradient text-white flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800">Cetak Form 81</p>
                        <p class="text-xs text-slate-500">Preview & export ke Excel/PDF</p>
                    </div>
                </a>
                <a href="{{ route('subkegiatan.index') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-maroon-300 hover:bg-maroon-50 transition group">
                    <div class="w-10 h-10 rounded-xl bg-brand-gradient text-white flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800">Master Sub Kegiatan</p>
                        <p class="text-xs text-slate-500">Kelola daftar sub kegiatan</p>
                    </div>
                </a>
            </div>
        </x-ui.card>
    </div>

    {{-- ================= SCRIPT CHART ================= --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const el = document.getElementById('chartAnggaran');
            if (!el) return;
            const ctx = el.getContext('2d');
            const grad = ctx.createLinearGradient(0, 0, 0, 300);
            grad.addColorStop(0, 'rgba(139,30,48,.35)');
            grad.addColorStop(1, 'rgba(139,30,48,0)');

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
                        },
                        {
                            label: 'Realisasi (Rp Juta)',
                            data: @json($chart['realisasi']),
                            borderColor: '#8b1e30',
                            backgroundColor: grad,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointBackgroundColor: '#8b1e30',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } },
                        tooltip: {
                            callbacks: {
                                label: (c) => c.dataset.label + ': Rp ' + c.parsed.y.toLocaleString('id-ID') + ' Juta',
                            },
                        },
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString('id-ID') } },
                    },
                },
            });
        })();
    </script>
@endsection