<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluasiRenjaController;
use App\Http\Controllers\IndikatorController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\LkpjController;
use App\Http\Controllers\OpdController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\RealisasiKinerjaController;
use App\Http\Controllers\SubKegiatanController;
use App\Http\Controllers\TargetTahunanController;
use App\Http\Controllers\UrusanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifikasiController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* ---------------- Master Data ---------------- */
    Route::middleware('role:super_admin,admin_bappeda')->group(function () {
        Route::resource('opd', OpdController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('urusan', UrusanController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('program', ProgramController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('kegiatan', KegiatanController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('subkegiatan', SubKegiatanController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('target-tahunan', TargetTahunanController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::resource('periode', PeriodeController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::patch('/periode/{periode}/toggle', [PeriodeController::class, 'toggleStatus'])->name('periode.toggle');

        Route::resource('user', UserController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('/audit', fn () => view('placeholder', ['title' => 'Audit Log']))->name('audit.index');

        /* ---------------- Indikator (IKU/IKK/IKD) ---------------- */
        Route::resource('indikator', IndikatorController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('/indikator/{indikator}/kelola', [IndikatorController::class, 'kelola'])->name('indikator.kelola');
        Route::post('/indikator/{indikator}/kelola/{tahun}/target', [IndikatorController::class, 'simpanTarget'])
            ->whereNumber('tahun')->name('indikator.target.simpan');
        Route::post('/indikator/{indikator}/kelola/{tahun}/{triwulan}/realisasi', [IndikatorController::class, 'simpanRealisasi'])
            ->whereNumber('tahun')->where('triwulan', '[1-4]')->name('indikator.realisasi.simpan');
    });

    /* ---------------- LKPJ (Operator OPD miliknya sendiri, Admin semua) ---------------- */
    Route::middleware('role:operator_opd,admin_bappeda,super_admin')->group(function () {
        Route::resource('lkpj', LkpjController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    /* ---------------- Evaluasi Renja ---------------- */
    Route::middleware('role:operator_opd,admin_bappeda,super_admin')->prefix('evaluasi')->name('evaluasi.')->group(function () {
        Route::get('/', [EvaluasiRenjaController::class, 'index'])->name('index');
        Route::get('/pilih', [EvaluasiRenjaController::class, 'pilih'])->name('pilih');
        Route::get('/kelola/{subkegiatan}/{tahun}', [EvaluasiRenjaController::class, 'kelola'])
            ->whereNumber('tahun')->name('kelola');
        Route::post('/kelola/{subkegiatan}/{tahun}/{triwulan}/simpan', [EvaluasiRenjaController::class, 'simpanTriwulan'])
            ->whereNumber('tahun')->where('triwulan', '[1-4]')->name('simpan');
        Route::patch('/kelola/{subkegiatan}/{tahun}/{triwulan}/ajukan', [EvaluasiRenjaController::class, 'ajukanTriwulan'])
            ->whereNumber('tahun')->where('triwulan', '[1-4]')->name('ajukan');
        Route::delete('/kelola/{subkegiatan}/{tahun}/{triwulan}', [EvaluasiRenjaController::class, 'hapusTriwulan'])
            ->whereNumber('tahun')->where('triwulan', '[1-4]')->name('hapus');
    });

    /* ---------------- Verifikasi ---------------- */
    Route::middleware('role:verifikator,admin_bappeda,super_admin')->prefix('verifikasi')->name('verifikasi.')->group(function () {
        Route::get('/', [VerifikasiController::class, 'index'])->name('index');
        Route::get('/{evaluasi}', [VerifikasiController::class, 'show'])->name('show');
        Route::patch('/{evaluasi}/approve', [VerifikasiController::class, 'approve'])->name('approve');
        Route::patch('/{evaluasi}/reject', [VerifikasiController::class, 'reject'])->name('reject');
    });

    /* ---------------- Realisasi Kinerja (semua role, read-only) ---------------- */
    Route::get('/realisasi', [RealisasiKinerjaController::class, 'index'])->name('realisasi.index');

    Route::prefix('form81')->name('form81.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Form81Controller::class, 'index'])->name('index');
    Route::get('/export/excel', [\App\Http\Controllers\Form81Controller::class, 'exportExcel'])->name('excel');
    Route::get('/export/pdf', [\App\Http\Controllers\Form81Controller::class, 'exportPdf'])->name('pdf');
});
});

require __DIR__.'/auth.php';