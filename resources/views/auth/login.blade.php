<x-guest-layout>
    <div class="text-center mb-7">
        <div class="flex items-center justify-center gap-3 mb-4">
            <img src="{{ asset('images/logo.png') }}"
                 alt="Logo Pemkab Nias Barat"
                 class="w-12 h-12 object-contain">
            <div class="h-8 w-px bg-slate-200"></div>
            <div class="logo-shimmer-wrapper">
                <img src="{{ asset('images/logo-sicerah.png') }}"
                     alt="Logo SICERAH"
                     class="h-12 w-auto max-w-[240px] object-contain">
            </div>
        </div>
        <p class="text-xs font-bold uppercase tracking-widest text-amber-800">Portal Masuk Terpadu</p>
        <p class="text-[11px] text-slate-500 mt-1 font-medium leading-relaxed">
            Sistem Cerdas Integrasi, Koordinasi, Evaluasi, Rekomendasi, Analisis, dan Harmonisasi Pembangunan
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4.5">
        @csrf

        <div>
            <x-input-label for="email" value="Email Pengguna" class="text-slate-700 font-bold text-xs uppercase tracking-wider" />
            <x-text-input id="email" class="input mt-1.5" type="email"
                          name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@opd.niasbarat.go.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password" value="Kata Sandi" class="text-slate-700 font-bold text-xs uppercase tracking-wider" />
            <x-text-input id="password" class="input mt-1.5" type="password"
                          name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="flex items-center justify-between pt-1">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember"
                       class="rounded-lg border-slate-300 text-amber-600 focus:ring-amber-500 w-4 h-4 transition" />
                <span class="text-xs font-medium text-slate-600">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                    class="text-xs text-amber-700 hover:text-amber-900 font-bold transition">
                    Lupa kata sandi?
                </a>
            @endif
        </div>

        <button type="submit" class="btn-primary w-full justify-center py-3 text-sm font-bold shadow-glow-gold mt-2">
            Masuk ke Dasbor
        </button>
    </form>

    <div class="mt-7 pt-5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
        <a href="{{ route('landing') }}" class="text-slate-500 hover:text-amber-700 font-semibold flex items-center gap-1 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Portal
        </a>
        <span>© {{ date('Y') }} Nias Barat</span>
    </div>
</x-guest-layout>
