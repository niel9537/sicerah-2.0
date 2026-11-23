@php
    $navGroups = [
        [
            'label' => 'Menu',
            'items' => [
                ['label' => 'Dasbor', 'route' => 'dashboard', 'icon' => 'home'],
                ['label' => 'Data BPS Nias Barat', 'route' => 'bps.index', 'icon' => 'chart'],
            ],
        ],
        [
            'label' => 'Pelaporan & Kinerja',
            'items' => [
                ['label' => 'Evaluasi Renja',    'route' => 'evaluasi.index',   'icon' => 'clipboard', 'roles' => ['operator_opd', 'admin_bappeda', 'super_admin']],
                ['label' => 'Verifikasi',        'route' => 'verifikasi.index', 'icon' => 'shield',     'roles' => ['verifikator', 'admin_bappeda', 'super_admin']],
                ['label' => 'LKPJ',              'route' => 'lkpj.index',       'icon' => 'book',       'roles' => ['operator_opd', 'admin_bappeda', 'super_admin']],
                ['label' => 'eMonev ProSN Tahun 2026', 'route' => 'prosn.index', 'icon' => 'chart'],
                ['label' => 'Realisasi Kinerja', 'route' => 'realisasi.index',  'icon' => 'chart'],
                ['label' => 'Form 81 (E60)',     'route' => 'form81.index',     'icon' => 'document'],
                ['label' => 'WebGIS Publik',     'route' => 'webgis.index',     'icon' => 'map'],
            ],
        ],
        [
            'label' => 'RPJMD & Indikator',
            'roles' => ['super_admin', 'admin_bappeda'],
            'items' => [
                ['label' => 'Indikator RPJMD',         'route' => 'rpjmd.index',            'icon' => 'book'],
                ['label' => 'Kelola WebGIS',            'route' => 'webgis.admin.index',     'icon' => 'map'],
                ['label' => 'Pendidikan Kecamatan',     'route' => 'webgis.education.index', 'icon' => 'book'],
            ],
        ],
        [
            'label' => 'Master Data',
            'roles' => ['super_admin', 'admin_bappeda'],
            'items' => [
                ['label' => 'SKPD / OPD',     'route' => 'opd.index',            'icon' => 'building'],
                ['label' => 'Urusan',         'route' => 'urusan.index',         'icon' => 'briefcase'],
                ['label' => 'Program',        'route' => 'program.index',        'icon' => 'layers'],
                ['label' => 'Kegiatan',       'route' => 'kegiatan.index',       'icon' => 'squares'],
                ['label' => 'Sub Kegiatan',   'route' => 'subkegiatan.index',    'icon' => 'square'],
                ['label' => 'Target Tahunan', 'route' => 'target-tahunan.index', 'icon' => 'target'],
                ['label' => 'Periode',        'route' => 'periode.index',        'icon' => 'calendar'],
            ],
        ],
        [
            'label' => 'CMS Publikasi',
            'roles' => ['super_admin'],
            'items' => [
                ['label' => 'Kelola Publikasi', 'route' => 'cms.publications.index', 'icon' => 'document'],
            ],
        ],
        [
            'label' => 'Pengaturan',
            'roles' => ['super_admin', 'admin_bappeda'],
            'items' => [
                ['label' => 'Pengguna', 'route' => 'user.index',  'icon' => 'user'],
                ['label' => 'Audit Log','route' => 'audit.index', 'icon' => 'shield'],
            ],
        ],
    ];

    $icons = [
        'home'     => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
        'clipboard'=> '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>',
        'chart'    => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
        'document' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
        'building' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
        'briefcase'=> '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
        'layers'   => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>',
        'squares'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>',
        'square'   => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>',
        'calendar' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
        'target'   => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>',
        'book'     => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
        'user'     => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
        'shield'   => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M12 21a9 9 0 100-18 9 9 0 000 18z"/></svg>',
        'map'      => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18l-6 3V6l6-3 6 3 6-3v15l-6 3-6-3zm0 0V3m6 18V6"/></svg>',
    ];
    $currentRole = auth()->user()?->role;
    if ($currentRole === 'inspektorat') {
        $navGroups = [['label' => 'Pelaporan & Kinerja', 'items' => [
            ['label'=>'eMonev ProSN Tahun 2026','route'=>'prosn.index','icon'=>'chart'],
        ]]];
    }
@endphp

<aside x-cloak
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="admin-sidebar fixed inset-y-0 left-0 z-40 w-64 bg-[#300810] border-r border-white/5 transition-transform duration-300 lg:translate-x-0 flex flex-col shadow-2xl">

    {{-- Brand Header with Official SICERAH Logo --}}
    <div class="flex items-center justify-center h-24 px-5 border-b border-white/5 bg-[#300810]">
        <a href="{{ route($currentRole === 'inspektorat' ? 'prosn.index' : 'dashboard') }}" class="flex items-center justify-center w-full group py-2">
            <div class="logo-shimmer-wrapper logo-shimmer-dark">
                <img src="{{ asset('images/logo-sicerah-dark.png') }}"
                     alt="SICERAH Nias Barat"
                     class="h-12 w-auto max-w-full object-contain filter drop-shadow-[0_2px_12px_rgba(217,167,46,0.25)] transition-transform group-hover:scale-105">
            </div>
        </a>
    </div>

    {{-- Navigation Groups --}}
    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-6">
        @foreach($navGroups as $group)
            @continue(isset($group['roles']) && !in_array($currentRole, $group['roles'], true))

            <div>
                @if(!empty($group['label']))
                    <p class="px-3 text-[10px] font-bold uppercase tracking-widest text-zinc-500 mb-2.5">{{ $group['label'] }}</p>
                @endif

                <ul class="space-y-1.5">
                    @foreach($group['items'] as $item)
                        @continue(isset($item['roles']) && !in_array($currentRole, $item['roles'], true))
                        @php
                            $exists = \Illuminate\Support\Facades\Route::has($item['route']);
                            $url    = $exists ? route($item['route'], $item['params'] ?? []) : '#';
                            $active = $exists && request()->routeIs($item['route'] === 'prosn.index' ? 'prosn.*' : ($item['route'] === 'cms.publications.index' ? 'cms.publications.*' : $item['route'] . '*'))
                                && (!isset($item['params']['jenis']) || request('jenis', 'IKD') === $item['params']['jenis']);
                        @endphp
                        <li>
                            <a href="{{ $url }}" class="sidebar-link {{ $active ? 'sidebar-link-active' : '' }}">
                                <span class="{{ $active ? 'text-slate-950' : 'text-zinc-400 group-hover:text-white' }}">
                                    {!! $icons[$item['icon']] ?? '' !!}
                                </span>
                                <span class="font-medium tracking-tight {{ $active ? 'text-slate-950 font-bold' : '' }}">{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    {{-- Bottom User Profile Card (EGOVERN reference) --}}
    <div class="p-4 border-t border-white/5 bg-[#300810]">
        <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-white/5 border border-white/5">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 text-slate-950 flex items-center justify-center font-extrabold text-sm shadow-md">
                {{ strtoupper(mb_substr(auth()->user()->nama ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->nama }}</p>
                <p class="text-[10px] text-zinc-400 truncate">{{ auth()->user()->roleLabel() }}</p>
            </div>
            @if($currentRole !== 'inspektorat')
            <a href="{{ route('profile.edit') }}" title="Pengaturan Akun" class="text-zinc-400 hover:text-white p-1 rounded-lg hover:bg-white/5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </a>
            @endif
        </div>
    </div>
</aside>
