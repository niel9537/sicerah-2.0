<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kenapa migration ini ada:
 * Tabel `indikator_target` dan `indikator_realisasi` selama ini TIDAK punya
 * kolom created_at/updated_at sama sekali, jadi sistem tidak mungkin tahu
 * kapan sebuah target atau realisasi capaian IKU/IKK/IKD terakhir diubah.
 * Ini yang bikin halaman "Kelola Capaian" & landing page tidak bisa menampilkan
 * info "diperbarui X yang lalu".
 *
 * Migration ini hanya menambah dua kolom nullable timestamp ke masing-masing
 * tabel — aman dijalankan kapan saja, tidak mengubah/menghapus data yang ada.
 * Setelah ini jalan, IndikatorController::simpanTarget() & simpanRealisasi()
 * otomatis mengisi kolom ini lewat updateOrCreate() bawaan Eloquent — TIDAK
 * perlu ubah kode di controller, SELAMA model IndikatorTarget & IndikatorRealisasi
 * tidak men-set `public $timestamps = false`. Kalau salah satu model punya baris
 * itu, hapus/ubah jadi true supaya timestamp-nya kepakai.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('indikator_target', function (Blueprint $table) {
            if (! Schema::hasColumn('indikator_target', 'created_at')) {
                $table->timestamps();
            }
        });

        Schema::table('indikator_realisasi', function (Blueprint $table) {
            if (! Schema::hasColumn('indikator_realisasi', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('indikator_target', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('indikator_realisasi', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
