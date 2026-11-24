@php
    $themes = ['Tata kelola', 'Sumber daya manusia', 'Ekonomi kerakyatan', 'Infrastruktur', 'Kesehatan'];
    $art = [
        '<path d="M20 74h100M30 70V40h80v30M22 40l48-24 48 24M44 48v22m26-22v22m26-22v22"/><circle cx="70" cy="29" r="3"/>',
        '<path d="m12 39 58-23 58 23-58 23zM35 49v27q35 23 70 0V49M125 41v39"/><path d="M21 101q49-25 98 0M42 91v-8m56 8v-8"/>',
        '<path d="M19 97h103M28 92V60h21v32m13 0V45h21v47m13 0V27h21v65M20 42l31-17 25 7 36-20"/><path d="m99 11 14 1-2 14"/>',
        '<path d="M10 94h120M19 87V45h24v42m51 0V45h24v42M43 61h51M43 76h51M22 45l9-20 9 20m57 0 9-20 9 20M60 108l5-26m15 26-5-26"/>',
        '<path d="M70 104S15 74 15 43c0-27 37-33 55-9 18-24 55-18 55 9 0 31-55 61-55 61Z"/><path d="M43 60h17V43h20v17h17v20H80v17H60V80H43z"/>'
    ];
@endphp
<section class="mission-landscape" aria-label="Visi dan lima misi pembangunan">
    <div class="mission-inner"><div class="mission-intro"><p>ARAH PEMBANGUNAN NIAS BARAT</p><h2>{{ $rpjmd['visi'] }}</h2><span>Cerdas, Sejahtera, dan Sehat</span><p class="mission-instruction">Pilih misi untuk melihat tujuan, sasaran, indikator, dan target pembangunan.</p></div>
    <div class="mission-grid">
    @foreach($rpjmd['pairs'] as $pair)
        <button type="button" class="mission-card mission-card-{{ $loop->index }}" data-mission="{{ $loop->index }}" style="--card-order:{{ $loop->index }}" aria-haspopup="dialog" aria-controls="mission-dialog-{{ $loop->index }}">
            <span class="mission-number">MISI 0{{ $loop->iteration }} <span aria-hidden="true">&#8599;</span></span>
            <h3>{{ $themes[$loop->index] }}</h3><p>{{ $pair['tujuan'] }}</p>
            <svg class="mission-art" viewBox="0 0 140 120" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $art[$loop->index] !!}</svg>
            <span class="mission-card-link">Jelajahi misi <span aria-hidden="true">+</span></span>
        </button>
        <dialog id="mission-dialog-{{ $loop->index }}" class="mission-dialog" aria-labelledby="mission-title-{{ $loop->index }}">
            <div class="mission-dialog-heading"><span>MISI 0{{ $loop->iteration }} &middot; NIAS BARAT CERAH</span><button type="button" data-close-mission aria-label="Tutup detail misi">&times;</button></div>
            <div class="mission-dialog-content"><h2 id="mission-title-{{ $loop->index }}">{{ $themes[$loop->index] }}</h2><div class="mission-detail-grid"><section><h3>Misi</h3><p>{{ $pair['misi'] }}</p></section><section><h3>Tujuan</h3><p>{{ $pair['tujuan'] }}</p></section></div>
            <h3 class="mission-target-title">Sasaran, indikator & target tahunan</h3>
            @include('rpjmd._indicators', ['rows' => collect($hierarchyRows ?? [])->where('pair', $loop->index)])
            <p class="mission-source">Sumber: Tabel 3.5 RPJMD Kabupaten Nias Barat. Target mengikuti data indikator yang terhubung.</p></div>
        </dialog>
    @endforeach
    </div></div>
    <div class="mission-sea-front" aria-hidden="true"></div>
</section>
@once
@push('scripts')
<script>
(() => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if ('IntersectionObserver' in window && !reducedMotion) {
        const observer = new IntersectionObserver(entries => entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-emerging');
            observer.unobserve(entry.target);
        }), { threshold: 0.12 });
        document.querySelectorAll('.mission-landscape').forEach(section => {
            section.classList.add('mission-reveal-ready');
            observer.observe(section);
            section.addEventListener('focusin', () => section.classList.add('is-emerging'), { once: true });
        });
    }
    let trigger, previousOverflow;
    const close = dialog => { dialog.close(); };
    document.querySelectorAll('[data-mission]').forEach(button => button.addEventListener('click', () => {
        const dialog = document.getElementById(button.getAttribute('aria-controls'));
        trigger = button; previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden'; dialog.showModal();
    }));
    document.querySelectorAll('.mission-dialog').forEach(dialog => {
        dialog.querySelector('[data-close-mission]').addEventListener('click', () => close(dialog));
        dialog.addEventListener('click', event => { if(event.target === dialog) { const r = dialog.getBoundingClientRect(); if(event.clientX < r.left || event.clientX > r.right || event.clientY < r.top || event.clientY > r.bottom) close(dialog); } });
        dialog.addEventListener('close', () => { document.body.style.overflow = previousOverflow || ''; trigger?.focus(); });
    });
})();
</script>
@endpush
@endonce
