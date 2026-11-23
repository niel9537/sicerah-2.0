<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="min-h-screen relative flex items-center justify-center p-4 overflow-hidden">
        {{-- Background gradient decorative --}}
        <div class="absolute inset-0 bg-gradient-to-br from-maroon-50 via-white to-slate-50"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-brand-gradient opacity-20 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-maroon-400/30 blur-3xl"></div>

        <div class="relative w-full max-w-md">
            <div class="card p-8 backdrop-blur-sm">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>