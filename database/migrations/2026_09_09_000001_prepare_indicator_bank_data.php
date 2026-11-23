<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('indikator', function (Blueprint $table) {
            $table->string('kode', 40)->nullable()->unique()->after('id');
            $table->string('aspek', 120)->nullable()->after('jenis')->index();
            $table->boolean('is_iku')->default(false)->after('aspek')->index();
            $table->boolean('is_makro')->default(false)->after('is_iku')->index();
            $table->unsignedSmallInteger('bank_data_row')->nullable()->after('is_makro')->unique();
            $table->unsignedSmallInteger('urutan')->nullable()->after('bank_data_row');
            $table->text('keterangan')->nullable()->after('sumber_data');
        });

        // Dump produksi lama sudah memiliki kedua kolom ini, sedangkan rangkaian
        // migration awal belum. Kondisional menjaga upgrade dan fresh install sama-sama aman.
        if (! Schema::hasColumn('indikator', 'updated_by')) {
            Schema::table('indikator', function (Blueprint $table) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('keterangan');
            });
        }
        if (! Schema::hasColumn('indikator', 'updated_at')) {
            Schema::table('indikator', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->after('updated_by');
            });
        }

        // Bank Data memuat angka desimal, rentang, catatan simbol, dan teks.
        // Kolom string mempertahankan nilai sumber; angka grafik dihitung oleh parser.
        Schema::table('indikator_target', function (Blueprint $table) {
            $table->string('target', 100)->nullable()->change();
        });

        Schema::table('indikator_realisasi', function (Blueprint $table) {
            $table->string('realisasi', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('indikator', function (Blueprint $table) {
            $table->dropUnique(['kode']);
            $table->dropUnique(['bank_data_row']);
            $table->dropIndex(['aspek']);
            $table->dropIndex(['is_iku']);
            $table->dropIndex(['is_makro']);
            $table->dropColumn([
                'kode', 'aspek', 'is_iku', 'is_makro',
                'bank_data_row', 'urutan', 'keterangan',
            ]);
        });

        Schema::table('indikator_target', function (Blueprint $table) {
            $table->decimal('target', 18, 4)->nullable()->change();
        });

        Schema::table('indikator_realisasi', function (Blueprint $table) {
            $table->decimal('realisasi', 18, 4)->nullable()->change();
        });
    }
};
