@extends('layouts.public')

@section('title', 'SICERAH – Data Pembangunan Nias Barat CERAH')
@section('meta_description', 'Portal SICERAH Bappelitbangda Kabupaten Nias Barat, Bumi Aekhula. Akses data pembangunan, RPJMD, IKU, IKD, statistik BPS, WebGIS, dan publikasi daerah.')

@section('content')
<div x-data="indicatorExplorer()" class="space-y-8">

    {{-- =========================================================================
       1. HERO SECTION (BANNER HORIZONTAL + 4 STATISTIK MENGAMBANG)
       ========================================================================= --}}
    @include('landing._portal-hero')
    @include('landing._about-sicerah')
    @include('landing._konvergensi')
    @include('landing._bps-publications')


    {{-- =========================================================================
       2. LAYANAN & MODUL UTAMA SICERAH (4 KARTU HORIZONTAL)
       ========================================================================= --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        {{-- Section Title dengan Aksen Emas --}}
        <div class="flex items-center gap-2.5 mb-4">
            <span class="w-1.5 h-6 bg-[#D4A017] rounded-full inline-block"></span>
            <h2 class="text-lg sm:text-xl font-black text-[#191716] tracking-tight">Layanan & Modul Utama SICERAH</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Kartu 1: Bank Data IKD (Active / Featured dengan Gradasi Merah Elegan) --}}
            <a href="#katalog-indikator" class="bg-gradient-to-r from-[#861827] via-[#68121D] to-[#191716] text-white rounded-2xl p-4.5 sm:p-5 shadow-md hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-between gap-3 group border border-red-700/50">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 text-[#FFDA65] border border-white/15 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7M4 7c0-2 1.5-3 3.5-3h9c2 0 3.5 1 3.5 3M4 7c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3M4 12c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-white text-sm sm:text-[15px] leading-snug">Bank Data IKD</h3>
                        <p class="text-[11px] text-slate-200/90 leading-tight mt-1 line-clamp-2">
                            Akses integrasi data pembangunan daerah (IKD) yang terstandar, mutakhir, dan lintas-perangkat daerah.
                        </p>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-full bg-white/15 group-hover:bg-[#FFDA65] group-hover:text-[#121212] text-white flex items-center justify-center shrink-0 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>

            {{-- Kartu 2: Indikator Makro --}}
            <a href="#ringkasan-makro" class="bg-white rounded-2xl p-4.5 sm:p-5 border border-slate-200/90 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-between gap-3 group">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-[#D4A017] border border-amber-200/60 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-[#191716] text-sm sm:text-[15px] leading-snug group-hover:text-[#AD1D30] transition-colors">Indikator Makro</h3>
                        <p class="text-[11px] text-slate-500 leading-tight mt-1 line-clamp-2">
                            Pantau indikator makro strategis untuk melihat capaian, tren, dan posisi pembangunan.
                        </p>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-full bg-slate-100 group-hover:bg-[#AD1D30] group-hover:text-white text-slate-500 flex items-center justify-center shrink-0 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>

            {{-- Kartu 3: Peta WebGIS --}}
            <a href="{{ route('webgis.index') }}" class="bg-white rounded-2xl p-4.5 sm:p-5 border border-slate-200/90 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-between gap-3 group">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-[#D4A017] border border-amber-200/60 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-[#191716] text-sm sm:text-[15px] leading-snug group-hover:text-[#AD1D30] transition-colors">Peta WebGIS</h3>
                        <p class="text-[11px] text-slate-500 leading-tight mt-1 line-clamp-2">
                            Jelajahi data spasial pembangunan daerah berbasis peta interaktif Kabupaten Nias Barat.
                        </p>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-full bg-slate-100 group-hover:bg-[#AD1D30] group-hover:text-white text-slate-500 flex items-center justify-center shrink-0 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>

            {{-- Kartu 4: Dokumen JDIH --}}
            <a href="{{ route('jdih.index') }}" class="bg-white rounded-2xl p-4.5 sm:p-5 border border-slate-200/90 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-between gap-3 group">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-[#D4A017] border border-amber-200/60 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-[#191716] text-sm sm:text-[15px] leading-snug group-hover:text-[#AD1D30] transition-colors">Dokumen JDIH</h3>
                        <p class="text-[11px] text-slate-500 leading-tight mt-1 line-clamp-2">
                            Akses produk hukum daerah, regulasi perencanaan, dan dokumen strategis pembangunan.
                        </p>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-full bg-slate-100 group-hover:bg-[#AD1D30] group-hover:text-white text-slate-500 flex items-center justify-center shrink-0 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
        </div>
    </section>

    {{-- =========================================================================
       3. PEMBAGIAN LAYOUT DUA KOLOM:
          AREA KIRI (65%): KATALOG INDIKATOR DAERAH
          AREA KANAN (35%): RINGKASAN MAKRO DAERAH & REALISASI PROGRAM
       ========================================================================= --}}
    <section id="katalog-indikator" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 scroll-mt-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- =====================================================
               KOLOM KIRI (65% - lg:col-span-8): JELAJAHI INDIKATOR DAERAH
               ===================================================== --}}
            <div class="lg:col-span-8 space-y-4">
                {{-- Header Katalog --}}
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-1.5 h-6 bg-[#D4A017] rounded-full inline-block"></span>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black text-[#191716] tracking-tight">Jelajahi Indikator Daerah</h2>
                            <p class="text-xs text-slate-500">Temukan dan telusuri <span class="font-bold text-[#191716]" x-text="items.length"></span> indikator pembangunan daerah Kabupaten Nias Barat.</p>
                        </div>
                    </div>
                    <button type="button" @click="resetFilters()" class="text-xs font-bold text-[#AD1D30] hover:text-[#68121D] hover:underline flex items-center gap-1">
                        <span>Lihat Semua Indikator</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>

                {{-- Toolbar Pencarian & Filter Kategori (Sesuai Referensi Gambar) --}}
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-3.5">
                    {{-- Baris 1: Input Search + Category Filter Pills --}}
                    <div class="flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
                        {{-- Search Input --}}
                        <div class="relative flex-1">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg>
                            <input x-model.debounce.200ms="search" type="search" placeholder="Cari indikator, program, data, atau kata kunci lainnya..."
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-[#AD1D30] focus:ring-2 focus:ring-[#AD1D30]/15 placeholder:text-slate-400 font-medium">
                        </div>

                        {{-- Kategori Pills (Merah & Emas Elegan) --}}
                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 shrink-0">
                            <span class="text-xs font-bold text-slate-500 mr-1 hidden sm:inline">Kategori:</span>
                            
                            {{-- Semua --}}
                            <button type="button" @click="category='all'; page=1"
                                    :class="category==='all' ? 'bg-[#861827] text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                                    class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all flex items-center gap-1">
                                <span x-show="category==='all'">✓</span>
                                <span>Semua</span>
                            </button>

                            {{-- Ekonomi --}}
                            <button type="button" @click="category='ekonomi'; page=1"
                                    :class="category==='ekonomi' ? 'bg-[#861827] text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                                    class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                <span>Ekonomi</span>
                            </button>

                            {{-- Sosial --}}
                            <button type="button" @click="category='sosial'; page=1"
                                    :class="category==='sosial' ? 'bg-[#861827] text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                                    class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <span>Sosial</span>
                            </button>

                            {{-- Pendidikan --}}
                            <button type="button" @click="category='pendidikan'; page=1"
                                    :class="category==='pendidikan' ? 'bg-[#861827] text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                                    class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5"/></svg>
                                <span>Pendidikan</span>
                            </button>

                            {{-- Kesehatan --}}
                            <button type="button" @click="category='kesehatan'; page=1"
                                    :class="category==='kesehatan' ? 'bg-[#861827] text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                                    class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                <span>Kesehatan</span>
                            </button>

                            {{-- Dropdown Lainnya --}}
                            <div class="relative" x-data="{ openOther: false }" @click.outside="openOther = false">
                                <button type="button" @click="openOther = !openOther"
                                        :class="['infrastruktur','lingkungan','pemerintahan'].includes(category) ? 'bg-[#861827] text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                                        class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all flex items-center gap-1">
                                    <span>Lainnya</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="openOther" x-cloak class="absolute right-0 mt-1 w-44 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 z-30 text-xs">
                                    <button type="button" @click="category='infrastruktur'; page=1; openOther=false" class="w-full text-left px-3 py-2 hover:bg-red-50/60 text-slate-700">Infrastruktur & PU</button>
                                    <button type="button" @click="category='lingkungan'; page=1; openOther=false" class="w-full text-left px-3 py-2 hover:bg-red-50/60 text-slate-700">Lingkungan Hidup</button>
                                    <button type="button" @click="category='pemerintahan'; page=1; openOther=false" class="w-full text-left px-3 py-2 hover:bg-red-50/60 text-slate-700">Tata Kelola & Umum</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Baris 2: Toolbar Tampilan (Grid/Daftar) + Urutkan + Pilihan Tahun --}}
                    <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                        {{-- Switch Tampilan --}}
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-500">Tampilan:</span>
                            <div class="inline-flex rounded-xl p-0.5 bg-slate-100 border border-slate-200/80">
                                <button type="button" @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-[#861827] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="px-2.5 py-1 rounded-lg font-bold flex items-center gap-1 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    <span>Grid</span>
                                </button>
                                <button type="button" @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-[#861827] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="px-2.5 py-1 rounded-lg font-bold flex items-center gap-1 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                    <span>Daftar</span>
                                </button>
                            </div>
                        </div>

                        {{-- Urutkan Dropdown & Jumlah Per Halaman --}}
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-slate-500">Urutkan:</span>
                                <select x-model="sortBy" class="rounded-xl border border-slate-200 py-1 px-2.5 text-xs text-[#121212] font-semibold bg-white focus:border-[#AD1D30] focus:ring-0">
                                    <option value="default">Capaian Terbaru</option>
                                    <option value="name_asc">Nama A-Z</option>
                                    <option value="name_desc">Nama Z-A</option>
                                    <option value="highest">Nilai Tertinggi</option>
                                </select>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <select x-model.number="perPage" @change="page = 1" class="rounded-xl border border-slate-200 py-1 px-2 text-xs text-[#121212] font-semibold bg-white focus:border-[#AD1D30] focus:ring-0">
                                    <option value="6">6 / hal</option>
                                    <option value="12">12 / hal</option>
                                    <option value="24">24 / hal</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- =====================================================
                   KARTU INDIKATOR (3 KOLOM X 2 BARIS = 6 KARTU AWAL)
                   ===================================================== --}}
                
                {{-- Mode Tampilan GRID --}}
                <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    <template x-for="item in paginated" :key="item.id">
                        <div @click="openIndicator(item)"
                             class="bg-white rounded-2xl p-4 sm:p-4.5 border border-slate-200/90 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between cursor-pointer group">
                            
                            {{-- Header Kartu: Icon Kategori + Nama Indikator + Tombol Action --}}
                            <div>
                                <div class="flex items-start justify-between gap-2.5">
                                    <div class="flex items-center gap-2">
                                        {{-- Icon Berdasarkan Kategori --}}
                                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                                             :class="getIconBgClass(item)">
                                            <span x-html="getCategoryIcon(item)"></span>
                                        </div>
                                        <h3 class="text-xs sm:text-[13px] font-black text-[#121212] group-hover:text-[#AD1D30] transition-colors leading-snug line-clamp-2"
                                            x-text="item.nama"></h3>
                                    </div>
                                    <button type="button" @click.stop="openIndicator(item)" class="text-slate-400 hover:text-slate-700 p-0.5" title="Detail Indikator">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                                    </button>
                                </div>

                                {{-- Tahun Data --}}
                                <p class="text-[11px] font-semibold text-slate-400 mt-2">
                                    Tahun <span x-text="activeYear"></span>
                                </p>

                                {{-- Nilai Utama & Badge Delta Perubahan --}}
                                <div class="mt-1 flex items-baseline gap-2.5 flex-wrap">
                                    <span class="text-xl sm:text-2xl font-black text-[#191716] tracking-tight tabular-nums"
                                          x-text="getDisplayValue(item)"></span>
                                    
                                    {{-- Badge Delta Ketercapaian --}}
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                                          :class="getDeltaClass(item)"
                                          x-html="getDeltaBadge(item)"></span>
                                </div>
                            </div>

                            {{-- Mini Sparkline Tren Grafik --}}
                            <div class="mt-3">
                                <div class="w-full h-10">
                                    <svg class="w-full h-full overflow-visible" viewBox="0 0 160 40" preserveAspectRatio="none">
                                        <defs>
                                            <linearGradient :id="'sparkGrad-' + item.id" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="0%" :stop-color="getSparkColor(item)" stop-opacity="0.3"></stop>
                                                <stop offset="100%" :stop-color="getSparkColor(item)" stop-opacity="0.0"></stop>
                                            </linearGradient>
                                        </defs>
                                        {{-- Area bawah kurva --}}
                                        <path :d="getSparkAreaPath(item)" :fill="'url(#sparkGrad-' + item.id + ')'"></path>
                                        {{-- Garis kurva sparkline --}}
                                        <path :d="getSparkLinePath(item)" fill="none" :stroke="getSparkColor(item)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </div>

                                {{-- Footer Kartu: Sumber Data --}}
                                <div class="mt-2 pt-2 border-t border-slate-100 flex items-center gap-1.5 text-[10px] text-slate-400 font-medium truncate">
                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <span class="truncate" x-text="getSourceText(item)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Mode Tampilan DAFTAR (LIST) --}}
                <div x-show="viewMode === 'list'" class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider text-[10px] font-bold border-b border-slate-200/80">
                                <tr>
                                    <th class="px-4 py-3">Indikator</th>
                                    <th class="px-4 py-3">Aspek / Kategori</th>
                                    <th class="px-4 py-3">Nilai Tahun {{ $tahun }}</th>
                                    <th class="px-4 py-3">Perubahan</th>
                                    <th class="px-4 py-3">Sumber Data</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="item in paginated" :key="item.id">
                                    <tr class="hover:bg-slate-50/80 transition-colors cursor-pointer" @click="openIndicator(item)">
                                        <td class="px-4 py-3 font-bold text-[#121212] max-w-xs" x-text="item.nama"></td>
                                        <td class="px-4 py-3 text-slate-500 font-medium" x-text="shortAspect(item.aspek)"></td>
                                        <td class="px-4 py-3 font-extrabold text-[#191716]" x-text="getDisplayValue(item)"></td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold"
                                                  :class="getDeltaClass(item)"
                                                  x-html="getDeltaBadge(item)"></span>
                                        </td>
                                        <td class="px-4 py-3 text-slate-400 text-[11px]" x-text="getSourceText(item)"></td>
                                        <td class="px-4 py-3 text-right">
                                            <button type="button" @click.stop="openIndicator(item)" class="text-xs font-bold text-[#AD1D30] hover:underline">Detail →</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Empty State jika Pencarian Tidak Ditemukan --}}
                <div x-show="filtered.length === 0" class="bg-white rounded-2xl p-10 text-center border border-slate-200/90 shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-[#D4A017] mx-auto flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="font-extrabold text-[#121212] text-sm">Indikator tidak ditemukan</p>
                    <p class="text-xs text-slate-500 mt-1">Ubah kata kunci pencarian atau pilih kategori lain.</p>
                    <button type="button" @click="resetFilters()" class="mt-4 px-4 py-2 rounded-xl bg-[#191716] text-white text-xs font-bold">Reset Pencarian</button>
                </div>

                {{-- =====================================================
                   PAGINATION KATALOG INDIKATOR (Sesuai Referensi Gambar)
                   ===================================================== --}}
                <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <p class="text-slate-500 font-medium">
                        Menampilkan <span class="font-bold text-[#191716]" x-text="paginationStart"></span>-<span class="font-bold text-[#191716]" x-text="paginationEnd"></span> dari <span class="font-bold text-[#191716]" x-text="filtered.length"></span> indikator
                    </p>

                    {{-- Tombol Halaman --}}
                    <div class="flex items-center gap-1">
                        {{-- First & Prev --}}
                        <button type="button" @click="page = 1" :disabled="page === 1" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-slate-100 transition font-bold">«</button>
                        <button type="button" @click="page--" :disabled="page === 1" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-slate-100 transition font-bold">‹</button>

                        {{-- Page Number Pills --}}
                        <template x-for="p in visiblePages" :key="p">
                            <button type="button" @click="if(p !== '...') page = p"
                                    :class="page === p ? 'bg-[#861827] text-white font-bold border-[#861827]' : (p === '...' ? 'border-transparent text-slate-400 cursor-default' : 'border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold')"
                                    class="w-8 h-8 rounded-lg border flex items-center justify-center text-xs transition"
                                    x-text="p"></button>
                        </template>

                        {{-- Next & Last --}}
                        <button type="button" @click="page++" :disabled="page === totalPages" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-slate-100 transition font-bold">›</button>
                        <button type="button" @click="page = totalPages" :disabled="page === totalPages" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-slate-100 transition font-bold">»</button>
                    </div>
                </div>
            </div>

            {{-- =====================================================
               KOLOM KANAN (35% - lg:col-span-4):
               1. RINGKASAN MAKRO DAERAH
               2. REALISASI PROGRAM PEMBANGUNAN
               ===================================================== --}}
            <div class="lg:col-span-4 space-y-6">

                {{-- ================= CARD 1: RINGKASAN MAKRO DAERAH ================= --}}
                <div id="ringkasan-makro" class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs">
                    {{-- Header Ringkasan Makro dengan Dropdown Tahun --}}
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-amber-50 text-[#D4A017] flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <h3 class="font-black text-[#191716] text-sm">Ringkasan Makro Daerah</h3>
                        </div>

                        {{-- Dropdown Pilihan Tahun --}}
                        <form method="GET" action="{{ route('landing') }}">
                            <select name="tahun" onchange="this.form.submit()" class="rounded-xl border border-slate-200 py-1 px-2.5 text-xs text-[#121212] font-bold bg-white focus:border-[#AD1D30] focus:ring-0">
                                @foreach($tahunRange as $y)
                                    <option value="{{ $y }}" @selected($tahun === $y)>Tahun {{ $y }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    {{-- List Indikator Makro Strategis --}}
                    <div class="divide-y divide-slate-100">
                        @foreach($ringkasanMakro as $key => $m)
                            <div class="py-3 flex items-center justify-between gap-3 group hover:bg-slate-50/50 -mx-2 px-2 rounded-xl transition">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    {{-- Icon Indikator --}}
                                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                                        @if($m['icon'] === 'user')
                                            <svg class="w-3.5 h-3.5 text-[#AD1D30]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        @elseif($m['icon'] === 'trending-up')
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                        @elseif($m['icon'] === 'scale')
                                            <svg class="w-3.5 h-3.5 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                                        @elseif($m['icon'] === 'shield-alert')
                                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        @endif
                                    </div>
                                    <div class="truncate">
                                        <p class="text-xs font-bold text-[#121212] truncate">{{ $m['label'] }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium">Target/Capaian {{ $tahun }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    {{-- Nilai Besar --}}
                                    <div class="text-right">
                                        <p class="text-sm font-black text-[#191716] tracking-tight tabular-nums">
                                            {{ $m['nilai'] }} <span class="text-[10px] font-semibold text-slate-400">{{ $m['satuan'] }}</span>
                                        </p>
                                        
                                        {{-- Badge Status Berdasarkan Arah Capaian (Sesuai Permintaan User Poin 7) --}}
                                        @if($m['delta'])
                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-extrabold px-1.5 py-0.2 rounded {{ $m['is_good'] ? 'text-amber-700 bg-amber-50' : 'text-rose-700 bg-rose-50' }}">
                                                {{ $m['delta_raw'] >= 0 ? '▲' : '▼' }} {{ $m['delta'] }}
                                            </span>
                                        @else
                                            <span class="text-[10px] text-slate-400">Baseline</span>
                                        @endif
                                    </div>

                                    {{-- Mini Sparkline SVG --}}
                                    <div class="w-16 h-6 shrink-0 hidden sm:block">
                                        @php
                                            $pts = $m['sparkline'];
                                            $minP = count($pts) > 0 ? min($pts) : 0;
                                            $maxP = count($pts) > 0 ? max($pts) : 1;
                                            $rangeP = max(0.001, $maxP - $minP);
                                            $svgPoints = [];
                                            foreach($pts as $idx => $val) {
                                                $x = round(($idx / max(1, count($pts) - 1)) * 60, 1);
                                                $y = round(22 - (($val - $minP) / $rangeP) * 18, 1);
                                                $svgPoints[] = "$x,$y";
                                            }
                                            $polyLine = implode(' ', $svgPoints);
                                        @endphp
                                        <svg class="w-full h-full overflow-visible" viewBox="0 0 64 24">
                                            <polyline fill="none" stroke="{{ $m['is_good'] ? '#B38412' : '#e11d48' }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" points="{{ $polyLine }}"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ================= CARD 2: REALISASI PROGRAM PEMBANGUNAN ================= --}}
                <div id="kinerja" class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs space-y-4">
                    {{-- Header Realisasi Program dengan Dropdown Tahun --}}
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-amber-50 text-[#D4A017] flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-black text-[#191716] text-sm">Realisasi Program {{ $tahun }}</h3>
                            </div>
                        </div>

                        {{-- Dropdown Tahun --}}
                        <form method="GET" action="{{ route('landing') }}">
                            <select name="tahun" onchange="this.form.submit()" class="rounded-xl border border-slate-200 py-1 px-2.5 text-xs text-[#121212] font-bold bg-white focus:border-[#AD1D30] focus:ring-0">
                                @foreach($tahunRange as $y)
                                    <option value="{{ $y }}" @selected($tahun === $y)>Tahun {{ $y }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <p class="text-xs text-slate-500 leading-normal">
                        Evaluasi kinerja pelaksanaan program pembangunan daerah Tahun {{ $tahun }}.
                    </p>

                    @if($kinerja['has_data'])
                        {{-- 2 Donut / Circular Gauge Chart (Fisik vs Keuangan) --}}
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            {{-- Donut Rata-rata Fisik (Hijau Emerald) --}}
                            <div class="bg-slate-50/70 rounded-xl p-3.5 text-center flex flex-col items-center">
                                <div class="relative w-20 h-20 flex items-center justify-center">
                                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                        <path class="text-slate-200" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                        <path class="text-amber-500 transition-all duration-1000 ease-out" stroke-dasharray="{{ min(100, $kinerja['rata_fisik']) }}, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                    </svg>
                                    <span class="absolute text-sm font-black text-[#121212]">{{ number_format($kinerja['rata_fisik'], 1, ',', '.') }}%</span>
                                </div>
                                <p class="text-xs font-bold text-slate-700 mt-2">Rata-rata Fisik</p>
                            </div>

                            {{-- Donut Rata-rata Keuangan (Merah Crimson Elegan) --}}
                            <div class="bg-slate-50/70 rounded-xl p-3.5 text-center flex flex-col items-center">
                                <div class="relative w-20 h-20 flex items-center justify-center">
                                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                        <path class="text-slate-200" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                        <path class="text-[#DC2626] transition-all duration-1000 ease-out" stroke-dasharray="{{ min(100, $kinerja['rata_keuangan']) }}, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                    </svg>
                                    <span class="absolute text-sm font-black text-[#861827]">{{ number_format($kinerja['rata_keuangan'], 1, ',', '.') }}%</span>
                                </div>
                                <p class="text-xs font-bold text-slate-700 mt-2">Rata-rata Keuangan</p>
                            </div>
                        </div>

                        {{-- Line Chart Interaktif: Tren Realisasi Kumulatif --}}
                        <div class="pt-3 border-t border-slate-100">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <p class="text-xs font-black text-[#191716]">Tren Realisasi Kumulatif {{ $tahun }}</p>
                                <div class="flex items-center gap-3 text-[10px] font-bold">
                                    <span class="inline-flex items-center gap-1 text-amber-700">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Fisik
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-[#DC2626]">
                                        <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span> Keuangan
                                    </span>
                                </div>
                            </div>
                            <div class="h-44 relative">
                                <canvas id="chartKumulatifLanding"></canvas>
                            </div>
                        </div>
                    @else
                        {{-- Empty State jika data tahun terkait belum diinput (Sesuai Arahan User Poin 8) --}}
                        <div class="rounded-xl bg-slate-50 border border-slate-200/80 p-5 text-center my-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-[#D4A017] mx-auto flex items-center justify-center mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-xs font-extrabold text-[#121212]">Data Evaluasi {{ $tahun }} Belum Tersedia</p>
                            <p class="text-[11px] text-slate-500 mt-1 leading-normal">
                                Pelaksanaan evaluasi program pembangunan tahun {{ $tahun }} belum diinput oleh perangkat daerah.
                            </p>
                            <a href="{{ route('landing', ['tahun' => 2025]) }}" class="inline-block mt-3 px-3.5 py-1.5 rounded-lg bg-[#861827] text-white text-[11px] font-bold hover:bg-[#68121D] transition">
                                Lihat Data Tahun 2025 →
                            </a>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>

    {{-- =========================================================================
       4. SECTION PROFIL BAPPELITBANGDA KABUPATEN NIAS BARAT (SEBELUMNYA KOSONG)
       ========================================================================= --}}
    <section id="profil" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-4 scroll-mt-24">
        {{-- Section Header --}}
        <div class="flex items-center gap-2.5 mb-6">
            <span class="w-1.5 h-6 bg-[#AD1D30] rounded-full inline-block"></span>
            <div>
                <h2 class="text-lg sm:text-2xl font-black text-[#191716] tracking-tight">Profil BAPPELITBANGDA Kabupaten Nias Barat</h2>
                <p class="text-xs sm:text-sm text-slate-500">Badan Perencanaan Pembangunan, Penelitian, dan Pengembangan Daerah Kabupaten Nias Barat</p>
            </div>
        </div>

        {{-- Hero Banner Profil Bappelitbangda --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#68121D] via-[#AD1D30] to-[#861827] text-white p-6 sm:p-10 lg:p-12 shadow-xl border border-[#D4A017]/40">
            <div class="absolute -right-10 -bottom-10 w-96 h-96 rounded-full bg-[#D4A017]/10 blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-8 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur border border-white/20 text-[#FFDA65] text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-[#FFDA65] animate-pulse"></span>
                        <span>Perencanaan Pembangunan · Riset · Inovasi Daerah</span>
                    </div>

                    <h3 class="text-xl sm:text-3xl font-black tracking-tight leading-snug">
                        Mewujudkan Perencanaan Pembangunan yang Terpadu, Akuntabel, dan Berdampak Menuju Nias Barat Sejahtera.
                    </h3>

                    <p class="text-xs sm:text-sm text-slate-200/90 leading-relaxed font-normal">
                        BAPPELITBANGDA Kabupaten Nias Barat merupakan lembaga penunjang urusan pemerintahan daerah yang bertugas memformulasikan kebijakan perencanaan makro, mengoordinasikan pembangunan lintas-sektoral, menyelenggarakan penelitian dan pengembangan inovasi, serta memantau dan mengevaluasi ketercapaian target RPJMD dan Renja perangkat daerah.
                    </p>

                    <div class="pt-2 flex flex-wrap gap-4 text-xs font-bold text-slate-200">
                        <div class="flex items-center gap-2 bg-white/10 px-3.5 py-2 rounded-xl backdrop-blur border border-white/10">
                            <svg class="w-4 h-4 text-[#FFDA65]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Terpadu & Selaras</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white/10 px-3.5 py-2 rounded-xl backdrop-blur border border-white/10">
                            <svg class="w-4 h-4 text-[#FFDA65]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Berbasis Riset & Bukti</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white/10 px-3.5 py-2 rounded-xl backdrop-blur border border-white/10">
                            <svg class="w-4 h-4 text-[#FFDA65]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Transparan & Akuntabel</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 text-center space-y-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Pemkab Nias Barat" class="w-16 h-16 mx-auto object-contain drop-shadow">
                    <div>
                        <h4 class="font-extrabold text-white text-base">Pemerintah Kabupaten Nias Barat</h4>
                        <p class="text-xs text-[#FFDA65] font-semibold mt-0.5">Badan Perencanaan Pembangunan, Penelitian, dan Pengembangan Daerah</p>
                    </div>
                    <div class="pt-3 border-t border-white/15 text-left text-xs text-slate-300 space-y-1.5">
                        <p class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-[#FFDA65] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Kompleks Perkantoran Pemkab Nias Barat, Onolimbu, Lahomi</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#FFDA65] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>bappelitbangda@niasbaratkab.go.id</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4 Bidang Kerja Teknis Bappelitbangda --}}
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Bidang 1 --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs hover:shadow-md transition-all">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-[#AD1D30] flex items-center justify-center font-black text-sm mb-3.5 border border-red-100">
                    I
                </div>
                <h4 class="font-extrabold text-[#191716] text-sm leading-snug">Perencanaan, Pengendalian & Evaluasi (PPEPD)</h4>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Penyusunan dokumen RPJMD, RKPD, harmonisasi Renja OPD, serta evaluasi capaian kinerja pembangunan daerah secara periodik.
                </p>
            </div>

            {{-- Bidang 2 --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs hover:shadow-md transition-all">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-[#D4A017] flex items-center justify-center font-black text-sm mb-3.5 border border-amber-100">
                    II
                </div>
                <h4 class="font-extrabold text-[#191716] text-sm leading-snug">Perekonomian, SDA & Kewilayahan (PSDAIK)</h4>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Perencanaan sektor pertanian, perikanan, pariwisata, ketahanan pangan, penataan ruang, dan infrastruktur strategis wilayah.
                </p>
            </div>

            {{-- Bidang 3 --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs hover:shadow-md transition-all">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-[#AD1D30] flex items-center justify-center font-black text-sm mb-3.5 border border-red-100">
                    III
                </div>
                <h4 class="font-extrabold text-[#191716] text-sm leading-snug">Pemerintahan & Pembangunan Manusia (PPM)</h4>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Peningkatan IPM, akses layanan pendidikan berkualitas, jaminan kesehatan semesta (UHC), penurunan kemiskinan, dan stunting.
                </p>
            </div>

            {{-- Bidang 4 --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs hover:shadow-md transition-all">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-black text-sm mb-3.5 border border-amber-100">
                    IV
                </div>
                <h4 class="font-extrabold text-[#191716] text-sm leading-snug">Penelitian & Pengembangan (Litbang)</h4>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Kajian riset terapan, penjaringan inovasi daerah (Inovasi Nias Barat), studi kelayakan program, dan pemanfaatan iptek daerah.
                </p>
            </div>
        </div>

    </section>
    <section class="mission-section" aria-label="Visi dan misi daerah">
        {{-- Visi & Misi Pemerintah Kabupaten Nias Barat --}}
        <div id="visi-misi">
            @php
                $rpjmd = app(\App\Services\RpjmdContent::class)->get();
                $hierarchyRows = app(\App\Services\RpjmdHierarchy::class)->present($rpjmd);
            @endphp
            @include('rpjmd._hierarchy', compact('rpjmd', 'hierarchyRows'))
            <div class="mission-legal text-xs text-slate-500 leading-relaxed">
                <p class="font-bold text-[#191716] mb-1">Dasar Hukum</p>
                <p>Peraturan Daerah Kabupaten Nias Barat Nomor 5 Tahun 2025 tentang Rencana Pembangunan Jangka Menengah Daerah Tahun 2025&ndash;2029, ditetapkan pada 19 Agustus 2025.</p>
                <a href="https://peraturan.bpk.go.id/Details/340868/perda-kab-nias-barat-no-5-tahun-2025" target="_blank" rel="noopener noreferrer" class="inline-block mt-2 font-bold text-[#AD1D30] hover:underline">Lihat Perda RPJMD 2025&ndash;2029 di JDIH BPK &#8599;</a>
            </div>
        </div>
    </section>

    {{-- =========================================================================
       5. MODAL POPUP DETAIL INDIKATOR LENGKAP
       ========================================================================= --}}
    <div x-show="modalOpen" x-cloak class="indicator-modal-shell" role="dialog" aria-modal="true" @keydown.escape.window="closeModal()">
        <div class="indicator-modal-backdrop" @click="closeModal()"></div>
        <div class="indicator-modal-panel p-6 sm:p-8">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex flex-wrap gap-2 mb-2">
                        <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-[#191716] text-white" x-text="selected?.jenis || 'IKD'"></span>
                        <span x-show="selected?.is_iku" class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-[#D4A017] text-[#121212]">IKU RPJMD</span>
                        <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-600" x-text="selected?.kode"></span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-[#191716]" x-text="selected?.nama"></h3>
                    <p class="text-xs text-slate-500 mt-1 font-medium" x-text="(selected?.aspek ? shortAspect(selected.aspek) : 'Aspek Daerah') + ' · Satuan: ' + (selected?.satuan || '—')"></p>
                </div>
                <button type="button" @click="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 shrink-0 transition" aria-label="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/></svg>
                </button>
            </div>

            {{-- Grafik Garis Tren Historis (2020-2030) --}}
            <div x-show="selected?.has_chart" class="indicator-chart-wrap mt-6">
                <canvas id="indicatorTrendChart"></canvas>
            </div>

            <div x-show="selected && !selected.has_chart" class="mt-5 rounded-xl bg-amber-50 border border-amber-200/80 p-4 text-xs text-amber-900 font-medium">
                Data indikator ini berupa narasi kualitatif, sehingga ditampilkan dalam bentuk tabel histori tanpa grafik numerik.
            </div>

            {{-- Tabel Histori Target & Realisasi --}}
            <div class="mt-5 overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 uppercase text-[10px] font-bold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-2.5">Tahun</th>
                            <th class="px-4 py-2.5">Jenis Nilai</th>
                            <th class="px-4 py-2.5">Target RPJMD</th>
                            <th class="px-4 py-2.5">Realisasi Capaian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="p in (selected?.points || [])" :key="p.year">
                            <tr :class="p.year === activeYear ? 'bg-amber-50/60 font-bold' : ''">
                                <td class="px-4 py-2.5 text-[#191716]" x-text="p.year + (p.year === activeYear ? ' (Terpilih)' : '')"></td>
                                <td class="px-4 py-2.5 capitalize text-slate-600" x-text="p.source || '—'"></td>
                                <td class="px-4 py-2.5 text-slate-700" x-text="p.target_raw || '—'"></td>
                                <td class="px-4 py-2.5 text-[#AD1D30] font-bold" x-text="p.actual_raw || '—'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <p x-show="selected?.sumber_data" class="mt-4 text-[11px] text-slate-500">
                <span class="font-bold text-slate-700">Sumber Data:</span> <span x-text="selected?.sumber_data"></span>
            </p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Plus Jakarta Sans', Inter, sans-serif";
    }

    window.__SICERAH_INDICATORS__ = {{ Illuminate\Support\Js::from($indikatorDaerah) }};
    window.__ACTIVE_YEAR__ = {{ $tahun }};

    window.indicatorExplorer = function () {
        return {
            items: window.__SICERAH_INDICATORS__ || [],
            activeYear: window.__ACTIVE_YEAR__,
            search: '',
            category: 'all',
            viewMode: 'grid',
            sortBy: 'default',
            page: 1,
            perPage: 6,
            modalOpen: false,
            selected: null,
            chart: null,

            resetFilters() {
                this.search = '';
                this.category = 'all';
                this.sortBy = 'default';
                this.page = 1;
            },

            get filtered() {
                const query = this.search.trim().toLowerCase();
                let list = this.items.filter((item) => {
                    // Filter Kategori
                    let catMatch = true;
                    if (this.category === 'ekonomi') {
                        catMatch = /ekonomi|keuangan|pdrb|daya saing|pangan|pertanian/i.test((item.nama || '') + ' ' + (item.aspek || ''));
                    } else if (this.category === 'sosial') {
                        catMatch = /sosial|kesejahteraan|kemiskinan|gender|masyarakat/i.test((item.nama || '') + ' ' + (item.aspek || ''));
                    } else if (this.category === 'pendidikan') {
                        catMatch = /pendidikan|sekolah|guru|literasi|ipm/i.test((item.nama || '') + ' ' + (item.aspek || ''));
                    } else if (this.category === 'kesehatan') {
                        catMatch = /kesehatan|jkn|harapan hidup|stunting|gizi|puskesmas/i.test((item.nama || '') + ' ' + (item.aspek || ''));
                    } else if (this.category === 'infrastruktur') {
                        catMatch = /infrastruktur|pekerjaan umum|jalan|jembatan|air|perumahan/i.test((item.nama || '') + ' ' + (item.aspek || ''));
                    } else if (this.category === 'lingkungan') {
                        catMatch = /lingkungan|sampah|kehutanan|bencana/i.test((item.nama || '') + ' ' + (item.aspek || ''));
                    } else if (this.category === 'pemerintahan') {
                        catMatch = /pemerintahan|pelayanan umum|birokrasi|kepegawaian/i.test((item.nama || '') + ' ' + (item.aspek || ''));
                    }

                    // Search Query Match
                    const fullText = [(item.nama || ''), (item.aspek || ''), (item.sumber_data || ''), (item.kode || '')].join(' ').toLowerCase();
                    const searchMatch = !query || fullText.includes(query);

                    return catMatch && searchMatch;
                });

                // Sorting
                if (this.sortBy === 'name_asc') {
                    list.sort((a, b) => (a.nama || '').localeCompare(b.nama || ''));
                } else if (this.sortBy === 'name_desc') {
                    list.sort((a, b) => (b.nama || '').localeCompare(a.nama || ''));
                } else if (this.sortBy === 'highest') {
                    list.sort((a, b) => {
                        const valA = parseFloat(String(a.selected?.graph || 0));
                        const valB = parseFloat(String(b.selected?.graph || 0));
                        return valB - valA;
                    });
                }

                return list;
            },

            get totalPages() {
                return Math.max(1, Math.ceil(this.filtered.length / this.perPage));
            },

            get paginated() {
                const start = (this.page - 1) * this.perPage;
                return this.filtered.slice(start, start + this.perPage);
            },

            get paginationStart() {
                return this.filtered.length === 0 ? 0 : (this.page - 1) * this.perPage + 1;
            },

            get paginationEnd() {
                return Math.min(this.page * this.perPage, this.filtered.length);
            },

            get visiblePages() {
                const total = this.totalPages;
                const current = this.page;
                if (total <= 5) {
                    return Array.from({ length: total }, (_, i) => i + 1);
                }
                if (current <= 3) {
                    return [1, 2, 3, 4, '...', total];
                }
                if (current >= total - 2) {
                    return [1, '...', total - 3, total - 2, total - 1, total];
                }
                return [1, '...', current - 1, current, current + 1, '...', total];
            },

            shortAspect(value) {
                if (!value) return 'Aspek Daerah';
                return value.replace(/^ASPEK\s+/i, '').toLowerCase().replace(/(^|\s)\S/g, (l) => l.toUpperCase());
            },

            getSourceText(item) {
                if (item.perangkat_daerah) return item.perangkat_daerah;
                if (item.file) return item.file.replace(/\.xlsx$/i, '');
                return 'Bappelitbangda Kabupaten Nias Barat';
            },

            getDisplayValue(item) {
                const sel = item.selected;
                if (sel && sel.raw !== null && sel.raw !== '') {
                    return sel.raw + (item.satuan ? ' ' + item.satuan : '');
                }
                if (item.baseline_2024) {
                    return item.baseline_2024 + (item.satuan ? ' ' + item.satuan : '');
                }
                const firstPoint = (item.points || []).find(p => p.raw);
                if (firstPoint) {
                    return firstPoint.raw + (item.satuan ? ' ' + item.satuan : '');
                }
                return '—';
            },

            getDeltaBadge(item) {
                const pts = item.points || [];
                const currYear = this.activeYear;
                const prevYear = currYear - 1;
                
                const currPt = pts.find(p => p.year === currYear);
                const prevPt = pts.find(p => p.year === prevYear) || pts.find(p => p.year === 2024);

                const currVal = currPt?.graph;
                const prevVal = prevPt?.graph;

                if (currVal !== null && prevVal !== null && currVal !== undefined && prevVal !== undefined && currVal > 0 && prevVal > 0) {
                    const diff = currVal - prevVal;
                    const sign = diff >= 0 ? '+' : '';
                    const arrow = diff >= 0 ? '▲' : '▼';
                    return `${arrow} ${sign}${diff.toFixed(2).replace('.', ',')}`;
                }
                return 'Target';
            },

            getDeltaClass(item) {
                const pts = item.points || [];
                const currPt = pts.find(p => p.year === this.activeYear);
                const prevPt = pts.find(p => p.year === (this.activeYear - 1)) || pts.find(p => p.year === 2024);

                const diff = (currPt?.graph ?? 0) - (prevPt?.graph ?? 0);
                const isNegativeGoal = /kemiskinan|pengangguran|gini|kematian|stunting/i.test(item.nama || '');
                
                let isGood = diff >= 0;
                if (isNegativeGoal) {
                    isGood = diff <= 0;
                }

                return isGood ? 'text-amber-700 bg-amber-50' : 'text-rose-700 bg-rose-50';
            },

            getIconBgClass(item) {
                const text = (item.nama || '').toLowerCase();
                if (/ekonomi|pdrb|keuangan/i.test(text)) return 'bg-amber-50 text-amber-600';
                if (/manusia|ipm|pendidikan/i.test(text)) return 'bg-red-50 text-[#AD1D30]';
                if (/gini|kemiskinan/i.test(text)) return 'bg-amber-50 text-amber-600';
                if (/kesehatan|usia|hidup|jkn/i.test(text)) return 'bg-rose-50 text-rose-600';
                if (/pangan|pertanian/i.test(text)) return 'bg-lime-50 text-lime-600';
                return 'bg-red-50 text-red-600';
            },

            getCategoryIcon(item) {
                const text = (item.nama || '').toLowerCase();
                if (/ekonomi|pdrb|keuangan/i.test(text)) {
                    return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>';
                }
                if (/manusia|ipm|pendidikan/i.test(text)) {
                    return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>';
                }
                if (/gini|kemiskinan/i.test(text)) {
                    return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>';
                }
                if (/kesehatan|usia|hidup|jkn/i.test(text)) {
                    return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>';
                }
                return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            },

            getSparkColor(item) {
                const text = (item.nama || '').toLowerCase();
                if (/ekonomi|pdrb/i.test(text)) return '#B38412'; // Emerald
                if (/manusia|ipm/i.test(text)) return '#AD1D30'; // Red Crimson
                if (/gini/i.test(text)) return '#741421'; // Purple
                if (/kesehatan|usia|hidup/i.test(text)) return '#f59e0b'; // Amber
                if (/pangan/i.test(text)) return '#B38412'; // Emerald
                return '#AD1D30'; // Red
            },

            getSparkLinePath(item) {
                const pts = (item.points || []).filter(p => p.graph !== null && p.graph !== undefined);
                if (pts.length < 2) {
                    return 'M 0,25 Q 80,15 160,20';
                }
                const values = pts.map(p => p.graph);
                const min = Math.min(...values);
                const max = Math.max(...values);
                const range = Math.max(0.001, max - min);

                const coords = pts.map((p, idx) => {
                    const x = (idx / (pts.length - 1)) * 160;
                    const y = 32 - ((p.graph - min) / range) * 24;
                    return [x, y];
                });

                let d = `M ${coords[0][0]},${coords[0][1]}`;
                for (let i = 1; i < coords.length; i++) {
                    const prev = coords[i - 1];
                    const curr = coords[i];
                    const cx = (prev[0] + curr[0]) / 2;
                    d += ` Q ${prev[0]},${prev[1]} ${cx},${(prev[1] + curr[1]) / 2} T ${curr[0]},${curr[1]}`;
                }
                return d;
            },

            getSparkAreaPath(item) {
                const linePath = this.getSparkLinePath(item);
                return `${linePath} L 160,40 L 0,40 Z`;
            },

            openIndicator(item) {
                this.selected = item;
                this.modalOpen = true;
                document.body.style.overflow = 'hidden';
                this.$nextTick(() => this.renderChart());
            },

            closeModal() {
                this.modalOpen = false;
                document.body.style.overflow = '';
                if (this.chart) {
                    this.chart.destroy();
                    this.chart = null;
                }
            },

            renderChart() {
                if (!this.selected?.has_chart || typeof Chart === 'undefined') return;
                const canvas = document.getElementById('indicatorTrendChart');
                if (!canvas) return;
                if (this.chart) this.chart.destroy();

                const points = this.selected.points || [];
                const datasets = [
                    {
                        label: 'Realisasi Capaian',
                        data: points.map(p => p.actual_graph),
                        borderColor: '#AD1D30',
                        backgroundColor: 'rgba(185, 28, 28, 0.1)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 5,
                        pointBackgroundColor: '#AD1D30',
                    },
                    {
                        label: 'Target RPJMD',
                        data: points.map(p => p.target_graph),
                        borderColor: '#D4A017',
                        borderDash: [6, 4],
                        fill: false,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#D4A017',
                    }
                ].filter(ds => ds.data.some(v => v !== null && v !== undefined));

                this.chart = new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: points.map(p => String(p.year)),
                        datasets,
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: { font: { weight: 'bold', size: 11 } }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                grid: { color: 'rgba(226, 232, 240, 0.7)' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        };
    };

    // Inisialisasi Line Chart Kumulatif Kinerja Daerah
    document.addEventListener('DOMContentLoaded', function () {
        const kumulatifCanvas = document.getElementById('chartKumulatifLanding');
        if (kumulatifCanvas && typeof Chart !== 'undefined') {
            const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            
            // Buat kurva kumulatif interpolasi bulanan berdasarkan realisasi triwulan
            @php
                $twFisik = $tren['fisik'] ?? [8.5, 19.2, 38.6, 54.0];
                $twKeuangan = $tren['keuangan'] ?? [6.5, 11.6, 32.4, 69.8];
            @endphp
            
            const fisikData = [
                {{ round(($twFisik[0] ?? 0) * 0.3, 1) }},
                {{ round(($twFisik[0] ?? 0) * 0.65, 1) }},
                {{ $twFisik[0] ?? 8.5 }},
                {{ round(($twFisik[0] ?? 8.5) + (($twFisik[1] ?? 19.2) - ($twFisik[0] ?? 8.5)) * 0.33, 1) }},
                {{ round(($twFisik[0] ?? 8.5) + (($twFisik[1] ?? 19.2) - ($twFisik[0] ?? 8.5)) * 0.66, 1) }},
                {{ $twFisik[1] ?? 19.2 }},
                {{ round(($twFisik[1] ?? 19.2) + (($twFisik[2] ?? 38.6) - ($twFisik[1] ?? 19.2)) * 0.33, 1) }},
                {{ round(($twFisik[1] ?? 19.2) + (($twFisik[2] ?? 38.6) - ($twFisik[1] ?? 19.2)) * 0.66, 1) }},
                {{ $twFisik[2] ?? 38.6 }},
                {{ round(($twFisik[2] ?? 38.6) + (($twFisik[3] ?? 54.0) - ($twFisik[2] ?? 38.6)) * 0.33, 1) }},
                {{ round(($twFisik[2] ?? 38.6) + (($twFisik[3] ?? 54.0) - ($twFisik[2] ?? 38.6)) * 0.66, 1) }},
                {{ $twFisik[3] ?? 54.0 }}
            ];

            const keuanganData = [
                {{ round(($twKeuangan[0] ?? 0) * 0.25, 1) }},
                {{ round(($twKeuangan[0] ?? 0) * 0.6, 1) }},
                {{ $twKeuangan[0] ?? 6.5 }},
                {{ round(($twKeuangan[0] ?? 6.5) + (($twKeuangan[1] ?? 11.6) - ($twKeuangan[0] ?? 6.5)) * 0.33, 1) }},
                {{ round(($twKeuangan[0] ?? 6.5) + (($twKeuangan[1] ?? 11.6) - ($twKeuangan[0] ?? 6.5)) * 0.66, 1) }},
                {{ $twKeuangan[1] ?? 11.6 }},
                {{ round(($twKeuangan[1] ?? 11.6) + (($twKeuangan[2] ?? 32.4) - ($twKeuangan[1] ?? 11.6)) * 0.33, 1) }},
                {{ round(($twKeuangan[1] ?? 11.6) + (($twKeuangan[2] ?? 32.4) - ($twKeuangan[1] ?? 11.6)) * 0.66, 1) }},
                {{ $twKeuangan[2] ?? 32.4 }},
                {{ round(($twKeuangan[2] ?? 32.4) + (($twKeuangan[3] ?? 69.8) - ($twKeuangan[2] ?? 32.4)) * 0.33, 1) }},
                {{ round(($twKeuangan[2] ?? 32.4) + (($twKeuangan[3] ?? 69.8) - ($twKeuangan[2] ?? 32.4)) * 0.66, 1) }},
                {{ $twKeuangan[3] ?? 69.8 }}
            ];

            new Chart(kumulatifCanvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Realisasi Fisik',
                            data: fisikData,
                            borderColor: '#D4A017',
                            backgroundColor: 'rgba(16, 185, 129, 0.08)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            pointBackgroundColor: '#D4A017'
                        },
                        {
                            label: 'Realisasi Keuangan',
                            data: keuanganData,
                            borderColor: '#DC2626',
                            backgroundColor: 'rgba(220, 38, 38, 0.08)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            pointBackgroundColor: '#DC2626'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.dataset.label}: ${ctx.parsed.y}%`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { callback: (v) => v + '%' },
                            grid: { color: 'rgba(226, 232, 240, 0.6)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
