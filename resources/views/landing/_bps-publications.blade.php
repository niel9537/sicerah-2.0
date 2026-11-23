<section class="bps-latest max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-labelledby="bps-latest-title"
    x-data="{ previous: false, next: false, update() { const track = this.$refs.track; this.previous = track.scrollLeft > 2; this.next = track.scrollLeft + track.clientWidth < track.scrollWidth - 2; }, move(direction) { this.$refs.track.scrollBy({ left: direction * this.$refs.track.clientWidth * .85, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth' }); } }"
    x-init="$nextTick(() => update())" @resize.window.debounce="update()">
    <div class="bps-latest-heading">
        <div><p class="bps-latest-eyebrow">STATISTIK RESMI DAERAH</p><h2 id="bps-latest-title">Publikasi BPS Terbaru</h2><p>Terbitan terbaru Badan Pusat Statistik Kabupaten Nias Barat.</p></div>
        <a class="bps-latest-all" href="{{ route('bps.index', ['kategori' => 'publication']) }}">Lihat Semua <span aria-hidden="true">&#8594;</span></a>
    </div>
    @if(count($bpsPublications))
        <div class="bps-latest-track" id="bps-latest-track" x-ref="track" @scroll.passive="update()" tabindex="0" aria-label="Daftar publikasi BPS terbaru, geser untuk melihat lainnya">
            @foreach($bpsPublications as $item)
                <article class="bps-latest-card">
                    <div class="bps-latest-cover">
                        <span class="bps-latest-placeholder" aria-hidden="true">BPS<br><small>Publikasi Statistik</small></span>
                        @if($item['cover'])<img src="{{ $item['cover'] }}" alt="Sampul {{ $item['title'] }}" loading="lazy" referrerpolicy="no-referrer" onerror="this.hidden = true">@endif
                    </div>
                    <div class="bps-latest-copy"><span class="bps-latest-label">{{ $item['subject'] ?: 'Publikasi BPS' }}</span><h3>{{ $item['title'] }}</h3>
                        @if($item['date'])<p class="bps-latest-date">Terbit: {{ $item['date'] }}</p>@endif
                        @if($item['download'])<a href="{{ $item['download'] }}" target="_blank" rel="noopener noreferrer" aria-label="Unduh PDF {{ $item['title'] }} (tab baru)">Unduh PDF <span aria-hidden="true">&#8599;</span></a>@else<a href="{{ route('bps.index', ['kategori' => 'publication', 'q' => $item['title']]) }}">Lihat Publikasi <span aria-hidden="true">&#8594;</span></a>@endif
                    </div>
                </article>
            @endforeach
        </div>
        <div class="bps-latest-controls"><span>Geser untuk melihat publikasi lainnya</span><div><button type="button" @click="move(-1)" :disabled="!previous" aria-label="Publikasi sebelumnya" aria-controls="bps-latest-track">&#8592;</button><button type="button" @click="move(1)" :disabled="!next" aria-label="Publikasi berikutnya" aria-controls="bps-latest-track">&#8594;</button></div></div>
    @else
        <div class="bps-latest-empty" role="status">{{ $bpsPublicationsUnavailable ? 'Publikasi BPS belum dapat dimuat. Silakan coba kembali beberapa saat lagi.' : 'Belum ada publikasi BPS yang tersedia.' }}</div>
    @endif
</section>
<style>
.bps-latest-heading{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:22px}.bps-latest-eyebrow{color:#967018;font-size:11px;font-weight:800;letter-spacing:.14em;margin-bottom:6px}.bps-latest-heading h2{font-size:24px;font-weight:900;color:#191716;line-height:1.3}.bps-latest-heading p:last-child{font-size:14px;color:#64748b;margin-top:7px}.bps-latest-all{font-size:13px;font-weight:800;color:#967018;white-space:nowrap}.bps-latest-track{display:flex;gap:20px;overflow-x:auto;scroll-snap-type:x mandatory;padding:3px 2px 14px;scrollbar-width:thin;scrollbar-color:#d4a017 #f1f5f9}.bps-latest-card{flex:0 0 calc((100% - 60px)/4);min-width:220px;scroll-snap-align:start;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;background:#fff;display:flex;flex-direction:column}.bps-latest-cover{height:235px;background:#f2f0e9;position:relative;display:grid;place-items:center}.bps-latest-placeholder{text-align:center;color:#978861;font-size:30px;font-weight:900}.bps-latest-placeholder small{font-size:12px;font-weight:600}.bps-latest-cover img{position:absolute;inset:0;width:100%;height:100%;object-fit:contain;padding:16px;background:#f2f0e9}.bps-latest-copy{padding:18px;display:flex;flex-direction:column;flex:1}.bps-latest-label{font-size:10px;letter-spacing:.06em;text-transform:uppercase;font-weight:800;color:#967018}.bps-latest-copy h3{font-size:15px;font-weight:800;line-height:1.5;color:#191716;margin:8px 0;overflow-wrap:anywhere}.bps-latest-date{font-size:12px;color:#64748b;margin-bottom:18px}.bps-latest-copy a{font-size:12px;font-weight:800;color:#967018;margin-top:auto;padding-top:12px}.bps-latest-controls{display:flex;justify-content:space-between;align-items:center;margin-top:10px;gap:12px}.bps-latest-controls>span{font-size:12px;color:#64748b}.bps-latest-controls>div{display:flex;gap:8px}.bps-latest-controls button{width:38px;height:38px;border:1px solid #d4a017;border-radius:50%;color:#967018;background:#fff}.bps-latest-controls button:disabled{opacity:.3;cursor:default}.bps-latest a:focus-visible,.bps-latest button:focus-visible,.bps-latest-track:focus-visible{outline:2px solid #967018;outline-offset:4px}.bps-latest-empty{padding:28px;border:1px dashed #d9d2bf;border-radius:16px;color:#64748b;background:#faf9f6;font-size:14px}@media(max-width:1023px){.bps-latest-card{flex-basis:calc((100% - 40px)/3)}}@media(max-width:767px){.bps-latest-card{flex-basis:calc((100% - 20px)/2)}.bps-latest-heading h2{font-size:20px}}@media(max-width:479px){.bps-latest-heading{align-items:flex-start;flex-direction:column;gap:12px}.bps-latest-card{flex-basis:78%;min-width:0}.bps-latest-cover{height:220px}}
</style>
