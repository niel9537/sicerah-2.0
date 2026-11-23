<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <meta name="robots" content="noindex, follow">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600;1,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/sicerah-motion.css') }}?v={{ filemtime(public_path('assets/sicerah-motion.css')) }}">
</head>
<body class="antialiased font-sans bg-[#fbfaf8] text-slate-800 selection:bg-amber-500 selection:text-slate-950">
    @include('layouts.partials.page-loader')
    <div class="min-h-screen relative flex items-center justify-center p-4 sm:p-6 overflow-hidden">
        {{-- Background decorative ambient glow --}}
        <div class="absolute inset-0 bg-gradient-to-br from-amber-50/40 via-white to-slate-50"></div>
        <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-gradient-to-tr from-amber-500/20 to-yellow-400/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-gradient-to-br from-amber-600/15 to-transparent blur-3xl pointer-events-none"></div>

        <div class="relative w-full max-w-md">
            <div class="bg-white/95 backdrop-blur-xl rounded-3xl p-7 sm:p-9 shadow-[0_20px_50px_rgba(15,23,42,0.08)] border border-slate-100/90">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
