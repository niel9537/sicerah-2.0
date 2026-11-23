<x-guest-layout>
    <div class="text-center mb-6">
<div class="flex justify-center mb-4">
    <img src="{{ asset('images/logo.png') }}"
         alt="Logo SICERAH"
         class="w-14 h-14 object-contain">
</div>
        <h1 class="text-2xl font-bold text-slate-800">SICERAH NIAS BARAT</h1>
        <p class="text-sm text-slate-500 mt-1">Sistem Informasi Capaian, Evaluasi, dan Perencanaan  Pembangunan Daerah</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email" class="text-slate-700 font-medium" />
            <x-text-input id="email" class="input mt-1" type="email"
                          name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Kata Sandi" class="text-slate-700 font-medium" />
            <x-text-input id="password" class="input mt-1" type="password"
                          name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="remember"
                       class="rounded border-slate-300 text-maroon-600 focus:ring-maroon-500" />
                <span class="text-sm text-slate-600">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm text-maroon-700 hover:text-maroon-900 font-medium">
                    Lupa kata sandi?
                </a>
            @endif
        </div>

        <button type="submit" class="btn-primary w-full justify-center py-3">
            Masuk ke Dasbor
        </button>
    </form>

    <p class="mt-6 text-center text-xs text-slate-400">
        © {{ date('Y') }} Pemerintah Kabupaten Nias Barat
    </p>
</x-guest-layout>