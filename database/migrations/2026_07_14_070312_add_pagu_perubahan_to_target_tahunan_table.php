<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('target_tahunan', function (Blueprint $table) {
            if (! Schema::hasColumn('target_tahunan', 'pagu_perubahan')) {
                $table->decimal('pagu_perubahan', 20, 2)->nullable()->after('pagu_renja');
            }
        });

        // Perbesar presisi pagu_renstra & pagu_renja dari (18,2) -> (20,2)
        // Pakai raw statement supaya tidak bergantung pada doctrine/dbal
        // (Laravel 11+ sebenarnya sudah mendukung ->change() secara native,
        // tapi raw SQL ini lebih portabel & pasti berhasil di MySQL/MariaDB).
        DB::statement('ALTER TABLE `target_tahunan` MODIFY `pagu_renstra` DECIMAL(20,2) NULL');
        DB::statement('ALTER TABLE `target_tahunan` MODIFY `pagu_renja` DECIMAL(20,2) NULL');
    }

    public function down(): void
    {
        Schema::table('target_tahunan', function (Blueprint $table) {
            if (Schema::hasColumn('target_tahunan', 'pagu_perubahan')) {
                $table->dropColumn('pagu_perubahan');
            }
        });

        DB::statement('ALTER TABLE `target_tahunan` MODIFY `pagu_renstra` DECIMAL(18,2) NULL');
        DB::statement('ALTER TABLE `target_tahunan` MODIFY `pagu_renja` DECIMAL(18,2) NULL');
    }
};