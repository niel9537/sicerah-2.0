<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emonev_prosn', function (Blueprint $t) {
            $t->id();
            $t->unsignedSmallInteger('tahun')->default(2026);
            $t->unsignedSmallInteger('baris');
            $t->string('jenis', 20);
            $t->string('tema', 10);
            $t->string('program', 20)->default('');
            $t->string('nomor', 20)->default('');
            $t->string('kode', 30)->default('');
            $t->text('uraian');
            $t->text('indikator')->nullable();
            $t->string('status', 50)->nullable();
            $t->string('ketersediaan', 50)->nullable();
            foreach ([1, 2, 3] as $i) {
                $t->text('bukti_'.$i)->nullable();
            }
            $t->json('tautan')->nullable();
            $t->text('catatan')->nullable();
            $t->timestamps();
            $t->unique(['tahun', 'baris']);
            $t->index(['tahun', 'tema', 'jenis']);
        });
        $rows = json_decode(file_get_contents(database_path('data/prosn_2026.json')), true, 512, JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $row['tautan'] = json_encode($row['tautan']);
                DB::table('emonev_prosn')->insert($row + ['tahun' => 2026, 'created_at' => now(), 'updated_at' => now()]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emonev_prosn');
    }
};
