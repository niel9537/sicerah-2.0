<header class="sicerah-admin-navbar sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs transition-all duration-200">
    <div class="h-20 flex items-center justify-between px-4 sm:px-6 lg:px-8">
        {{-- Toggle sidebar mobile & page titles --}}
        <div class="flex items-center gap-3.5">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden p-2.5 rounded-xl hover:bg-slate-100 text-slate-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div>
                <h1 class="font-extrabold text-slate-900 text-lg sm:text-xl tracking-tight">@yield('title', 'Dasbor')</h1>
                <p class="text-xs text-slate-500 hidden sm:block font-medium">@yield('subtitle', 'Sistem Cerdas Integrasi, Koordinasi, Evaluasi, Rekomendasi, Analisis, dan Harmonisasi Pembangunan')</p>
            </div>
        </div>

        {{-- Right Section (Inspired by EGOVERN top-right header) --}}
        <div class="flex items-center gap-2.5 sm:gap-4">
            {{-- Periode aktif --}}
            @php
                $aktif = \App\Models\Periode::aktif()->orderByDesc('tahun')->orderByDesc('triwulan')->first();
            @endphp
            @if($aktif && auth()->user()->role !== 'inspektorat')
                <span class="hidden md:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-amber-50/80 border border-amber-200/60 text-amber-800 text-xs font-semibold shadow-xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Periode: {{ $aktif->label }}</span>
                </span>
            @endif

            {{-- Notification bell button (EGOVERN reference) --}}
            <button type="button" class="relative w-10 h-10 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-center text-slate-600 hover:text-amber-800 hover:border-amber-300 hover:bg-amber-50/40 transition">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-amber-500 ring-2 ring-white"></span>
            </button>

            {{-- User profile chip (EGOVERN reference) --}}
            <div class="relative">
                <button @click="profileOpen = !profileOpen" @click.outside="profileOpen = false"
                        class="flex items-center gap-3 p-1.5 sm:px-3 sm:py-2 rounded-2xl hover:bg-slate-100/80 border border-transparent hover:border-slate-200/60 transition">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 text-slate-950 flex items-center justify-center text-sm font-extrabold shadow-sm">
                        {{ strtoupper(mb_substr(auth()->user()->nama ?? 'U', 0, 1)) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-xs font-bold text-slate-900 leading-tight">{{ auth()->user()->nama }}</p>
                        <p class="text-[10px] text-slate-500 font-medium">{{ auth()->user()->roleLabel() }}</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="profileOpen" x-transition x-cloak
                     class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 p-2 z-30">
                    <div class="px-3 py-2 border-b border-slate-100 mb-1">
                        <p class="text-sm font-bold text-slate-900">{{ auth()->user()->nama }}</p>
                        <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                    </div>
                    @if(auth()->user()->role !== 'inspektorat')
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profil Saya
                    </a>
                    <a href="{{ route('landing') }}" target="_blank" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Lihat Portal Publik
                    </a>
                    @endif
                    <div class="border-t border-slate-100 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
