<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dasbor') — {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div x-data="{ sidebarOpen: false, profileOpen: false }" class="min-h-screen bg-slate-50">

        {{-- ================= SIDEBAR ================= --}}
        @include('layouts.partials.sidebar')

        {{-- Overlay untuk mobile --}}
        <div x-show="sidebarOpen"
             x-transition.opacity
             @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-slate-900/50 backdrop-blur-sm lg:hidden"
             x-cloak></div>

        {{-- ================= MAIN AREA ================= --}}
        <div class="lg:pl-64 flex flex-col min-h-screen">

            @include('layouts.partials.navbar')

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
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

            <footer class="px-6 py-4 border-t border-slate-100 bg-white text-xs text-slate-500 flex flex-wrap items-center justify-between gap-2">
                <p>© {{ date('Y') }} Pemerintah Kabupaten Nias Barat — SICERAH v1.0</p>
                <p>Dibangun dengan Laravel {{ app()->version() }}</p>
            </footer>
        </div>
    </div>
</body>
</html>