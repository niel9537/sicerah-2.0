@extends('layouts.public')

@section('title', 'Portal Kinerja Daerah')

@section('content')
<div x-data="indicatorExplorer()">
    <section class="relative overflow-hidden bg-[#4a0e18] text-white">
        <div class="absolute inset-0 opacity-30" style="background-image:radial-gradient(circle at 18% 12%,rgba(217,167,46,.7),transparent 26%),radial-gradient(circle at 85% 25%,rgba(255,255,255,.2),transparent 24%),linear-gradient(135deg,transparent 45%,rgba(255,255,255,.06) 45%,rgba(255,255,255,.06) 46%,transparent 46%)"></div>
        <div class="relative max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 py-14 sm:py-20 lg:py-24 grid lg:grid-cols-[1.15fr_.85fr] gap-10 items-center">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/15 px-3 py-1.5 text-xs font-semibold text-white/85">Portal Data Pembangunan Nias Barat</div>
                <h1 class="mt-5 text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-[-.045em] leading-[1.05] max-w-4xl">Data kinerja daerah yang mudah dibaca publik.</h1>
                <p class="mt-5 text-sm sm:text-base leading-7 text-white/72 max-w-2xl">Telusuri IKD, IKU, indikator makro, kinerja program, dan peta tematik dalam satu portal. Seluruh indikator utama di halaman ini bersumber dari Bank Data.xlsx.</p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="#indikator" class="inline-flex items-center justify-center rounded-xl bg-[#e2b442] text-[#321018] px-5 py-3 text-sm font-bold hover:bg-[#f0c85f]">Jelajahi indikator</a>
                    <a href="{{ route('webgis.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white/10 border border-white/20 px-5 py-3 text-sm font-semibold hover:bg-white/15">Buka WebGIS</a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                @foreach([
                    ['value' => $counts['ikd'], 'label' => 'IKD Bank Data'],
                    ['value' => $counts['iku'], 'label' => 'IKU'],
                    ['value' => $counts['makro'], 'label' => 'Indikator Makro'],
                    ['value' => $counts['aspek'], 'label' => 'Aspek Pembangunan'],
                ] as $stat)
                    <div class="rounded-2xl sm:rounded-3xl border border-white/15 bg-white/10 backdrop-blur p-4 sm:p-6">
                        <p class="text-3xl sm:text-5xl font-extrabold tabular-nums text-white">{{ $stat['value'] }}</p>
                        <p class="mt-2 text-xs sm:text-sm text-white/65">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 -mt-6 relative z-10">
        <div class="public-card p-4 sm:p-5 grid sm:grid-cols-[1fr_auto] gap-4 items-center">
            <div><p class="text-xs font-semibold uppercase tracking-[.16em] text-maroon-700">Tahun tampilan</p><p class="text-sm text-slate-500 mt-1">Capaian digunakan bila tersedia. Jika belum, sistem menampilkan target.</p></div>
            <form method="GET" action="{{ route('landing') }}" class="flex gap-2">
                <select name="tahun" class="select min-w-28" aria-label="Pilih tahun">
                    @foreach($tahunRange as $year)<option value="{{ $year }}" @selected($tahun === $year)>{{ $year }}</option>@endforeach
                </select>
                <button class="btn-primary">Tampilkan</button>
            </form>
        </div>
    </section>

    <section id="indikator" class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 pt-20 scroll-mt-20">
        <div class="max-w-3xl">
            <p class="text-xs font-bold uppercase tracking-[.18em] text-maroon-700">Bank Data Kabupaten</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">119 indikator dalam empat aspek</h2>
            <p class="mt-3 text-sm sm:text-base leading-7 text-slate-600">Angka desimal, rentang, dan teks disimpan apa adanya. Nilai numerik termasuk rentang tetap dapat dibuka sebagai grafik tren 2020–2030.</p>
        </div>

        <div class="mt-7 public-card p-4 sm:p-5 sticky top-14 lg:top-[4.5rem] z-20">
            <div class="grid lg:grid-cols-[1fr_auto] gap-3">
                <label class="relative block">
                    <span class="sr-only">Cari indikator</span>
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg>
                    <input x-model.debounce.200ms="search" type="search" class="input pl-11" placeholder="Cari nama indikator, aspek, atau sumber data">
                </label>
                <div class="flex gap-2 overflow-x-auto pb-1 lg:pb-0">
                    @foreach([['all','Semua'],['iku','IKU'],['makro','Makro']] as [$key,$label])
                        <button type="button" @click="group='{{ $key }}'" :class="group==='{{ $key }}' ? 'bg-maroon-800 text-white' : 'bg-slate-100 text-slate-600'" class="px-4 py-2.5 rounded-xl text-sm font-semibold whitespace-nowrap">{{ $label }}</button>
                    @endforeach
                </div>
            </div>
            <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                <button type="button" @click="aspect='all'" :class="aspect==='all' ? 'border-maroon-700 text-maroon-800 bg-maroon-50' : 'border-slate-200 text-slate-500 bg-white'" class="px-3 py-2 rounded-xl border text-xs font-semibold whitespace-nowrap">Semua aspek</button>
                @foreach($aspek as $namaAspek => $jumlah)
                    <button type="button" @click="aspect=@js($namaAspek)" :class="aspect===@js($namaAspek) ? 'border-maroon-700 text-maroon-800 bg-maroon-50' : 'border-slate-200 text-slate-500 bg-white'" class="px-3 py-2 rounded-xl border text-xs font-semibold whitespace-nowrap">{{ Str::headline(Str::lower(str_replace('ASPEK ', '', $namaAspek))) }} · {{ $jumlah }}</button>
                @endforeach
            </div>
        </div>

        <div class="mt-5 flex items-center justify-between text-sm text-slate-500"><p><span class="font-bold text-slate-800" x-text="filtered.length"></span> indikator ditemukan</p><p class="hidden sm:block">Klik kartu untuk melihat grafik</p></div>

        <div class="mt-4 grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
            <template x-for="item in filtered" :key="item.id">
                <button type="button" @click="openIndicator(item)" class="public-card p-5 text-left hover:-translate-y-0.5 hover:shadow-xl transition group">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex flex-wrap gap-1.5">
                            <span x-show="item.is_iku" class="badge-maroon">IKU</span>
                            <span x-show="item.is_makro" class="badge-success">Makro</span>
                            <span class="badge-default">IKD</span>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-400" x-text="item.kode"></span>
                    </div>
                    <h3 class="mt-4 text-sm font-bold leading-6 text-slate-800 group-hover:text-maroon-800" x-text="item.nama"></h3>
                    <p class="mt-1 text-xs text-slate-500" x-text="shortAspect(item.aspek)"></p>
                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-end justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[10px] uppercase tracking-[.14em] text-slate-400" x-text="item.selected.source === 'capaian' ? 'Capaian {{ $tahun }}' : 'Target {{ $tahun }}'"></p>
                            <p class="mt-1 text-xl font-extrabold text-slate-900 break-words" x-text="item.selected.raw || 'Belum tersedia'"></p>
                            <p class="text-xs text-slate-500 mt-0.5" x-text="item.satuan || ''"></p>
                        </div>
                        <span x-show="item.has_chart" class="w-10 h-10 rounded-xl bg-maroon-50 text-maroon-800 flex items-center justify-center shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19V9m5 10V5m5 14v-7m5 7V3"/></svg></span>
                    </div>
                </button>
            </template>
        </div>

        <div x-show="filtered.length === 0" class="public-card p-10 text-center mt-4"><p class="font-bold text-slate-800">Indikator tidak ditemukan</p><p class="text-sm text-slate-500 mt-1">Ubah kata pencarian atau filter aspek.</p></div>
    </section>

    @if($indikatorMakro->isNotEmpty())
    <section class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 pt-20">
        <p class="text-xs font-bold uppercase tracking-[.18em] text-maroon-700">Sorotan RPJMD</p>
        <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Indikator makro</h2>
        <div class="mt-7 grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            @foreach($indikatorMakro as $item)
                <button type="button" @click='openIndicator(@js($item))' class="public-card p-4 sm:p-6 text-left hover:-translate-y-0.5 transition">
                    <p class="text-xs text-slate-500 leading-5 min-h-10">{{ $item['nama'] }}</p>
                    <p class="mt-3 text-2xl sm:text-3xl font-extrabold text-maroon-800 break-words">{{ $item['selected']['raw'] ?: '—' }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $item['satuan'] }} · {{ $item['selected']['source'] === 'capaian' ? 'capaian' : 'target' }} {{ $tahun }}</p>
                </button>
            @endforeach
        </div>
    </section>
    @endif

    <section id="kinerja" class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 pt-20 scroll-mt-20">
        <div class="grid lg:grid-cols-3 gap-5">
            <div class="lg:col-span-1 rounded-3xl bg-[#4a0e18] text-white p-6 sm:p-8">
                <p class="text-xs font-bold uppercase tracking-[.18em] text-[#e2b442]">Kinerja Program</p>
                <h2 class="mt-3 text-3xl font-extrabold">Realisasi tahun {{ $tahun }}</h2>
                <p class="mt-3 text-sm leading-6 text-white/65">Ringkasan fisik dan keuangan pada triwulan {{ ['I','II','III','IV'][$kinerja['triwulan']-1] }}.</p>
                <div class="mt-7 grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-white/10 p-4"><p class="text-3xl font-extrabold">{{ number_format($kinerja['rata_fisik'],1,',','.') }}%</p><p class="text-xs text-white/60 mt-1">Rata-rata fisik</p></div>
                    <div class="rounded-2xl bg-white/10 p-4"><p class="text-3xl font-extrabold">{{ number_format($kinerja['rata_keuangan'],1,',','.') }}%</p><p class="text-xs text-white/60 mt-1">Rata-rata keuangan</p></div>
                </div>
            </div>
            <div class="lg:col-span-2 public-card p-5 sm:p-7">
                <div class="flex items-start justify-between gap-4"><div><h3 class="font-bold text-slate-900">Tren realisasi anggaran</h3><p class="text-xs text-slate-500 mt-1">Dalam juta rupiah</p></div><x-ui.updated-badge :at="$terakhirDiperbarui" label="Diperbarui" /></div>
                <div class="h-64 mt-5"><canvas id="chartTrenLanding"></canvas></div>
            </div>
        </div>
    </section>

    <section id="profil" class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 pt-20 scroll-mt-20">
        <div class="rounded-3xl bg-slate-900 text-white p-7 sm:p-10 lg:p-12 grid lg:grid-cols-[1fr_.8fr] gap-10">
            <div><p class="text-xs font-bold uppercase tracking-[.18em] text-[#e2b442]">Bappelitbangda Nias Barat</p><h2 class="mt-3 text-3xl sm:text-4xl font-extrabold">Perencanaan berbasis data menuju Nias Barat CERAH.</h2><p class="mt-4 text-sm leading-7 text-white/65">SICERAH mendukung transparansi capaian pembangunan, evaluasi program, penyajian dokumen perencanaan, dan analisis spasial bagi pemerintah serta masyarakat.</p></div>
            <div class="grid sm:grid-cols-2 gap-3 self-start"><a href="{{ route('webgis.index') }}" class="rounded-2xl bg-white/10 p-5 hover:bg-white/15"><p class="font-bold">Peta Tematik</p><p class="text-xs text-white/55 mt-1">Lihat data per desa</p></a><a href="{{ route('jdih.index') }}" class="rounded-2xl bg-white/10 p-5 hover:bg-white/15"><p class="font-bold">Dokumen JDIH</p><p class="text-xs text-white/55 mt-1">Akses regulasi dan dokumen</p></a></div>
        </div>
    </section>

    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[70]" role="dialog" aria-modal="true" @keydown.escape.window="closeModal()">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-sm" @click="closeModal()"></div>
        <div class="absolute inset-x-0 bottom-0 sm:inset-0 sm:m-auto sm:max-w-4xl sm:h-fit max-h-[92vh] overflow-y-auto bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl p-5 sm:p-7">
            <div class="flex items-start justify-between gap-4">
                <div><div class="flex flex-wrap gap-2 mb-2"><span class="badge-default">IKD</span><span x-show="selected?.is_iku" class="badge-maroon">IKU</span><span x-show="selected?.is_makro" class="badge-success">Makro</span></div><h3 class="text-xl sm:text-2xl font-extrabold text-slate-900" x-text="selected?.nama"></h3><p class="text-sm text-slate-500 mt-1" x-text="selected ? shortAspect(selected.aspek)+' · '+(selected.satuan || 'Tanpa satuan') : ''"></p></div>
                <button type="button" @click="closeModal()" class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 shrink-0" aria-label="Tutup"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/></svg></button>
            </div>
            <div x-show="selected?.has_chart" class="h-72 sm:h-80 mt-6"><canvas id="indicatorTrendChart"></canvas></div>
            <div x-show="selected && !selected.has_chart" class="mt-6 rounded-2xl bg-amber-50 border border-amber-100 p-5 text-sm text-amber-900">Data indikator ini berupa teks, sehingga ditampilkan sebagai riwayat nilai tanpa grafik numerik.</div>
            <div class="mt-6 overflow-x-auto"><table class="w-full min-w-[620px] text-sm"><thead class="text-xs text-slate-500 border-b border-slate-200"><tr><th class="text-left py-2">Tahun</th><th class="text-left py-2">Jenis</th><th class="text-left py-2">Nilai asli</th><th class="text-left py-2">Nilai grafik</th></tr></thead><tbody><template x-for="point in (selected?.points || [])" :key="point.year"><tr class="border-b border-slate-100"><td class="py-2.5 font-semibold" x-text="point.year"></td><td class="py-2.5 capitalize" x-text="point.source || '—'"></td><td class="py-2.5" x-text="point.raw || '—'"></td><td class="py-2.5" x-text="point.graph === null ? '—' : formatNumber(point.graph)"></td></tr></template></tbody></table></div>
            <p x-show="selected?.sumber_data" class="mt-5 text-xs leading-5 text-slate-500"><span class="font-semibold text-slate-700">Sumber:</span> <span x-text="selected?.sumber_data"></span></p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    if (typeof Chart !== 'undefined') Chart.defaults.font.family = "'Inter', sans-serif";
    window.__SICERAH_INDICATORS__ = {{ Illuminate\Support\Js::from($indikatorDaerah) }};
    window.indicatorExplorer = function () {
        return {
            items: window.__SICERAH_INDICATORS__ || [], search: '', group: 'all', aspect: 'all', selected: null, modalOpen: false, chart: null,
            get filtered() {
                const query = this.search.trim().toLocaleLowerCase('id-ID');
                return this.items.filter((item) => {
                    const groupMatch = this.group === 'all' || (this.group === 'iku' && item.is_iku) || (this.group === 'makro' && item.is_makro);
                    const aspectMatch = this.aspect === 'all' || item.aspek === this.aspect;
                    const text = [item.nama, item.aspek, item.sumber_data, item.kode].filter(Boolean).join(' ').toLocaleLowerCase('id-ID');
                    return groupMatch && aspectMatch && (!query || text.includes(query));
                });
            },
            shortAspect(value) { return (value || 'Belum ada aspek').replace(/^ASPEK\s+/i, '').toLocaleLowerCase('id-ID').replace(/(^|\s)\S/g, (letter) => letter.toUpperCase()); },
            formatNumber(value) { return Number(value).toLocaleString('id-ID', { maximumFractionDigits: 4 }); },
            openIndicator(item) { this.selected = item; this.modalOpen = true; document.body.style.overflow = 'hidden'; this.$nextTick(() => this.renderChart()); },
            closeModal() { this.modalOpen = false; document.body.style.overflow = ''; if (this.chart) { this.chart.destroy(); this.chart = null; } },
            renderChart() {
                if (!this.selected?.has_chart || typeof Chart === 'undefined') return;
                const canvas = document.getElementById('indicatorTrendChart');
                if (!canvas) return;
                if (this.chart) this.chart.destroy();
                const points = this.selected.points;
                this.chart = new Chart(canvas, {type:'line',data:{labels:points.map((p)=>String(p.year)),datasets:[{label:'Nilai grafik',data:points.map((p)=>p.graph),borderColor:'#7f1d2d',backgroundColor:'rgba(127,29,45,.12)',fill:true,tension:.3,spanGaps:false,pointRadius:4,pointHoverRadius:6}]},options:{responsive:true,maintainAspectRatio:false,interaction:{mode:'index',intersect:false},plugins:{legend:{display:false},tooltip:{callbacks:{label:(context)=>{const point=points[context.dataIndex];return point.raw ? `${point.source === 'capaian' ? 'Capaian' : 'Target'}: ${point.raw} ${this.selected.satuan || ''}` : 'Belum tersedia';},afterLabel:(context)=>points[context.dataIndex].type === 'range' ? 'Grafik menggunakan titik tengah rentang' : ''}}},scales:{y:{beginAtZero:false,ticks:{callback:(value)=>Number(value).toLocaleString('id-ID')}},x:{grid:{display:false}}}}});
            }
        };
    };

    document.addEventListener('DOMContentLoaded', function () {
        const element = document.getElementById('chartTrenLanding');
        if (!element || typeof Chart === 'undefined') return;
        new Chart(element, {type:'bar',data:{labels:@json($tren['labels']),datasets:[{label:'Realisasi (Rp juta)',data:@json($tren['realisasi']),backgroundColor:['#cbd5e1','#a72033','#8b1e30','#4a0e18'],borderRadius:8}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,ticks:{callback:(value)=>Number(value).toLocaleString('id-ID')}}}}});
    });
</script>
@endpush
