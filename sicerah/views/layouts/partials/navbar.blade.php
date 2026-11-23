<header class="sticky top-0 z-20 bg-white/85 backdrop-blur border-b border-slate-100">
    <div class="h-16 flex items-center justify-between px-4 sm:px-6">
        {{-- Toggle sidebar mobile --}}
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden p-2 rounded-xl hover:bg-slate-100">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div>
                <h1 class="font-semibold text-slate-800 text-sm sm:text-base">@yield('title', 'Dasbor')</h1>
                <p class="text-xs text-slate-500 hidden sm:block">@yield('subtitle', 'Selamat datang di SICERAH Nias Barat')</p>
            </div>
        </div>

        {{-- Kanan --}}
        <div class="flex items-center gap-2 sm:gap-3">
            {{-- Periode aktif --}}
            @php
                $aktif = \App\Models\Periode::aktif()->orderByDesc('tahun')->orderByDesc('triwulan')->first();
            @endphp
            @if($aktif)
                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-maroon-50 text-maroon-700 text-xs font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Periode Aktif: {{ $aktif->label }}
                </span>
            @endif

            {{-- User dropdown --}}
            <div class="relative">
                <button @click="profileOpen = !profileOpen" @click.outside="profileOpen = false"
                        class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-100 transition">
                    <div class="w-8 h-8 rounded-lg bg-brand-gradient text-white flex items-center justify-center text-sm font-semibold">
                        {{ strtoupper(mb_substr(auth()->user()->nama ?? 'U', 0, 1)) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-xs font-medium text-slate-800 leading-tight">{{ auth()->user()->nama }}</p>
                        <p class="text-[10px] text-slate-500">{{ auth()->user()->email }}</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="profileOpen" x-transition x-cloak
                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-2 z-30">
                    <div class="px-4 py-2 border-b border-slate-100">
                        <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->nama }}</p>
                        <p class="text-xs text-slate-500">{{ auth()->user()->roleLabel() }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                        Profil Saya
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>