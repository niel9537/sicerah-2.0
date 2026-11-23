<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluasiImportController;
use App\Http\Controllers\EvaluasiRenjaController;
use App\Http\Controllers\IndikatorController;
use App\Http\Controllers\IndikatorImportController;
use App\Http\Controllers\JdihController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LkpjController;
use App\Http\Controllers\OpdController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\PublicIndikatorController;
use App\Http\Controllers\RealisasiKinerjaController;
use App\Http\Controllers\StrukturImportController;
use App\Http\Controllers\SubKegiatanController;
use App\Http\Controllers\TargetTahunanController;
use App\Http\Controllers\UrusanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifikasiController;
use Illuminate\Support\Facades\Route;

/* ---------------- Publik (tanpa login) ---------------- */
// Landing page menggantikan redirect langsung ke /dashboard: menampilkan
// capaian IKU, dasbor kinerja ringkas, indikator makro daerah, JDIH, dan
// profil Bappelitbangda kepada siapa saja tanpa perlu masuk ke sistem.
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/api/cuaca/nias-barat', \App\Http\Controllers\WeatherController::class)->name('weather.nias-barat');
Route::get('/indikator-publik', [PublicIndikatorController::class, 'index'])->name('indikator.publik');
require __DIR__.'/webgis.php';
Route::get('/bps', [\App\Http\Controllers\BpsController::class, 'index'])->name('bps.index');
Route::get('/bps/tabel/{id}', [\App\Http\Controllers\BpsController::class, 'table'])->whereNumber('id')->name('bps.table');

Route::view('/struktur-hierarki', 'public.empty', ['title' => 'Struktur Hierarki', 'section' => 'Struktur Hierarki'])->name('publik.struktur');
Route::view('/kinerja/iku-opd', 'public.empty', ['title' => 'IKU OPD', 'section' => 'Kinerja'])->name('publik.iku-opd');
Route::get('/publikasi', [\App\Http\Controllers\PublicationController::class, 'index'])->name('publik.publikasi');
Route::get('/publikasi/{publication}', [\App\Http\Controllers\PublicationController::class, 'show'])->name('publik.publikasi.show');
Route::middleware(['auth', 'role:super_admin'])->prefix('dashboard/cms')->name('cms.')->group(function () {
    Route::resource('publications', \App\Http\Controllers\PublicationAdminController::class)->except('show');
});

Route::prefix('jdih')->name('jdih.')->group(function () {
    Route::get('/', [JdihController::class, 'index'])->name('index');
});

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

        // Impor/ekspor massal struktur Urusan → Program → Kegiatan → Sub Kegiatan lewat Excel
        Route::prefix('struktur/import')->name('struktur.import.')->group(function () {
            Route::get('/', [StrukturImportController::class, 'form'])->name('form');
            Route::get('/template', [StrukturImportController::class, 'template'])->name('template');
            Route::post('/', [StrukturImportController::class, 'import'])->name('store');
        });

        Route::resource('periode', PeriodeController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::patch('/periode/{periode}/toggle', [PeriodeController::class, 'toggleStatus'])->name('periode.toggle');

        Route::resource('user', UserController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('/audit', fn () => view('placeholder', ['title' => 'Audit Log']))->name('audit.index');

        /* ---------------- Indikator (IKU/IKK/IKD) ---------------- */
        Route::get('/rpjmd', [\App\Http\Controllers\RpjmdController::class, 'index'])->name('rpjmd.index');
        Route::resource('indikator', IndikatorController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('/indikator/{indikator}/kelola', [IndikatorController::class, 'kelola'])->name('indikator.kelola');
        Route::post('/indikator/{indikator}/kelola/{tahun}/target', [IndikatorController::class, 'simpanTarget'])
            ->whereNumber('tahun')->name('indikator.target.simpan');
        Route::post('/indikator/{indikator}/kelola/{tahun}/{triwulan}/realisasi', [IndikatorController::class, 'simpanRealisasi'])
            ->whereNumber('tahun')->where('triwulan', '[1-4]')->name('indikator.realisasi.simpan');

        // Impor/ekspor massal target & realisasi indikator lewat Excel
        Route::get('/indikator/import', [IndikatorImportController::class, 'form'])->name('indikator.import.form');
        Route::get('/indikator/import/template', [IndikatorImportController::class, 'template'])->name('indikator.import.template');
        Route::post('/indikator/import', [IndikatorImportController::class, 'import'])->name('indikator.import.store');
        Route::post('/indikator/import/bank-data', [IndikatorImportController::class, 'importBankData'])->name('indikator.import.bank-data');

        Route::redirect('/webgis-admin', '/dashboard/webgis/kelola');

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

        // Impor/ekspor massal evaluasi renja lewat Excel — sengaja dibatasi admin saja
        // (bukan operator_opd) karena impor tidak menerapkan pembatasan kepemilikan OPD
        // per baris seperti alur input manual.
        Route::middleware('role:super_admin,admin_bappeda')->prefix('import')->name('import.')->group(function () {
            Route::get('/', [EvaluasiImportController::class, 'form'])->name('form');
            Route::get('/template', [EvaluasiImportController::class, 'template'])->name('template');
            Route::post('/', [EvaluasiImportController::class, 'import'])->name('store');
        });
    });

    /* ---------------- Verifikasi ---------------- */
    Route::middleware('role:verifikator,admin_bappeda,super_admin')->prefix('verifikasi')->name('verifikasi.')->group(function () {
        Route::get('/', [VerifikasiController::class, 'index'])->name('index');
        Route::get('/{evaluasi}', [VerifikasiController::class, 'show'])->name('show');
        Route::patch('/{evaluasi}/approve', [VerifikasiController::class, 'approve'])->name('approve');
        Route::patch('/{evaluasi}/reject', [VerifikasiController::class, 'reject'])->name('reject');
    });

    Route::get('/emonev-prosn-2026', [\App\Http\Controllers\ProsnController::class, 'index'])->name('prosn.index');
    Route::middleware('role:inspektorat')->group(function () {
        Route::get('/emonev-prosn-2026/{prosn}/validasi', [\App\Http\Controllers\ProsnController::class, 'validasi'])->name('prosn.validasi');
        Route::post('/emonev-prosn-2026/{prosn}/validasi', [\App\Http\Controllers\ProsnController::class, 'validateItem'])->name('prosn.validate');
    });
    Route::get('/emonev-prosn-2026/export', [\App\Http\Controllers\ProsnController::class, 'export'])->name('prosn.export');
    Route::middleware('role:super_admin,admin_bappeda')->group(function () {
        Route::get('/emonev-prosn-2026/{prosn}/edit', [\App\Http\Controllers\ProsnController::class, 'edit'])->name('prosn.edit');
        Route::put('/emonev-prosn-2026/{prosn}', [\App\Http\Controllers\ProsnController::class, 'update'])->name('prosn.update');
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
