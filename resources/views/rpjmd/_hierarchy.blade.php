<div class="space-y-6">
    <div class="rounded-3xl bg-gradient-to-br from-[#68121D] to-[#AD1D30] p-6 sm:p-8 text-white shadow-lg">
        <p class="text-sm font-bold uppercase tracking-widest text-amber-200">Visi Kabupaten Nias Barat</p>
        <h3 class="mt-3 text-2xl sm:text-3xl font-extrabold leading-snug">{{ $rpjmd['visi'] }}</h3>
        <p class="mt-3 text-base text-white/90">CERAH: Cerdas, Sejahtera, dan Sehat</p>
        <p class="mt-5 text-sm text-amber-100">5 misi pembangunan · 5 tujuan daerah</p>
    </div>
    <ol class="space-y-4 border-l-2 border-red-100 pl-4 sm:pl-6 ml-2">
        @foreach($rpjmd['pairs'] as $pair)
        <li class="relative rounded-2xl border border-slate-200 bg-white shadow-sm overflow-visible">
            <span aria-hidden="true" class="absolute -left-6 sm:-left-8 top-7 h-3 w-3 rounded-full bg-[#AD1D30] ring-4 ring-white"></span>
            <div class="grid md:grid-cols-2">
                <div class="p-5 sm:p-6">
                    <p class="text-sm font-extrabold uppercase tracking-wider text-[#AD1D30]">Misi {{ $loop->iteration }}</p>
                    <p class="mt-3 text-base leading-relaxed text-slate-800">{{ $pair['misi'] }}</p>
                </div>
                <div class="p-5 sm:p-6 bg-slate-50 border-t md:border-t-0 md:border-l border-slate-200 rounded-b-2xl md:rounded-bl-none md:rounded-r-2xl">
                    <p class="text-sm font-extrabold uppercase tracking-wider text-slate-600">Tujuan {{ $loop->iteration }}</p>
                    <p class="mt-3 text-base leading-relaxed text-slate-800">{{ $pair['tujuan'] }}</p>
                </div>
            </div>
            @include('rpjmd._indicators', ['rows' => collect($hierarchyRows ?? [])->where('pair', $loop->index)])
        </li>
        @endforeach
    </ol>
    <p class="text-sm text-slate-500">Sumber: Tabel 3.5 Teknik Merumuskan Tujuan dan Sasaran, Final RPJMD Pemerintah Kabupaten Nias Barat.</p>
</div>
