<section class="home-konvergensi" aria-labelledby="home-konvergensi-title"
    x-data="{ summary: null, failed: false, async init() { try { const response = await fetch(@js(route('konvergensi.summary')), { headers: { Accept: 'application/json' } }); if (!response.ok) throw new Error(); this.summary = await response.json(); } catch (error) { this.failed = true; } } }">
    <div class="home-konvergensi-intro">
        <span class="home-konvergensi-label">TERINTEGRASI DENGAN DITJEN BINA BANGDA</span>
        <h2 id="home-konvergensi-title">Aksi Konvergensi Penurunan Stunting</h2>
        <p>Pantau progres aksi konvergensi Kabupaten Nias Barat melalui data e-Monev Bina Bangda, Kementerian Dalam Negeri.</p>
        <a class="portal-gold-button" href="{{ route('konvergensi.index') }}">Lihat Data &amp; Tabel <span aria-hidden="true">&#8594;</span></a>
    </div>
    <div class="home-konvergensi-data" aria-live="polite" :aria-busy="!summary && !failed">
        <p x-show="!summary && !failed">Memuat ringkasan data Nias Barat…</p>
        <p x-show="failed" x-cloak>Ringkasan belum dapat dimuat. Buka halaman data untuk melihat status dan tabel rincian.</p>
        <div x-show="summary" x-cloak>
            <p class="home-konvergensi-year">KABUPATEN NIAS BARAT · <span x-text="summary?.year"></span></p>
            <dl class="home-konvergensi-stats">
                <div><dt>Progres konvergensi</dt><dd x-text="summary?.progress !== '' && summary?.progress != null ? summary.progress + '%' : '—'"></dd></div>
                <div><dt>Desa tercatat</dt><dd x-text="summary?.villages !== '' && summary?.villages != null ? summary.villages : '—'"></dd></div>
                <div><dt>Kecamatan terdata</dt><dd x-text="summary?.subdistricts"></dd></div>
            </dl>
            <p class="home-konvergensi-status" x-show="summary?.is_fallback">Data cadangan terakhir; koneksi langsung ke sumber belum berhasil.</p>
            <p class="home-konvergensi-time">Diambil dari sumber: <span x-text="summary?.retrieved_at"></span></p>
            <p class="home-konvergensi-note">Progres pelaporan aksi konvergensi, bukan angka prevalensi stunting.</p>
        </div>
    </div>
</section>
