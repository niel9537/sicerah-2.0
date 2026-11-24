@extends($dashboard ? 'layouts.app' : 'layouts.public')

@section('title', 'WebGIS — Peta Tematik Spasial Pembangunan Nias Barat')
@section('meta_description', 'Sistem Informasi Geografis (WebGIS) Bappelitbangda Kabupaten Nias Barat: pemetaan spasial indikator pembangunan daerah per desa dan per kecamatan.')

@section('content')
@include('webgis._style')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

{{-- Hero Banner WebGIS Selaras dengan Beranda & JDIH --}}
@if(!$dashboard)
<section class="relative overflow-hidden text-white bg-[#741421]">
    {{-- Background Panorama Nias Barat --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/spasial-hero.jpg') }}" alt="Dermaga dan perahu di laut biru Nias Barat" class="w-full h-full object-cover object-center filter brightness-[0.40] contrast-[1.12] transform scale-105">
        {{-- Overlay Gradasi Navy (#741421) ke Merah Tua & Vignet --}}
        <div class="absolute inset-0 bg-gradient-to-r from-[#741421]/95 via-[#741421]/80 to-[#68121D]/75 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#741421] via-transparent to-transparent opacity-90"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="flex flex-wrap items-center gap-2 mb-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-[#D4A017]/25 text-[#FFDA65] border border-[#FFDA65]/40 backdrop-blur-md">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Peta Tematik Spasial Wilayah
            </span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-white/10 text-white/90 border border-white/15 backdrop-blur-md">
                Kecamatan & Desa Kabupaten Nias Barat
            </span>
        </div>

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div class="max-w-3xl">
                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                    Sistem Informasi Geografis & <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#FFDA65] to-[#D4A017]">WebGIS Tematik</span>
                </h1>
                <p class="mt-3 text-sm sm:text-base text-slate-200/90 leading-relaxed max-w-2xl">
                    Visualisasi data spasial indikator pembangunan daerah per desa dan kecamatan. Pilih tema indikator, filter tahun, dan telusuri sebaran wilayah secara interaktif.
                </p>
            </div>

            @auth
                @if(in_array(auth()->user()->role, ['super_admin', 'admin_bappeda'], true))
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('webgis.admin.index') }}" class="wg-button wg-button-gold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Kelola Peta Tematik</span>
                        </a>
                        <a href="{{ route('webgis.education.index') }}" class="wg-button">
                            <span>Kelola Pendidikan</span>
                        </a>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</section>
@endif

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 {{ !$dashboard ? '-mt-6' : '' }} relative z-20 wg-container">
    @if($dashboard)
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-[#741421]">WebGIS Tematik Nias Barat</h1>
                <p class="text-xs text-slate-500 mt-1">Jelajahi indikator pendidikan per kecamatan serta indikator pembangunan per desa dan tahun.</p>
            </div>
            @if(in_array(auth()->user()?->role,['super_admin','admin_bappeda'],true))
                <div class="flex items-center gap-2">
                    <a class="wg-button wg-button-gold" href="{{ route('webgis.admin.index') }}">Kelola Peta Tematik</a>
                    <a class="wg-button" href="{{ route('webgis.education.index') }}">Kelola Pendidikan</a>
                </div>
            @endif
        </div>
    @endif

    <div class="wg-card">
        <div class="wg-surface-controls">
            <label for="wg-basemap">Peta permukaan
                <select id="wg-basemap">
                    <option value="street">Peta Jalan (OpenStreetMap)</option>
                    <option value="satellite">Satelit - Bangunan dan Jalan</option>
                    <option value="dsm">DSM - Warna Ketinggian</option>
                    <option value="relief">DSM - Relief Permukaan</option>
                </select>
            </label>
            <button type="button" class="wg-button" id="wg-detail">Zoom Detail Bangunan</button>
            <label class="wg-overlay-control"><input type="checkbox" id="wg-thematic"> Tampilkan indikator pembangunan</label>
        </div>
        <div id="wg-satellite-info" class="wg-dsm-info" hidden>
            <strong>Citra satelit - bangunan, jalan, dan tutupan lahan</strong>
            <p>Perbesar ke desa atau lokasi yang ingin dilihat. Detail rumah mengikuti resolusi citra di lokasi tersebut. Tampilan ini berupa foto dari atas (2D).</p>
        </div>
        <div id="wg-dsm-info" class="wg-dsm-info" hidden>
            <strong>Digital Surface Model - ASTER GDEM</strong>
            <p>Ketinggian permukaan termasuk vegetasi dan bangunan. Visualisasi regional dari NASA GIBS; detail tampilan tersedia hingga zoom 12. Memperbesar peta tidak menambah detail elevasi.</p>
            <img id="wg-dsm-legend" src="{{ asset('images/aster-gdem-legend.svg') }}" alt="Legenda ketinggian ASTER GDEM dalam meter" width="378" height="86">
            <a href="https://www.earthdata.nasa.gov/data/catalog/lpcloud-astgtm-003" target="_blank" rel="noopener noreferrer">Sumber: NASA / METI ASTER GDEM</a>
        </div>
        <p id="wg-basemap-status" class="wg-basemap-status" role="status" aria-live="polite"></p>
        <section class="wg-facilities" aria-labelledby="wg-facilities-title" data-url="{{ asset('data/gis/nias-barat-buildings-map.geojson') }}?v=20261124" id="wg-facilities">
            <strong id="wg-facilities-title">Bangunan dan Fasilitas Publik</strong>
            <div class="wg-facility-filters">
                <label><input type="checkbox" value="government" checked> <span class="wg-chip" style="background:#7c3aed"></span>Pemerintah <span id="wg-count-government">0</span></label>
                <label><input type="checkbox" value="school" checked> <span class="wg-chip" style="background:#2563eb"></span>Sekolah <span id="wg-count-school">0</span></label>
                <label><input type="checkbox" value="health" checked> <span class="wg-chip" style="background:#dc2626"></span>Kesehatan <span id="wg-count-health">0</span></label>
                <label><input type="checkbox" value="unclassified" checked> <span class="wg-chip" style="background:#64748b"></span>Bangunan <span id="wg-count-unclassified">0</span></label>
                <button type="button" id="wg-facility-fit" class="wg-button" disabled>Lihat Semua Fasilitas</button>
                <button type="button" id="wg-facility-retry" class="wg-button" hidden>Coba Lagi</button>
            </div>
            <p id="wg-facility-status" role="status" aria-live="polite">Memuat fasilitas publik...</p>
            <p>Warna membedakan fungsi fasilitas. Poligon menunjukkan tapak bangunan atau area fasilitas sesuai data sumber; titik menunjukkan lokasi tanpa tapak terpetakan. Cakupan seluruh Kabupaten Nias Barat; terpisah dari filter wilayah dan tahun indikator. Data sumber belum tentu lengkap.</p>
            <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">Peta dasar: OpenStreetMap contributors (ODbL)</a>
            <a href="https://docs.overturemaps.org/attribution/" target="_blank" rel="noopener noreferrer">Data bangunan dan fasilitas: Overture Maps / sumber data</a>
            <p>Abu-abu: tapak bangunan yang belum diketahui fungsinya. Daftar di bawah berisi fasilitas yang kategorinya telah tercatat pada sumber.</p>
            <div id="wg-facility-list" class="wg-facility-list" aria-label="Daftar fasilitas publik"></div>
        </section>
        {{-- Filter Bar --}}
        <div class="wg-filters">
            <label>
                <span>Peta Tematik</span>
                <select id="wg-theme">
                    <option value="{{ route('webgis.education.data') }}">Pendidikan per Kecamatan</option>
                    @foreach($themes as $t)
                        <option value="{{ route('webgis.data',$t) }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Tahun Data</span>
                <select id="wg-year"></select>
            </label>
            <label>
                <span>Kecamatan</span>
                <select id="wg-kec">
                    <option value="">Semua Kecamatan</option>
                </select>
            </label>
            <label>
                <span>Desa / Kelurahan</span>
                <select id="wg-desa">
                    <option value="">Semua Desa</option>
                </select>
            </label>
        </div>

        {{-- Status Bar & Retry Button --}}
        <div class="flex flex-wrap items-center gap-3">
            <div id="wg-status" class="wg-status" role="status" aria-live="polite">
                <span class="w-2 h-2 rounded-full bg-[#AD1D30] animate-pulse"></span>
                <span>Memuat peta…</span>
            </div>
            <button type="button" class="wg-button" id="wg-retry" hidden>Coba Lagi</button>
        </div>

        {{-- Map Canvas --}}
        <div id="wg-map" class="wg-map"></div>

        {{-- Legend Container --}}
        <div id="wg-legend" class="wg-legend"></div>

        {{-- Sumber Data & Catatan Teknis --}}
        <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-slate-500">
            <p id="wg-source" class="font-medium text-[#741421]"></p>
            <p class="text-[11px] text-slate-400 italic">Angka ditampilkan sesuai satuan sumber resmi.</p>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('assets/webgis/map.js') . '?v=local-buildings-4' }}"></script>
@endsection
