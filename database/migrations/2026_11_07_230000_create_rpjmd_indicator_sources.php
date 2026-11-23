<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rpjmd_indicator_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('indikator');
            $table->string('file');
            $table->unsignedInteger('baris');
            $table->string('jenis', 3);
            $table->text('nama');
            $table->string('satuan')->nullable();
            $table->text('perangkat_daerah')->nullable();
            $table->text('kelompok')->nullable();
            $table->string('arah')->nullable();
            $table->string('baseline_2024')->nullable();
            $table->json('targets');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->unique(['file', 'baris']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpjmd_indicator_sources');
    }
};
