@extends('layouts.public')

@section('title', 'JDIH — Dokumentasi Hukum & Regulasi Pembangunan')
@section('meta_description', 'Jaringan Dokumentasi dan Informasi Hukum Bappelitbangda Kabupaten Nias Barat — RPJMD, RKPD, Renstra, Renja, Peraturan Daerah, dan Peraturan Bupati terkait perencanaan pembangunan daerah.')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════
     HERO BANNER JDIH
     Foto Bappelitbangda HQ + overlay navy, selaras dengan beranda
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden text-white bg-[#741421]" style="min-height: 300px;">
    <div class="absolute inset-0 z-0 overflow-hidden">
        <img src="{{ asset('images/bappelitbangda-hero.jpg') }}"
             alt="Gedung Bappelitbangda Kabupaten Nias Barat"
             class="w-full h-full object-cover object-center"
             style="filter: brightness(0.48) contrast(1.10);"
             loading="eager" fetchpriority="high" decoding="sync">
        {{-- Overlay navy ke kiri, tebal agar teks terbaca sempurna --}}
        <div class="absolute inset-0"
             style="background: linear-gradient(105deg, rgba(116,20,33,0.98) 0%, rgba(116,20,33,0.90) 32%, rgba(116,20,33,0.60) 58%, rgba(127,29,29,0.35) 82%, transparent 100%);"></div>
        {{-- Vignet bawah agar menyambung mulus ke konten --}}
        <div class="absolute inset-x-0 bottom-0 h-16"
             style="background: linear-gradient(to bottom, transparent 0%, rgba(116,20,33,0.90) 100%);"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        {{-- Breadcrumb / Badge --}}
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-white/60 hover:text-white/90 transition">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Beranda
            </a>
            <span class="text-white/30 text-xs">/</span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-[#D4A017]/20 text-[#FFDA65] border border-[#FFDA65]/35">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                JDIH
            </span>
        </div>

        {{-- Judul & Deskripsi --}}
        <div class="max-w-2xl">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-white leading-tight">
                Dokumentasi Hukum &
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#FFDA65] to-[#D4A017]">Produk Kebijakan</span>
            </h1>
            <p class="mt-2.5 text-sm text-slate-200/80 leading-relaxed max-w-xl">
                Akses keterbukaan informasi regulasi perencanaan pembangunan — RPJMD, RKPD, Renstra, Renja, Perda, dan Perbup secara terintegrasi dan akuntabel.
            </p>
        </div>

        {{-- Stat chips --}}
        <div class="mt-6 flex flex-wrap items-center gap-2.5 text-xs">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3.5 py-1.5 rounded-xl border border-white/15">
                <span class="w-1.5 h-1.5 rounded-full bg-[#FFDA65] animate-pulse shrink-0"></span>
                <span class="text-white/70">Total Dokumen:</span>
                <strong class="text-white font-black">{{ $items->total() }}</strong>
            </div>
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3.5 py-1.5 rounded-xl border border-white/15">
                <span class="text-white/70">Kategori:</span>
                <strong class="text-white font-black">{{ count($kategoriList) }} Jenis</strong>
            </div>
            @if(count($tahunList))
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3.5 py-1.5 rounded-xl border border-white/15">
                <span class="text-white/70">Tahun Arsip:</span>
                <strong class="text-white font-black">{{ collect($tahunList)->min() }} – {{ collect($tahunList)->max() }}</strong>
            </div>
            @endif
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     KONTEN UTAMA (Filter + Daftar Dokumen)
     Floating card naik sedikit menutupi ujung hero
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">

    {{-- ── Filter Card (floating, naik ke atas hero) ── --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_6px_28px_rgba(116,20,33,0.10)] -mt-6 mb-7 p-4 sm:p-5">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="q" value="{{ $q }}"
                       placeholder="Cari judul dokumen, nomor regulasi, atau kata kunci…"
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:border-[#AD1D30] focus:ring-2 focus:ring-[#AD1D30]/15 bg-slate-50/60 focus:bg-white outline-none transition">
            </div>

            {{-- Kategori --}}
            <select name="kategori" onchange="this.form.submit()"
                    class="py-2.5 px-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 focus:border-[#AD1D30] focus:ring-2 focus:ring-[#AD1D30]/15 bg-white outline-none transition sm:w-52">
                <option value="">Semua Kategori</option>
                @foreach($kategoriList as $key => $label)
                    <option value="{{ $key }}" @selected($kategori === $key)>{{ $label }}</option>
                @endforeach
            </select>

            {{-- Tahun --}}
            <select name="tahun" onchange="this.form.submit()"
                    class="py-2.5 px-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 focus:border-[#AD1D30] focus:ring-2 focus:ring-[#AD1D30]/15 bg-white outline-none transition sm:w-36">
                <option value="">Semua Tahun</option>
                @foreach($tahunList as $y)
                    <option value="{{ $y }}" @selected((string) $tahun === (string) $y)>{{ $y }}</option>
                @endforeach
            </select>

            {{-- Submit --}}
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-extrabold bg-gradient-to-r from-[#861827] to-[#AD1D30] hover:from-[#68121D] hover:to-[#861827] text-white shadow-sm hover:shadow-md transition-all shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span class="hidden sm:inline">Cari</span>
            </button>
        </form>

        {{-- Active filter chips --}}
        @if($q || $kategori || $tahun)
            <div class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2 text-xs">
                <span class="text-slate-500 font-medium">Filter aktif:</span>
                @if($q)
                    <span class="inline-flex items-center gap-1 bg-red-50 text-[#AD1D30] font-semibold px-2.5 py-1 rounded-lg border border-red-100">
                        "{{ $q }}"
                        <a href="{{ route('jdih.index', array_merge(request()->except('q'), ['kategori' => $kategori, 'tahun' => $tahun])) }}" class="ml-0.5 opacity-60 hover:opacity-100">✕</a>
                    </span>
                @endif
                @if($kategori)
                    <span class="inline-flex items-center gap-1 bg-amber-50 text-[#D4A017] font-semibold px-2.5 py-1 rounded-lg border border-amber-100">
                        {{ $kategoriList[$kategori] ?? $kategori }}
                        <a href="{{ route('jdih.index', array_merge(request()->except('kategori'), ['q' => $q, 'tahun' => $tahun])) }}" class="ml-0.5 opacity-60 hover:opacity-100">✕</a>
                    </span>
                @endif
                @if($tahun)
                    <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-700 font-semibold px-2.5 py-1 rounded-lg border border-slate-200">
                        Tahun {{ $tahun }}
                        <a href="{{ route('jdih.index', array_merge(request()->except('tahun'), ['q' => $q, 'kategori' => $kategori])) }}" class="ml-0.5 opacity-60 hover:opacity-100">✕</a>
                    </span>
                @endif
                <a href="{{ route('jdih.index') }}" class="ml-auto font-bold text-[#AD1D30] hover:underline flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reset Semua
                </a>
            </div>
        @endif
    </div>

    {{-- ── Jumlah hasil + judul kolom ── --}}
    @if(!$items->isEmpty())
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2">
                <span class="w-1 h-5 bg-[#D4A017] rounded-full inline-block"></span>
                <p class="text-sm font-bold text-[#741421]">
                    {{ number_format($items->total()) }} dokumen ditemukan
                    @if($q || $kategori || $tahun)
                        <span class="font-normal text-slate-500">· hasil filter</span>
                    @endif
                </p>
            </div>
            <span class="text-xs text-slate-400 font-medium">Hal. {{ $items->currentPage() }} / {{ $items->lastPage() }}</span>
        </div>
    @endif

    {{-- ── Daftar Dokumen ── --}}
    @if($items->isEmpty())
        <div class="bg-white rounded-2xl p-14 text-center border border-slate-200/90 shadow-xs">
            <div class="w-16 h-16 rounded-2xl bg-red-50 text-[#AD1D30] flex items-center justify-center mx-auto mb-4 border border-red-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="text-base font-extrabold text-[#741421]">Tidak Ada Dokumen Ditemukan</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 leading-relaxed">
                Coba ubah kata kunci, bersihkan filter kategori atau tahun untuk melihat semua dokumen yang tersedia.
            </p>
            <a href="{{ route('jdih.index') }}"
               class="inline-flex items-center gap-1.5 mt-5 px-5 py-2.5 rounded-xl text-sm font-bold bg-[#861827] text-white hover:bg-[#68121D] transition">
                Lihat Semua Dokumen
            </a>
        </div>

    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            @foreach($items as $doc)
                <article class="bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col group overflow-hidden">

                    {{-- Accent strip warna berdasar kategori --}}
                    <div class="h-1 w-full
                        @if(str_contains(strtolower($doc->kategori ?? ''), 'rpjmd') || str_contains(strtolower($doc->kategori ?? ''), 'iku'))
                            bg-gradient-to-r from-[#861827] to-[#AD1D30]
                        @elseif(str_contains(strtolower($doc->kategori ?? ''), 'rkpd') || str_contains(strtolower($doc->kategori ?? ''), 'renja'))
                            bg-gradient-to-r from-[#D4A017] to-[#FFDA65]
                        @elseif(str_contains(strtolower($doc->kategori ?? ''), 'perda') || str_contains(strtolower($doc->kategori ?? ''), 'peraturan'))
                            bg-gradient-to-r from-[#741421] to-[#AD1D30]
                        @else
                            bg-gradient-to-r from-slate-300 to-slate-200
                        @endif
                    "></div>

                    <div class="p-5 flex flex-col flex-1">
                        {{-- Header: Kategori badge + Tahun --}}
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-red-50 text-[#AD1D30] border border-red-100 leading-none">
                                {{ $doc->kategoriLabel() }}
                            </span>
                            @if($doc->tahun)
                                <span class="text-[11px] font-bold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200 shrink-0 leading-none">
                                    {{ $doc->tahun }}
                                </span>
                            @endif
                        </div>

                        {{-- Judul --}}
                        <h3 class="font-extrabold text-[#741421] text-sm leading-snug group-hover:text-[#AD1D30] transition-colors mb-auto">
                            {{ $doc->judul }}
                        </h3>

                        {{-- Meta: Nomor --}}
                        @if($doc->nomor)
                            <div class="flex items-center gap-1.5 mt-3 text-[11px] text-slate-500 font-medium">
                                <svg class="w-3.5 h-3.5 text-[#D4A017] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                                <span class="truncate">No. <strong class="text-slate-700">{{ $doc->nomor }}</strong></span>
                            </div>
                        @endif

                        {{-- Ringkasan --}}
                        @if($doc->ringkasan)
                            <p class="text-[11px] text-slate-400 mt-2 leading-relaxed line-clamp-2">{{ $doc->ringkasan }}</p>
                        @endif

                        {{-- Footer: Tanggal + Tombol Unduh --}}
                        <div class="mt-4 pt-3.5 border-t border-slate-100 flex items-center justify-between gap-2">
                            <span class="text-[10px] text-slate-400 font-medium flex items-center gap-1 min-w-0">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="truncate">{{ $doc->updated_at ? $doc->updated_at->diffForHumans() : 'Tersedia' }}</span>
                            </span>

                            @if($doc->file_url)
                                <a href="{{ $doc->file_url }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold text-[#AD1D30] bg-red-50 hover:bg-[#861827] hover:text-white transition-all border border-red-100 shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Unduh
                                </a>
                            @else
                                <span class="text-[10px] text-slate-300 font-medium italic shrink-0">Segera Diunggah</span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $items->links() }}
        </div>
    @endif

</section>

@endsection
