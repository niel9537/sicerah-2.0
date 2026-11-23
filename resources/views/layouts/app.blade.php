<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dasbor') — {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600;1,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-[#fbfaf8] text-slate-800 font-sans selection:bg-amber-500 selection:text-slate-950">
    <div x-data="{ sidebarOpen: false, profileOpen: false }" class="min-h-screen bg-[#fbfaf8]">

        {{-- ================= SIDEBAR ================= --}}
        @include('layouts.partials.sidebar')

        {{-- Overlay mobile --}}
        <div x-show="sidebarOpen"
             x-transition.opacity
             @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-slate-950/60 backdrop-blur-sm lg:hidden"
             x-cloak></div>

        {{-- ================= MAIN AREA ================= --}}
        <div class="lg:pl-64 flex flex-col min-h-screen">

            @include('layouts.partials.navbar')

            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                @if(session('success'))
                    <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
                @endif
                @if(session('error'))
                    <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
                @endif
                @if($errors->any())
                    <x-ui.alert type="error">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </x-ui.alert>
                @endif

                @yield('content')
            </main>

            <footer class="sicerah-admin-footer px-6 py-4 border-t border-slate-200/70 bg-white/80 backdrop-blur text-xs text-slate-500 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <p>© {{ date('Y') }} Pemerintah Kabupaten Nias Barat — SICERAH v1.0</p>
                </div>
                <p class="text-slate-400">Bappelitbangda Kabupaten Nias Barat</p>
            </footer>
        </div>
    </div>
</body>
</html>
