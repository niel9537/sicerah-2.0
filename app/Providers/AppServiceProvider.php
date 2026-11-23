<?php

namespace App\Providers;

use App\Models\EvaluasiRenja;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
           // Explicit binding: route param "evaluasi" → model EvaluasiRenja
        \Illuminate\Support\Facades\Route::model('evaluasi', \App\Models\EvaluasiRenja::class);

        // Setup locale Bahasa Indonesia untuk Carbon (nama bulan/hari)
        \Carbon\Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'ID');
        // // Explicit binding karena nama route param "evaluasi" ≠ nama class "EvaluasiRenja"
        // Route::model('evaluasi', EvaluasiRenja::class);
    }
}