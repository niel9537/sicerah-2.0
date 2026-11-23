<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SICERAH') — Bappelitbangda Nias Barat</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta name="description" content="@yield('meta_description', 'Portal publik indikator kinerja dan pembangunan Kabupaten Nias Barat.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600;1,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/public-portal.css') }}?v={{ filemtime(public_path('assets/public-portal.css')) }}">
    @stack('head')

    <style>
        :root {
            --sc-red: #AD1D30;
            --sc-red-dark: #861827;
            --sc-red-deep: #68121D;
            --sc-navy: #191716;
            --sc-navy-dark: #121212;
            --sc-gold: #D4A017;
            --sc-gold-light: #FFDA65;
            --sc-bg: #FBF8F4;
        }
        html, body {
            font-family: 'Plus Jakarta Sans', Inter, ui-sans-serif, system-ui, sans-serif;
            font-style: normal;
            background-color: #FBF8F4;
            color: #121212;
            overflow-x: clip;
        }
        em, i, .italic { font-style: normal !important; }
        [x-cloak] { display: none !important; }
        :focus-visible { outline: 3px solid rgba(185, 28, 28, 0.4); outline-offset: 2px; border-radius: .75rem; }
        .reveal { opacity: 0; transform: translateY(14px); transition: opacity .55s cubic-bezier(0.16, 1, 0.3, 1), transform .55s cubic-bezier(0.16, 1, 0.3, 1); }
        .reveal.is-visible { opacity: 1; transform: none; }
        .public-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 1.25rem;
            box-shadow: 0 2px 14px -2px rgba(185, 28, 28, 0.04), 0 1px 3px -1px rgba(25, 23, 22, 0.02);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .public-card:hover {
            box-shadow: 0 10px 28px -4px rgba(185, 28, 28, 0.08), 0 3px 8px -2px rgba(25, 23, 22, 0.03);
        }
        .indicator-modal-shell {
            position: fixed !important;
            inset: 0 !important;
            z-index: 2147483600 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            isolation: isolate;
        }
        .indicator-modal-backdrop {
            position: absolute !important;
            inset: 0 !important;
            background: rgba(25, 23, 22, 0.72) !important;
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
        }
        .indicator-modal-panel {
            position: relative !important;
            z-index: 1 !important;
            width: min(920px, calc(100vw - 32px)) !important;
            max-height: calc(100dvh - 32px) !important;
            overflow-y: auto !important;
            margin: auto !important;
            background: #fff !important;
            border-radius: 24px !important;
            box-shadow: 0 28px 80px -22px rgba(185, 28, 28, 0.35) !important;
            overscroll-behavior: contain;
        }
        .indicator-chart-wrap { height: 320px; position: relative; }
        .public-bottom-nav {
            position: fixed !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            z-index: 2147483000 !important;
            width: 100vw !important;
            height: 72px !important;
            min-height: 72px !important;
            padding: 0 4px 4px !important;
            box-sizing: border-box !important;
            background: #ffffff !important;
            border-top: 1px solid rgba(226, 232, 240, 0.9) !important;
            box-shadow: 0 -4px 24px rgba(185, 28, 28, 0.07) !important;
        }
        .public-bottom-nav-items {
            display: grid !important;
            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
            height: 100%;
            width: 100%;
            align-items: stretch;
        }
        .public-bottom-nav-items > a {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 1px !important;
            min-width: 0 !important;
            position: relative !important;
        }
        body { padding-bottom: 0 !important; }
        @media(max-width: 639px) {
            .indicator-modal-shell { align-items: flex-end; padding: 0; }
            .indicator-modal-panel { width: 100% !important; max-height: 92dvh !important; margin: 0 !important; border-radius: 24px 24px 0 0 !important; }
            .indicator-chart-wrap { height: 260px; }
        }
        @media(min-width: 1024px) { .public-bottom-nav { display: none !important; } }
        @media(prefers-reduced-motion: reduce) {
            .reveal { opacity: 1 !important; transform: none !important; transition: none !important; }
            * { scroll-behavior: auto !important; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/sicerah-motion.css') }}?v={{ filemtime(public_path('assets/sicerah-motion.css')) }}">
</head>
<body class="antialiased bg-[#FBF8F4] text-[#121212] pb-20 lg:pb-0 selection:bg-[#D4A017] selection:text-white">
    @include('layouts.partials.page-loader')

    @include('layouts.partials.public-header')

    <main>@yield('content')</main>

    {{-- Footer Berwarna Navy (#191716) dengan Aksen Emas & Motif Budaya Nias --}}
    <footer class="sicerah-footer mt-24 bg-[#191716] text-white border-t-2 border-[#D4A017] relative overflow-hidden">
        {{-- Background Ornament Motif Khas Nias --}}
        <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: radial-gradient(circle at 20% 30%, #D4A017 1px, transparent 1px), radial-gradient(circle at 80% 70%, #D4A017 1px, transparent 1px); background-size: 32px 32px;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 items-start">
                {{-- Kolom 1 & 2: Identitas Pemkab & SICERAH --}}
                <div class="lg:col-span-2 space-y-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Pemkab Nias Barat" class="w-11 h-11 object-contain drop-shadow">
                        <div class="leading-tight">
                            <p class="font-black text-white text-[13px] tracking-tight uppercase">BAPPELITBANGDA</p>
                            <p class="font-semibold text-slate-300 text-xs">Kabupaten Nias Barat</p>
                        </div>
                        <div class="h-9 w-px bg-white/20 mx-1"></div>
                        <div class="logo-shimmer-wrapper logo-shimmer-dark">
                            <img src="{{ asset('images/logo-sicerah-dark.png') }}" alt="SICERAH" class="h-10 w-auto max-w-[200px] object-contain">
                        </div>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed font-normal">
                        <span class="font-bold text-[#FFDA65] block mb-0.5">SISTEM CERDAS INTEGRASI, KOORDINASI, EVALUASI, REKOMENDASI, ANALISIS, DAN HARMONISASI PEMBANGUNAN</span>
                        Portal integrasi data perencanaan, evaluasi kinerja pembangunan, dan geospasial resmi Bappelitbangda Kabupaten Nias Barat.
                    </p>
                </div>

                {{-- Kolom 3: Data Terbuka --}}
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white/10 text-[#FFDA65] flex items-center justify-center shrink-0 border border-white/15">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7M4 7c0-2 1.5-3 3.5-3h9c2 0 3.5 1 3.5 3M4 7c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3M4 12c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3"/></svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-white text-xs tracking-wide">Data Terbuka</h4>
                        <p class="text-[11px] text-slate-300 mt-1 leading-normal">
                            Akses data pembangunan daerah yang terbuka, terstandar, dan akuntabel.
                        </p>
                    </div>
                </div>

                {{-- Kolom 4: Transparansi & Perencanaan Berbasis Data --}}
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white/10 text-[#FFDA65] flex items-center justify-center shrink-0 border border-white/15">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-white text-xs tracking-wide">Transparansi</h4>
                        <p class="text-[11px] text-slate-300 mt-1 leading-normal">
                            Mendorong akuntabilitas tata kelola dan keterbukaan informasi publik.
                        </p>
                    </div>
                </div>

                {{-- Kolom 5: Alamat Kantor & Siluet Budaya Nias --}}
                <div class="flex items-start gap-3 relative">
                    <div class="w-9 h-9 rounded-xl bg-white/10 text-[#FFDA65] flex items-center justify-center shrink-0 border border-white/15">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-white text-xs tracking-wide">Kantor Bappelitbangda</h4>
                        <p class="text-[11px] text-slate-300 mt-1 leading-normal">
                            Lahomi, Kabupaten Nias Barat<br>
                            Provinsi Sumatera Utara
                        </p>
                    </div>
                    <img src="{{ asset('images/ref-footer-house.png') }}" alt="Budaya Nias" class="hidden xl:block absolute right-0 -bottom-2 w-24 h-auto opacity-20 pointer-events-none select-none">
                </div>
            </div>

            {{-- Bottom copyright line --}}
            <div class="mt-8 pt-4 border-t border-white/10 flex flex-wrap items-center justify-between gap-3 text-[11px] text-slate-400">
                <p>© {{ date('Y') }} Pemerintah Kabupaten Nias Barat — Hak Cipta Dilindungi.</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('landing') }}#indikator" class="hover:text-[#FFDA65] transition">Bank Data IKD</a>
                    <span>•</span>
                    <a href="{{ route('webgis.index') }}" class="hover:text-[#FFDA65] transition">Peta WebGIS</a>
                    <span>•</span>
                    <a href="{{ route('jdih.index') }}" class="hover:text-[#FFDA65] transition">JDIH Perencanaan</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Bottom Navigation for Mobile --}}


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const elements = document.querySelectorAll('.reveal');
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
                elements.forEach((element) => element.classList.add('is-visible'));
                return;
            }
            const observer = new IntersectionObserver((entries) => entries.forEach((entry) => {
                if (entry.isIntersecting) { entry.target.classList.add('is-visible'); observer.unobserve(entry.target); }
            }), { threshold: .08 });
            elements.forEach((element) => observer.observe(element));
        });
    </script>
    @stack('scripts')
</body>
</html>
