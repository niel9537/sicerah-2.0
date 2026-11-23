<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ============ Tabel referensi (level atas) ============

        Schema::create('opd', function (Blueprint $t) {
            $t->increments('id');
            $t->string('kode', 20)->nullable()->unique();
            $t->string('nama', 255)->nullable();
            $t->text('alamat')->nullable();
            $t->string('telepon', 30)->nullable();
            $t->string('email', 100)->nullable();
            $t->timestamps();
        });

        Schema::create('urusan', function (Blueprint $t) {
            $t->increments('id');
            $t->string('kode', 20)->unique();
            $t->string('nama', 255);
            $t->timestamps();
        });

        Schema::create('sasaran_rpjmd', function (Blueprint $t) {
            $t->increments('id');
            $t->string('kode', 20)->nullable();
            $t->text('nama')->nullable();
            $t->year('periode_awal')->nullable();
            $t->year('periode_akhir')->nullable();
            $t->timestamps();
        });

        Schema::create('tujuan_pd', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('opd_id')->nullable();
            $t->unsignedInteger('sasaran_rpjmd_id')->nullable();
            $t->string('kode', 20)->nullable();
            $t->text('nama')->nullable();
            $t->year('tahun_awal')->nullable();
            $t->year('tahun_akhir')->nullable();
            $t->foreign('opd_id')->references('id')->on('opd');
            $t->foreign('sasaran_rpjmd_id')->references('id')->on('sasaran_rpjmd');
        });

        Schema::create('sasaran_pd', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('tujuan_pd_id')->nullable();
            $t->string('kode', 20)->nullable();
            $t->text('nama')->nullable();
            $t->year('tahun_awal')->nullable();
            $t->year('tahun_akhir')->nullable();
            $t->foreign('tujuan_pd_id')->references('id')->on('tujuan_pd');
        });

        // ============ Program → Kegiatan → Sub Kegiatan ============

        Schema::create('program', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('urusan_id')->nullable();
            $t->unsignedInteger('opd_id')->nullable();
            $t->unsignedInteger('sasaran_pd_id')->nullable();
            $t->string('kode', 30)->nullable();
            $t->string('nama', 255)->nullable();
            $t->foreign('opd_id')->references('id')->on('opd');
            $t->foreign('urusan_id')->references('id')->on('urusan');
            $t->foreign('sasaran_pd_id')->references('id')->on('sasaran_pd');
        });

        Schema::create('kegiatan', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('program_id')->nullable();
            $t->string('kode', 30)->nullable();
            $t->text('nama')->nullable();
            $t->foreign('program_id')->references('id')->on('program');
        });

        Schema::create('subkegiatan', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('kegiatan_id')->nullable();
            $t->string('kode', 30)->nullable();
            $t->string('nama', 255)->nullable();
            $t->string('indikator', 255)->nullable();
            $t->string('satuan', 100)->nullable();
            $t->foreign('kegiatan_id')->references('id')->on('kegiatan');
        });

        // ============ Periode & Target/Realisasi ============

        Schema::create('periode', function (Blueprint $t) {
            $t->increments('id');
            $t->year('tahun');
            $t->tinyInteger('triwulan');
            $t->string('nama', 30)->nullable();
            $t->enum('status', ['aktif', 'tutup'])->default('aktif');
            $t->unique(['tahun', 'triwulan']);
        });

        Schema::create('target_tahunan', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedInteger('subkegiatan_id')->nullable();
            $t->year('tahun')->nullable();
            $t->decimal('target_kinerja', 18, 2)->nullable();
            $t->decimal('pagu_renstra', 18, 2)->nullable();
            $t->decimal('pagu_renja', 18, 2)->nullable();
            $t->unique(['subkegiatan_id', 'tahun']);
            $t->foreign('subkegiatan_id')->references('id')->on('subkegiatan');
        });

        Schema::create('realisasi_triwulanan', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedInteger('subkegiatan_id')->nullable();
            $t->year('tahun')->nullable();
            $t->tinyInteger('triwulan')->nullable();
            $t->decimal('realisasi_kinerja', 18, 2)->nullable();
            $t->decimal('realisasi_anggaran', 18, 2)->nullable();
            $t->enum('status', ['draft', 'diajukan', 'diverifikasi', 'ditolak'])->nullable();
            $t->text('catatan_verifikasi')->nullable();
            $t->unsignedBigInteger('input_by')->nullable();
            $t->unsignedBigInteger('verified_by')->nullable();
            $t->dateTime('verified_at')->nullable();
            $t->timestamps();
            $t->unique(['subkegiatan_id', 'tahun', 'triwulan']);
            $t->foreign('subkegiatan_id')->references('id')->on('subkegiatan');
        });

        Schema::create('evaluasi_renja', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedInteger('subkegiatan_id');
            $t->unsignedInteger('periode_id');
            $t->decimal('target_fisik', 18, 2)->nullable();
            $t->decimal('target_keuangan', 20, 2)->nullable();
            $t->decimal('realisasi_fisik', 18, 2)->nullable();
            $t->decimal('realisasi_keuangan', 20, 2)->nullable();
            $t->enum('status', ['draft', 'diajukan', 'diverifikasi', 'ditolak'])->default('draft');
            $t->text('keterangan')->nullable();
            $t->text('analisis')->nullable();
            $t->text('kendala')->nullable();
            $t->text('solusi')->nullable();
            $t->text('tindak_lanjut')->nullable();
            $t->unsignedBigInteger('created_by')->nullable();
            $t->unsignedBigInteger('verified_by')->nullable();
            $t->dateTime('verified_at')->nullable();
            $t->timestamps();
            $t->foreign('subkegiatan_id')->references('id')->on('subkegiatan');
            $t->foreign('periode_id')->references('id')->on('periode');
        });

        // ============ Indikator IKU / IKK / IKD ============

        Schema::create('indikator', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->enum('jenis', ['IKU', 'IKK', 'IKD'])->nullable();
            $t->unsignedInteger('opd_id')->nullable();
            $t->unsignedInteger('urusan_id')->nullable();
            $t->unsignedInteger('sasaran_rpjmd_id')->nullable();
            $t->string('nama', 255)->nullable();
            $t->string('satuan', 100)->nullable();
            $t->string('sumber_data', 255)->nullable();
        });

        Schema::create('indikator_target', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedBigInteger('indikator_id')->nullable();
            $t->year('tahun')->nullable();
            $t->string('target', 100)->nullable();
            $t->unique(['indikator_id', 'tahun']);
            $t->foreign('indikator_id')->references('id')->on('indikator');
        });

        Schema::create('indikator_realisasi', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedBigInteger('indikator_id')->nullable();
            $t->year('tahun')->nullable();
            $t->tinyInteger('triwulan')->nullable();
            $t->string('realisasi', 100)->nullable();
            $t->enum('status', ['draft', 'diajukan', 'diverifikasi', 'ditolak'])->nullable();
            $t->unique(['indikator_id', 'tahun', 'triwulan']);
            $t->foreign('indikator_id')->references('id')->on('indikator');
        });

        // ============ LKPJ ============

        Schema::create('lkpj', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedInteger('urusan_id')->nullable();
            $t->unsignedInteger('opd_id')->nullable();
            $t->year('tahun')->nullable();
            $t->text('kebijakan')->nullable();
            $t->text('uraian_program')->nullable();
            $t->text('target')->nullable();
            $t->text('realisasi')->nullable();
            $t->text('permasalahan')->nullable();
            $t->text('upaya')->nullable();
            $t->text('rekomendasi')->nullable();
            $t->enum('status', ['draft', 'diajukan', 'diverifikasi'])->nullable();
        });

        // ============ Users (sesuai skema lampiran) ============
        // Breeze sudah membuat tabel users default; kita drop lalu buat ulang.

        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('nama', 255)->nullable();
            $t->string('email', 255)->nullable()->unique();
            $t->string('password', 255)->nullable();
            $t->enum('role', [
                'super_admin', 'admin_bappeda', 'operator_opd',
                'verifikator', 'pimpinan'
            ])->nullable();
            $t->unsignedInteger('opd_id')->nullable();
            $t->string('remember_token', 100)->nullable();
            $t->timestamps();
            $t->foreign('opd_id')->references('id')->on('opd');
        });

        // ============ Utilitas ============

        Schema::create('filter_laporan', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->year('tahun')->nullable();
            $t->tinyInteger('triwulan')->nullable();
            $t->unsignedInteger('opd_id')->nullable();
            $t->unsignedInteger('urusan_id')->nullable();
            $t->unsignedInteger('program_id')->nullable();
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('audit_log', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('tabel', 100)->nullable();
            $t->unsignedBigInteger('data_id')->nullable();
            $t->string('aksi', 30)->nullable();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->json('data_lama')->nullable();
            $t->json('data_baru')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
        Schema::dropIfExists('filter_laporan');
        Schema::dropIfExists('users');
        Schema::dropIfExists('lkpj');
        Schema::dropIfExists('indikator_realisasi');
        Schema::dropIfExists('indikator_target');
        Schema::dropIfExists('indikator');
        Schema::dropIfExists('evaluasi_renja');
        Schema::dropIfExists('realisasi_triwulanan');
        Schema::dropIfExists('target_tahunan');
        Schema::dropIfExists('periode');
        Schema::dropIfExists('subkegiatan');
        Schema::dropIfExists('kegiatan');
        Schema::dropIfExists('program');
        Schema::dropIfExists('sasaran_pd');
        Schema::dropIfExists('tujuan_pd');
        Schema::dropIfExists('sasaran_rpjmd');
        Schema::dropIfExists('urusan');
        Schema::dropIfExists('opd');
    }
};
