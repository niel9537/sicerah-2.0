<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebgisController;
use App\Http\Controllers\WebgisAdminController;
Route::get('/webgis', [WebgisController::class, 'index'])->name('webgis.index');
Route::get('/webgis/tema/{theme}/data', [WebgisController::class, 'data'])->whereNumber('theme')->name('webgis.data');
Route::get('/dashboard/webgis', [WebgisController::class, 'dashboard'])->middleware('auth')->name('webgis.dashboard');
Route::middleware(['auth', 'role:super_admin,admin_bappeda'])->prefix('dashboard/webgis/kelola')->name('webgis.admin.')->group(function () {
    Route::get('/', [WebgisAdminController::class, 'index'])->name('index');
    Route::post('/tema', [WebgisAdminController::class, 'store'])->name('store');
    Route::get('/tema/{theme}', [WebgisAdminController::class, 'edit'])->whereNumber('theme')->name('edit');
    Route::put('/tema/{theme}', [WebgisAdminController::class, 'update'])->whereNumber('theme')->name('update');
    Route::delete('/tema/{theme}', [WebgisAdminController::class, 'destroy'])->whereNumber('theme')->name('destroy');
    Route::post('/tema/{theme}/nilai', [WebgisAdminController::class, 'value'])->whereNumber('theme')->name('value');
    Route::delete('/tema/{theme}/nilai/{value}', [WebgisAdminController::class, 'deleteValue'])->whereNumber(['theme','value'])->name('deleteValue');
    Route::post('/tema/{theme}/csv', [WebgisAdminController::class, 'csv'])->whereNumber('theme')->name('csv');
});

Route::middleware(['auth','role:super_admin,admin_bappeda'])->prefix('dashboard/webgis/pendidikan')->name('webgis.education.')->controller(\App\Http\Controllers\WebgisEducationController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::post('/csv', 'csv')->name('csv');
    Route::put('/{education}', 'update')->whereNumber('education')->name('update');
    Route::delete('/{education}', 'destroy')->whereNumber('education')->name('destroy');
});

Route::get('/webgis/pendidikan/data', [\App\Http\Controllers\WebgisEducationController::class,'data'])->name('webgis.education.data');
