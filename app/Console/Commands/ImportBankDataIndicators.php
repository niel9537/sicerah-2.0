<?php

namespace App\Console\Commands;

use App\Services\BankDataIndicatorImporter;
use Illuminate\Console\Command;

class ImportBankDataIndicators extends Command
{
    protected $signature = 'indikator:import-bank-data {path? : Lokasi file Bank Data.xlsx}';

    protected $description = 'Impor 119 IKD beserta penanda IKU dan indikator makro dari Bank Data.xlsx';

    public function handle(BankDataIndicatorImporter $importer): int
    {
        $path = $this->argument('path') ?: storage_path('app/imports/Bank Data.xlsx');

        if (! is_file($path)) {
            $this->error("File tidak ditemukan: {$path}");

            return self::FAILURE;
        }

        try {
            $result = $importer->import($path);
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Selesai: {$result['indikator']} IKD, {$result['iku']} IKU, {$result['makro']} indikator makro.");
        $this->line("Nilai tersimpan: {$result['realisasi']} capaian dan {$result['target']} target.");

        return self::SUCCESS;
    }
}
