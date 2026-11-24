@extends('layouts.public')

@section('title', 'WebGIS — Peta Tematik Spasial Pembangunan Nias Barat')
@section('meta_description', 'Sistem Informasi Geografis (WebGIS) Bappelitbangda Kabupaten Nias Barat: pemetaan spasial indikator pembangunan daerah tingkat desa dan kecamatan secara interaktif.')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
    #niasMap {
        position: relative;
        z-index: 0;
        isolation: isolate;
        height: clamp(480px, 72vh, 800px);
        background: #e2e8f0;
        border-radius: 1.25rem;
    }
    .leaflet-container {
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        border-radius: 1.25rem;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 1.25rem;
        box-shadow: 0 20px 45px -15px rgba(116,20,33, 0.45);
        border: 1px solid rgba(226, 232, 240, 0.9);
        padding: 4px;
    }
    .leaflet-popup-content {
        margin: 1rem;
        min-width: 200px;
    }
    @media (max-width: 639px) {
        #niasMap {
            height: min(65vh, 560px);
            min-height: 440px;
        }
    }
</style>
@endpush

@section('content')

{{-- Hero Banner WebGIS Selaras dengan Beranda & JDIH --}}
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
                    Visualisasi data spasial indikator pembangunan daerah per desa dan kecamatan. Pilih layer tematik, filter tahun, dan telusuri sebaran capaian wilayah secara interaktif.
                </p>
            </div>

            @auth
                @if(in_array(auth()->user()->role, ['super_admin', 'admin_bappeda'], true))
                    <a href="{{ route('webgis.admin.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#D4A017] to-[#FFDA65] text-[#21191A] px-4 py-2.5 text-xs font-extrabold shadow-[0_2px_12px_rgba(212,160,23,0.35)] hover:shadow-[0_4px_18px_rgba(212,160,23,0.5)] hover:-translate-y-0.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Kelola Layer WebGIS</span>
                    </a>
                @endif
            @endauth
        </div>
    </div>
</section>

