@extends('layouts.public')

@section('title', 'Katalog Indikator — Bank Data & IKU RPJMD')
@section('meta_description', 'Jelajahi seluruh indikator pembangunan daerah Kabupaten Nias Barat — IKD Bank Data, IKU RPJMD, dan IKK secara terbuka dan transparan.')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════
     HERO BANNER INDIKATOR
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden text-white bg-[#741421]" style="min-height: 280px;">
    <div class="absolute inset-0 z-0 overflow-hidden">
        <img src="{{ asset('images/bappelitbangda-hero.jpg') }}"
             alt="Gedung Bappelitbangda Kabupaten Nias Barat"
             class="w-full h-full object-cover object-center"
             style="filter: brightness(0.46) contrast(1.10);"
             loading="eager" fetchpriority="high" decoding="sync">
        <div class="absolute inset-0"
             style="background: linear-gradient(105deg, rgba(116,20,33,0.98) 0%, rgba(116,20,33,0.90) 32%, rgba(116,20,33,0.60) 60%, rgba(116,20,33,0.20) 85%, transparent 100%);"></div>
        <div class="absolute inset-x-0 bottom-0 h-16"
             style="background: linear-gradient(to bottom, transparent 0%, rgba(116,20,33,0.88) 100%);"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        {{-- Breadcrumb --}}
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-white/60 hover:text-white/90 transition">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Beranda
            </a>
            <span class="text-white/30 text-xs">/</span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-[#D4A017]/20 text-[#FFDA65] border border-[#FFDA65]/35">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Katalog Indikator
            </span>
        </div>

        <div class="max-w-2xl">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-white leading-tight">
                Katalog Indikator
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#FFDA65] to-[#D4A017]">Pembangunan Daerah</span>
            </h1>
            <p class="mt-2.5 text-sm text-slate-200/80 leading-relaxed max-w-xl">
                Jelajahi seluruh indikator kinerja dan data pembangunan Kabupaten Nias Barat secara terbuka dan transparan — IKD Bank Data, IKU RPJMD, dan IKK Perangkat Daerah.
            </p>
        </div>

        {{-- Stat chips --}}
        <div class="mt-6 flex flex-wrap items-center gap-2.5 text-xs">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3.5 py-1.5 rounded-xl border border-white/15">
                <span class="w-1.5 h-1.5 rounded-full bg-[#FFDA65] animate-pulse shrink-0"></span>
                <span class="text-white/70">IKD Bank Data:</span>
                <strong class="text-white font-black">{{ number_format($counts['IKD']) }}</strong>
            </div>
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3.5 py-1.5 rounded-xl border border-white/15">
                <span class="text-white/70">IKU RPJMD:</span>
                <strong class="text-white font-black">{{ number_format($counts['IKU']) }}</strong>
            </div>
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3.5 py-1.5 rounded-xl border border-white/15">
                <span class="text-white/70">IKK:</span>
                <strong class="text-white font-black">{{ number_format($counts['IKK']) }}</strong>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     KONTEN UTAMA
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">

    {{-- ── Tabs jenis + Filter card ── --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_6px_28px_rgba(116,20,33,0.10)] -mt-6 mb-7 overflow-hidden">

        {{-- Tab Jenis Indikator --}}
        <div class="flex items-stretch border-b border-slate-100 overflow-x-auto">
            @foreach([
                'IKD' => ['label' => 'Bank Data IKD', 'icon' => 'M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7M4 7c0-2 1.5-3 3.5-3h9c2 0 3.5 1 3.5 3M4 7c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3M4 12c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3'],
                'IKU' => ['label' => 'IKU RPJMD', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                'IKK' => ['label' => 'IKK Perangkat Daerah', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            ] as $key => $tab)
                <a href="{{ route('indikator.publik', array_merge(request()->except(['jenis','page']), ['jenis' => $key])) }}"
                   class="inline-flex items-center gap-2 px-4 py-3.5 text-xs font-bold whitespace-nowrap border-b-2 transition-all
                          {{ $jenis === $key
                              ? 'border-[#AD1D30] text-[#AD1D30] bg-red-50/50'
                              : 'border-transparent text-slate-500 hover:text-[#AD1D30] hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $tab['icon'] }}"/>
                    </svg>
                    {{ $tab['label'] }}
                    <span class="ml-0.5 px-1.5 py-0.5 rounded-md text-[10px] font-black
                                 {{ $jenis === $key ? 'bg-[#861827] text-white' : 'bg-slate-100 text-slate-500' }}">
                        {{ number_format($counts[$key]) }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- Form Filter --}}
        <form method="GET" class="p-4 sm:p-5 flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="jenis" value="{{ $jenis }}">

            {{-- Search --}}
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="q" value="{{ $q }}"
                       placeholder="Cari nama indikator, program, atau kata kunci…"
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:border-[#AD1D30] focus:ring-2 focus:ring-[#AD1D30]/15 bg-slate-50/60 focus:bg-white outline-none transition">
            </div>

            {{-- Aspek / Kelompok --}}
            @if($aspekList->isNotEmpty())
            <select name="aspek" onchange="this.form.submit()"
                    class="py-2.5 px-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 focus:border-[#AD1D30] focus:ring-2 focus:ring-[#AD1D30]/15 bg-white outline-none transition sm:w-48">
                <option value="">Semua Aspek</option>
                @foreach($aspekList as $a)
                    <option value="{{ $a }}" @selected($aspek === $a)>{{ $a }}</option>
                @endforeach
            </select>
            @endif

            {{-- Tahun --}}
            <select name="tahun" onchange="this.form.submit()"
                    class="py-2.5 px-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 focus:border-[#AD1D30] focus:ring-2 focus:ring-[#AD1D30]/15 bg-white outline-none transition sm:w-32">
                @foreach($tahunRange as $y)
                    <option value="{{ $y }}" @selected($tahun === $y)>Tahun {{ $y }}</option>
                @endforeach
            </select>

            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-extrabold bg-gradient-to-r from-[#861827] to-[#AD1D30] hover:from-[#68121D] hover:to-[#861827] text-white shadow-sm hover:shadow-md transition-all shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span class="hidden sm:inline">Cari</span>
            </button>
        </form>
    </div>

    {{-- ── Info Jumlah Hasil ── --}}
    @if($indikatorList->isNotEmpty())
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-2">
            <span class="w-1 h-5 bg-[#D4A017] rounded-full"></span>
            <p class="text-sm font-bold text-[#741421]">
                {{ number_format($indikatorList->count()) }} indikator
                @if($q || $aspek)· <span class="font-normal text-slate-500">hasil filter</span>@endif
            </p>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Data Tahun <strong class="text-[#741421] ml-0.5">{{ $tahun }}</strong>
        </div>
    </div>
    @endif

    {{-- ── Tabel / Kartu Indikator ── --}}
    @if($indikatorList->isEmpty())
        <div class="bg-white rounded-2xl p-14 text-center border border-slate-200/90 shadow-xs">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 text-[#D4A017] flex items-center justify-center mx-auto mb-4 border border-amber-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-base font-extrabold text-[#741421]">Indikator Tidak Ditemukan</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Ubah kata kunci pencarian atau pilih aspek lain.</p>
            <a href="{{ route('indikator.publik', ['jenis' => $jenis]) }}"
               class="inline-flex items-center gap-1.5 mt-5 px-5 py-2.5 rounded-xl text-sm font-bold bg-[#861827] text-white hover:bg-[#68121D] transition">
                Reset Pencarian
            </a>
        </div>

    @else
        {{-- Tabel List --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/90 border-b border-slate-200/80">
                        <tr class="text-[10px] text-slate-500 uppercase tracking-wider font-bold">
                            <th class="px-4 py-3 w-8">#</th>
                            <th class="px-4 py-3">Nama Indikator</th>
                            @if($jenis !== 'IKD')
                                <th class="px-4 py-3 hidden md:table-cell">Perangkat Daerah</th>
                            @endif
                            <th class="px-4 py-3 hidden sm:table-cell">Aspek / Kelompok</th>
                            <th class="px-4 py-3 text-right">Nilai {{ $tahun }}</th>
                            <th class="px-4 py-3 text-right hidden lg:table-cell">Target {{ $tahun }}</th>
                            <th class="px-4 py-3 hidden sm:table-cell">Satuan</th>
                            <th class="px-4 py-3 hidden lg:table-cell">Sumber</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($indikatorList as $idx => $item)
                            @php
                                $point     = $item['points'][$tahun] ?? null;
                                $nilai     = $point['raw'] ?? '—';
                                $target    = $point['target'] ?? '—';
                                $satuan    = $item['satuan'] ?? '';
                                $aspekVal  = $item['aspek'] ?? ($item['kelompok'] ?? '—');
                                $opd_val   = $item['perangkat_daerah'] ?? '—';
                                $sumber    = $item['sumber'] ?? '—';
                                $isCapaian = ($point['source'] ?? '') === 'capaian';
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-4 py-3 text-slate-400 font-medium tabular-nums">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3 font-semibold text-[#21191A] max-w-xs">
                                    <div class="flex items-start gap-2">
                                        <span class="mt-0.5 w-1.5 h-1.5 rounded-full shrink-0 {{ $isCapaian ? 'bg-emerald-400' : 'bg-slate-300' }}"></span>
                                        <span class="leading-snug">{{ $item['nama'] }}</span>
                                    </div>
                                </td>
                                @if($jenis !== 'IKD')
                                    <td class="px-4 py-3 text-slate-500 hidden md:table-cell max-w-[180px]">
                                        <span class="truncate block" title="{{ $opd_val }}">{{ $opd_val !== '—' ? Str::limit($opd_val, 32) : '—' }}</span>
                                    </td>
                                @endif
                                <td class="px-4 py-3 hidden sm:table-cell">
                                    @if($aspekVal && $aspekVal !== '—')
                                        <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-[#D4A017] border border-amber-100">
                                            {{ Str::limit($aspekVal, 24) }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-extrabold tabular-nums
                                           {{ $isCapaian ? 'text-[#741421]' : 'text-slate-400' }}">
                                    {{ $nilai }}
                                    @if($satuan && $nilai !== '—')
                                        <span class="text-[10px] font-normal text-slate-400 ml-0.5">{{ $satuan }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-slate-400 tabular-nums hidden lg:table-cell">
                                    {{ $target }}
                                    @if($satuan && $target !== '—')
                                        <span class="text-[10px]">{{ $satuan }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-400 hidden sm:table-cell">{{ $satuan ?: '—' }}</td>
                                <td class="px-4 py-3 text-slate-400 hidden lg:table-cell text-[10px]">
                                    {{ $sumber !== '—' ? Str::limit($sumber, 28) : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer tabel --}}
            <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between gap-3 bg-slate-50/60">
                <p class="text-[11px] text-slate-400">
                    <span class="inline-flex items-center gap-1 mr-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Data capaian tersedia
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Belum ada capaian
                    </span>
                </p>
                <p class="text-[11px] text-slate-400 font-medium">Total: <strong class="text-[#741421]">{{ number_format($indikatorList->count()) }}</strong> indikator</p>
            </div>
        </div>

        {{-- Kembali ke Beranda --}}
        <div class="mt-8 text-center">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-[#AD1D30] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Beranda
            </a>
        </div>
    @endif

</section>

@endsection
