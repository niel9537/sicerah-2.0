<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluasi_renja', function (Blueprint $table) {
            if (! Schema::hasColumn('evaluasi_renja', 'catatan_verifikasi')) {
                $table->text('catatan_verifikasi')->nullable()->after('tindak_lanjut');
            }
        });

        // Cegah duplikasi: satu sub kegiatan hanya boleh punya 1 evaluasi per periode
        Schema::table('evaluasi_renja', function (Blueprint $table) {
            $table->unique(['subkegiatan_id', 'periode_id'], 'evaluasi_renja_subkegiatan_periode_unique');
        });
    }

    public function down(): void
    {
        Schema::table('evaluasi_renja', function (Blueprint $table) {
            $table->dropUnique('evaluasi_renja_subkegiatan_periode_unique');
            $table->dropColumn('catatan_verifikasi');
        });
    }
};