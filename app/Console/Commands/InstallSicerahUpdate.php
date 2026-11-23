<?php

namespace App\Console\Commands;

use Database\Seeders\GisSeeder;
use Illuminate\Console\Command;

class InstallSicerahUpdate extends Command
{
    protected $signature = 'sicerah:install-update {--bank-data= : Lokasi alternatif Bank Data.xlsx}';

    protected $description = 'Pasang migration, data WebGIS, dan 119 indikator Bank Data untuk pembaruan SICERAH';

    public function handle(): int
    {
        $this->components->info('Menjalankan migration basis data');
        if ($this->call('migrate', ['--force' => true]) !== self::SUCCESS) {
            return self::FAILURE;
        }

        $this->components->info('Menyinkronkan wilayah dan layer awal WebGIS');
        if ($this->call('db:seed', ['--class' => GisSeeder::class, '--force' => true]) !== self::SUCCESS) {
            return self::FAILURE;
        }

        $arguments = [];
        if ($path = $this->option('bank-data')) {
            $arguments['path'] = $path;
        }

        $this->components->info('Menyinkronkan IKD, IKU, dan indikator makro');
        if ($this->call('indikator:import-bank-data', $arguments) !== self::SUCCESS) {
            return self::FAILURE;
        }

        $this->callSilently('optimize:clear');
        $this->newLine();
        $this->components->info('Pembaruan SICERAH selesai dipasang. IKK tidak diubah.');

        return self::SUCCESS;
    }
}