{{-- Konten Utama WebGIS --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{panel: true}">
    @if(!$activeLayer)
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200/90 shadow-xs">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 text-[#D4A017] flex items-center justify-center mx-auto mb-4 border border-amber-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
            </div>
            <h3 class="text-base font-extrabold text-[#741421]">Belum Ada Layer Peta yang Aktif</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto mt-1 leading-relaxed">
                Layer peta tematik sedang dipersiapkan oleh tim Bappelitbangda Kabupaten Nias Barat. Admin dapat menambahkan layer melalui dasbor manajemen.
            </p>
        </div>
    @else
        <div class="grid lg:grid-cols-[330px_1fr] gap-6 items-start">
            {{-- Sidebar Kontrol Layer & Ringkasan Data --}}
            <aside class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-[0_4px_20px_rgb(8,38,74,0.06)] lg:sticky lg:top-24 space-y-5">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-black uppercase tracking-wider text-[#AD1D30]">Kontrol Layer</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Aktif
                        </span>
                    </div>

                    <div class="space-y-3.5">
                        {{-- Pilih Layer --}}
                        <div>
                            <label for="layerSelect" class="block text-xs font-bold text-[#741421] mb-1.5">Layer Tematik</label>
                            <select id="layerSelect" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:border-[#AD1D30] focus:ring-2 focus:ring-[#AD1D30]/15 transition bg-white outline-hidden" onchange="location.href=this.value">
                                @foreach($layers as $layer)
                                    <option value="{{ route('webgis.index', ['layer' => $layer->slug]) }}" @selected($activeLayer->id === $layer->id)>
                                        {{ $layer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pilih Tahun --}}
                        <div>
                            <label for="yearSelect" class="block text-xs font-bold text-[#741421] mb-1.5">Tahun Data</label>
                            <select id="yearSelect" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:border-[#AD1D30] focus:ring-2 focus:ring-[#AD1D30]/15 transition bg-white outline-hidden">
                                @forelse($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @empty
                                    <option value="{{ now()->year }}">Belum ada data</option>
                                @endforelse
                            </select>
                        </div>

                        {{-- Pilih Desa / Wilayah --}}
                        <div>
                            <label for="areaSelect" class="block text-xs font-bold text-[#741421] mb-1.5">Fokus Wilayah / Desa</label>
                            <select id="areaSelect" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:border-[#AD1D30] focus:ring-2 focus:ring-[#AD1D30]/15 transition bg-white outline-hidden">
                                <option value="">Semua Desa (Kabupaten Nias Barat)</option>
                                @foreach($areas->groupBy('district_name') as $district => $districtAreas)
                                    <optgroup label="{{ $district ?: 'Kecamatan' }}">
                                        @foreach($districtAreas as $area)
                                            <option value="{{ $area->code }}">{{ $area->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Panel Ringkasan Metrik --}}
                <div class="pt-4 border-t border-slate-100">
                    <p class="text-[11px] font-black uppercase tracking-wider text-slate-400 mb-3">Ringkasan Nilai Layer</p>
                    <div class="grid grid-cols-3 lg:grid-cols-1 gap-2.5">
                        <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                            <span class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wide">Desa Terisi</span>
                            <p id="countValue" class="text-sm sm:text-base font-black text-[#741421] mt-0.5">—</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                            <span class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wide">Nilai Terendah</span>
                            <p id="minValue" class="text-sm sm:text-base font-black text-slate-700 mt-0.5">—</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                            <span class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wide">Nilai Tertinggi</span>
                            <p id="maxValue" class="text-sm sm:text-base font-black text-[#AD1D30] mt-0.5">—</p>
                        </div>
                    </div>
                </div>

                {{-- Legenda Gradasi Warna --}}
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between text-xs font-bold text-[#741421] mb-1.5">
                        <span>Legenda Indikator</span>
                        <span class="text-[10.5px] text-slate-400 font-semibold">{{ $activeLayer->unit ?? 'Sebaran' }}</span>
                    </div>
                    <div class="h-3.5 rounded-full shadow-inner border border-slate-200" style="background: linear-gradient(90deg, {{ $activeLayer->color_from }}, {{ $activeLayer->color_to }})"></div>
                    <div class="flex justify-between mt-1.5 text-[10.5px] font-bold text-slate-500">
                        <span>Rendah (Min)</span>
                        <span>Tinggi (Maks)</span>
                    </div>
                </div>
            </aside>

            {{-- Wadah Peta WebGIS --}}
            <div class="bg-white rounded-2xl p-3 sm:p-4 border border-slate-200/90 shadow-[0_8px_30px_rgb(8,38,74,0.06)] relative overflow-hidden">
                <div id="mapStatus" class="absolute left-6 top-6 z-[500] rounded-xl bg-white/95 backdrop-blur-md shadow-md border border-slate-200 px-3.5 py-2 text-xs font-bold text-[#741421] flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#AD1D30] animate-pulse"></span>
                    <span>Memuat peta spasial…</span>
                </div>
                <div id="niasMap"></div>
            </div>
        </div>
    @endif
</section>
@endsection

@if($activeLayer)
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function(){
    const map = L.map('niasMap', { zoomControl: false }).setView([1.02, 97.48], 10);
    L.control.zoom({ position: 'bottomright' }).addTo(map);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap & Bappelitbangda Nias Barat'
    }).addTo(map);

    const status = document.getElementById('mapStatus');
    const yearSelect = document.getElementById('yearSelect');
    const areaSelect = document.getElementById('areaSelect');
    const endpoint = @js(route('webgis.data'));
    const layerSlug = @js($activeLayer->slug);
    let geometry = null, layer = null, valueMap = new Map(), config = null;

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
    }[char]));

    const hexToRgb = (hex) => {
        const value = parseInt(hex.slice(1), 16);
        return [(value >> 16) & 255, (value >> 8) & 255, value & 255];
    };

    const color = (value, min, max) => {
        if (value === null || Number.isNaN(Number(value))) return '#cbd5e1';
        const from = hexToRgb(config.color_from);
        const to = hexToRgb(config.color_to);
        let ratio = max === min ? 0.65 : (Number(value) - min) / (max - min);
        if (Array.isArray(config.breakpoints) && config.breakpoints.length) {
            const index = config.breakpoints.findIndex((point) => Number(value) <= Number(point));
            ratio = (index < 0 ? config.breakpoints.length : index) / Math.max(config.breakpoints.length, 1);
        }
        ratio = Math.max(0, Math.min(1, ratio));
        return `rgb(${from.map((part, index) => Math.round(part + (to[index] - part) * ratio)).join(',')})`;
    };

    const codeOf = (feature) => feature.properties.KDEPUM || feature.properties.kode_desa || feature.properties.KODE_DESA;
    const nameOf = (feature) => feature.properties.WADMKD || feature.properties.NAMOBJ || 'Desa';
    const districtOf = (feature) => feature.properties.WADMKC || '';

    function draw() {
        if (layer) map.removeLayer(layer);
        const numeric = [...valueMap.values()].map((item) => item.numeric_value).filter((value) => value !== null).map(Number);
        const min = numeric.length ? Math.min(...numeric) : 0;
        const max = numeric.length ? Math.max(...numeric) : 0;
        const selected = areaSelect.value;

        document.getElementById('countValue').textContent = `${numeric.length} desa`;
        document.getElementById('minValue').textContent = numeric.length ? min.toLocaleString('id-ID') : '—';
        document.getElementById('maxValue').textContent = numeric.length ? max.toLocaleString('id-ID') : '—';

        layer = L.geoJSON(geometry, {
            filter: (feature) => !selected || codeOf(feature) === selected,
            style: (feature) => {
                const item = valueMap.get(codeOf(feature));
                return {
                    color: '#ffffff',
                    weight: 1.5,
                    fillColor: color(item?.numeric_value ?? null, min, max),
                    fillOpacity: 0.85
                };
            },
            onEachFeature: (feature, polygon) => {
                const item = valueMap.get(codeOf(feature));
                polygon.bindPopup(`
                    <div style="font-family:'Plus Jakarta Sans',sans-serif">
                        <p style="font-size:11px;font-weight:700;color:#94a3b8;margin:0 0 2px;text-transform:uppercase;letter-spacing:0.05em">Kecamatan ${escapeHtml(districtOf(feature))}</p>
                        <h4 style="font-size:15px;font-weight:800;color:#741421;margin:0">${escapeHtml(nameOf(feature))}</h4>
                        <div style="margin-top:10px;padding-top:10px;border-top:1px solid #f1f5f9">
                            <span style="font-size:11px;font-weight:600;color:#64748b">${escapeHtml(config.name)} (${escapeHtml(yearSelect.value)})</span>
                            <p style="font-size:22px;font-weight:900;color:#AD1D30;margin:4px 0 0;line-height:1.1">${escapeHtml(item?.raw_value ?? 'Belum tersedia')}</p>
                            <span style="font-size:11px;color:#94a3b8;font-weight:600">${escapeHtml(config.unit ?? '')}</span>
                        </div>
                    </div>
                `);
                polygon.on({
                    mouseover: (event) => event.target.setStyle({ weight: 2.5, color: '#741421', fillOpacity: 0.95 }),
                    mouseout: (event) => layer.resetStyle(event.target)
                });
            }
        }).addTo(map);

        if (layer.getLayers().length) {
            map.fitBounds(layer.getBounds(), { padding: [16, 16] });
        }
        status.textContent = numeric.length ? 'Peta Siap' : 'Data numerik belum tersedia';
        setTimeout(() => status.classList.add('hidden'), 1500);
    }

    async function load() {
        status.classList.remove('hidden');
        status.textContent = 'Memuat data spasial…';
        try {
            const response = await fetch(`${endpoint}?layer=${encodeURIComponent(layerSlug)}&year=${encodeURIComponent(yearSelect.value)}`, {
                headers: { Accept: 'application/json' }
            });
            if (!response.ok) throw new Error('Data layer tidak dapat dimuat');
            const payload = await response.json();
            config = payload.layer;
            valueMap = new Map(payload.values.map((item) => [item.area_code, item]));
            if (!geometry) {
                const geoResponse = await fetch(config.geojson_url);
                if (!geoResponse.ok) throw new Error('GeoJSON peta tidak dapat dimuat');
                geometry = await geoResponse.json();
            }
            draw();
        } catch (error) {
            status.textContent = error.message;
            status.classList.remove('hidden');
        }
    }

    yearSelect.addEventListener('change', load);
    areaSelect.addEventListener('change', draw);
    load();
    window.addEventListener('load', () => map.invalidateSize());
})();
</script>
@endpush
@endif
