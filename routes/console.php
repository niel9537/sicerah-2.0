<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('rpjmd:import {directory}', function (\App\Services\RpjmdIndicatorImporter $importer) {
    $result = $importer->import($this->argument('directory'));
    $this->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
})->purpose('Sinkronkan lima dokumen indikator RPJMD; pertahankan rincian sumber dan baseline');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('prosn:akun-inspektorat {email=inspektorat@sicerah.local} {--nama=Inspektorat Kabupaten Nias Barat}', function () {
    $email = $this->argument('email');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $this->error('Email tidak valid.'); return 1; }
    if (\App\Models\User::where('email',$email)->exists()) { $this->error('Email sudah terdaftar. Akun tidak diubah.'); return 1; }
    $password = \Illuminate\Support\Str::password(20, symbols: false);
    \App\Models\User::create(['nama'=>$this->option('nama'),'email'=>$email,'password'=>$password,'role'=>'inspektorat']);
    $this->info('Akun Inspektorat berhasil dibuat.');
    $this->line('Email: '.$email);
    $this->line('Password: '.$password);
})->purpose('Buat akun khusus validasi eMonev ProSN dengan kata sandi acak');
