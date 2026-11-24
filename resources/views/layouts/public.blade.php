<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.partials.seo')
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

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
<body class="{{ request()->routeIs('landing') ? 'portal-home' : '' }} antialiased bg-[#FBF8F4] text-[#121212] pb-20 lg:pb-0 selection:bg-[#D4A017] selection:text-white">
    @include('layouts.partials.page-loader')

    @include('layouts.partials.public-header')

    <main>@yield('content')</main>

    {{-- Footer Berwarna Navy (#191716) dengan Aksen Emas & Motif Budaya Nias --}}
    <footer class="sicerah-footer portal-footer">
        <div class="portal-footer-inner">
            <a class="portal-footer-brand" href="{{ route('landing') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Lambang Kabupaten Nias Barat" width="44" height="44">
                <span><strong>BAPPELITBANGDA</strong><span>Kabupaten Nias Barat</span></span>
            </a>
            <p class="portal-footer-description">SICERAH &middot; Portal data dan perencanaan pembangunan daerah.</p>
            <p class="portal-footer-address">Lahomi, Nias Barat, Sumatera Utara</p>
        </div>
        <div class="portal-footer-bottom">
            <p>&copy; {{ date('Y') }} Pemerintah Kabupaten Nias Barat</p>
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
