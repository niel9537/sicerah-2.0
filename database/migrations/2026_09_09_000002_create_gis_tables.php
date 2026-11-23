<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gis_areas', function (Blueprint $table) {
            $table->string('code', 30)->primary();
            $table->string('name', 150);
            $table->string('district_code', 30)->nullable()->index();
            $table->string('district_name', 150)->nullable()->index();
            $table->timestamps();
        });

        Schema::create('gis_layers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 160)->unique();
            $table->text('description')->nullable();
            $table->string('unit', 50)->nullable();
            $table->string('geojson_path')->default('data/gis/nias-barat-desa.geojson');
            $table->string('color_from', 7)->default('#fee2e2');
            $table->string('color_to', 7)->default('#7f1d1d');
            $table->json('breakpoints')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_public')->default(true)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('gis_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gis_layer_id')->constrained('gis_layers')->cascadeOnDelete();
            $table->string('area_code', 30);
            $table->year('year');
            $table->string('raw_value', 100)->nullable();
            $table->enum('value_type', ['empty', 'number', 'range', 'text'])->default('empty');
            $table->decimal('numeric_value', 24, 6)->nullable();
            $table->decimal('value_min', 24, 6)->nullable();
            $table->decimal('value_max', 24, 6)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['gis_layer_id', 'area_code', 'year']);
            $table->foreign('area_code')->references('code')->on('gis_areas')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gis_values');
        Schema::dropIfExists('gis_layers');
        Schema::dropIfExists('gis_areas');
    }
};
