<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('webgis_education', function (Blueprint $t) {
            $t->id(); $t->string('kode_kecamatan',30)->unique();
            foreach (['schools','students','teachers','synced','unsynced'] as $field) $t->unsignedInteger($field);
            $t->string('source',255); $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('webgis_education'); }
};
