<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->enum('role', ['super_admin', 'admin_bappeda', 'operator_opd', 'verifikator', 'pimpinan', 'inspektorat'])->nullable()->change();
        });
        Schema::table('emonev_prosn', fn (Blueprint $t) => $t->unsignedInteger('versi')->default(1));
        Schema::create('emonev_prosn_validasi', function (Blueprint $t) {
            $t->id();
            $t->foreignId('prosn_id')->constrained('emonev_prosn')->cascadeOnDelete();
            $t->unsignedInteger('versi');
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('nama_validator');
            $t->string('keputusan', 20);
            $t->text('catatan')->nullable();
            $t->timestamp('validated_at');
            $t->index(['prosn_id', 'versi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emonev_prosn_validasi');
        Schema::table('emonev_prosn', fn (Blueprint $t) => $t->dropColumn('versi'));
        // Keep the expanded role enum so existing Inspektorat accounts remain valid.
    }
};
