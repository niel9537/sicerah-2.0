<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel baru untuk menu publik JDIH (Jaringan Dokumentasi dan Informasi Hukum)
 * di landing page SICERAH — daftar dokumen perencanaan & regulasi milik
 * Bappelitbangda (RPJMD, RKPD, Renstra, Renja, Perda, Perbub, dst).
 *
 * `nomor` dan `file_url` sengaja nullable: baris contoh di bawah HANYA diisi
 * judul/kategori/tahun yang sudah pasti (dokumen jenis ini memang wajib ada
 * di setiap Bappelitbangda), tanpa mengarang nomor Perda/Perbup atau tautan
 * file yang tidak bisa dipastikan kebenarannya. Silakan lengkapi nomor resmi
 * & unggah dokumennya lewat admin sebelum tayang final ke publik.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jdih_dokumen', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori', ['RPJPD', 'RPJMD', 'RENSTRA', 'RKPD', 'RENJA', 'PERDA', 'PERBUP', 'LAINNYA'])
                ->default('LAINNYA');
            $table->string('judul');
            $table->string('nomor')->nullable();
            $table->year('tahun')->nullable();
            $table->text('ringkasan')->nullable();
            $table->string('file_url')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['kategori', 'tahun']);
        });

        DB::table('jdih_dokumen')->insert([
            [
                'kategori' => 'RPJMD',
                'judul' => 'RPJMD Kabupaten Nias Barat Tahun 2025–2029',
                'nomor' => null,
                'tahun' => 2025,
                'ringkasan' => 'Rencana Pembangunan Jangka Menengah Daerah yang memuat visi "Mewujudkan Nias Barat CERAH" (Cerdas, Sejahtera, Sehat). Data awal — lengkapi nomor Perda & unggah dokumen resmi melalui admin.',
                'file_url' => null,
                'tanggal_terbit' => null,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'RKPD',
                'judul' => 'RKPD Kabupaten Nias Barat Tahun 2026',
                'nomor' => null,
                'tahun' => 2026,
                'ringkasan' => 'Rencana Kerja Pemerintah Daerah sebagai penjabaran tahunan RPJMD 2025–2029. Data awal — lengkapi nomor Perbup & unggah dokumen resmi melalui admin.',
                'file_url' => null,
                'tanggal_terbit' => null,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'RENSTRA',
                'judul' => 'Renstra Bappelitbangda Kabupaten Nias Barat 2025–2029',
                'nomor' => null,
                'tahun' => 2025,
                'ringkasan' => 'Rencana Strategis Perangkat Daerah Bappelitbangda, selaras dengan RPJMD 2025–2029. Data awal — lengkapi nomor & unggah dokumen resmi melalui admin.',
                'file_url' => null,
                'tanggal_terbit' => null,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'RENJA',
                'judul' => 'Renja Bappelitbangda Kabupaten Nias Barat Tahun 2026',
                'nomor' => null,
                'tahun' => 2026,
                'ringkasan' => 'Rencana Kerja tahunan Bappelitbangda sebagai dasar evaluasi triwulanan di SICERAH. Data awal — lengkapi nomor & unggah dokumen resmi melalui admin.',
                'file_url' => null,
                'tanggal_terbit' => null,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'PERDA',
                'judul' => 'Peraturan Daerah tentang RPJMD Kabupaten Nias Barat 2025–2029',
                'nomor' => null,
                'tahun' => 2025,
                'ringkasan' => 'Payung hukum penetapan RPJMD. Data awal — lengkapi nomor Perda & unggah dokumen resmi melalui admin.',
                'file_url' => null,
                'tanggal_terbit' => null,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'PERBUP',
                'judul' => 'Peraturan Bupati tentang RKPD Kabupaten Nias Barat Tahun 2026',
                'nomor' => null,
                'tahun' => 2026,
                'ringkasan' => 'Payung hukum penetapan RKPD tahun berjalan. Data awal — lengkapi nomor Perbup & unggah dokumen resmi melalui admin.',
                'file_url' => null,
                'tanggal_terbit' => null,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('jdih_dokumen');
    }
};
