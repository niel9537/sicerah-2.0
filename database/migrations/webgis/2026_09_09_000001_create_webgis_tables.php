<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('webgis_desa', function (Blueprint $t) {
            $t->string('kode_desa',30)->primary(); $t->string('nama_desa',150);
            $t->string('kode_kecamatan',30)->nullable(); $t->string('nama_kecamatan',150)->nullable();
        });
        Schema::create('webgis_themes', function (Blueprint $t) {
            $t->id(); $t->string('slug',100)->unique(); $t->string('name',150); $t->string('unit',40);
            $t->text('description')->nullable(); $t->string('source',255)->nullable();
            $t->boolean('is_public')->default(false); $t->json('breaks'); $t->json('colors');
            $t->longText('geojson'); $t->timestamps();
        });
        Schema::create('webgis_values', function (Blueprint $t) {
            $t->id(); $t->foreignId('theme_id')->constrained('webgis_themes')->cascadeOnDelete();
            $t->string('kode_desa',30); $t->foreign('kode_desa')->references('kode_desa')->on('webgis_desa');
            $t->unsignedSmallInteger('year'); $t->decimal('value',18,4); $t->string('note',500)->nullable(); $t->timestamps();
            $t->unique(['theme_id','kode_desa','year'],'webgis_unique_value');
        });
    }
    public function down(): void {
        Schema::dropIfExists('webgis_values'); Schema::dropIfExists('webgis_themes'); Schema::dropIfExists('webgis_desa');
    }
};
