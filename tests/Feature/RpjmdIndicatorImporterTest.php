<?php

namespace Tests\Feature;

use App\Models\Indikator;
use App\Models\IndikatorRealisasi;
use App\Models\IndikatorTarget;
use App\Models\RpjmdIndicatorSource;
use App\Services\RpjmdIndicatorImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class RpjmdIndicatorImporterTest extends TestCase
{
    use RefreshDatabase { refreshDatabase as private refreshIsolatedDatabase; }

    public function refreshDatabase(): void
    {
        if (! str_ends_with((string) config('database.connections.'.config('database.default').'.database'), '_test')) {
            throw new \RuntimeException('Tes ini wajib memakai database terpisah dengan akhiran _test.');
        }
        $this->refreshIsolatedDatabase();
    }

    public function test_import_preserves_source_variants_baseline_ranges_and_existing_realisasi(): void
    {
        $directory = sys_get_temp_dir().'/rpjmd-'.bin2hex(random_bytes(6));
        mkdir($directory);
        try {
            foreach (RpjmdIndicatorImporter::FILES as $file => $jenis) {
                $book = new Spreadsheet();
                $sheet = $book->getActiveSheet();
                $sheet->setCellValue('B5', 'INDIKATOR');
                $start = $jenis === 'IKK' ? 6 : ($jenis === 'IKU' ? 4 : 5);
                foreach (range(2025, 2030) as $offset => $year) {
                    $sheet->setCellValue([$start + $offset, 6], (string) $year);
                }
                $opd = str_contains($file, 'Perangkat');
                if ($opd) {
                    $sheet->setCellValue($jenis === 'IKK' ? 'B8' : 'A8', '1.01.0.00.0.00.01.0000 - Dinas Pendidikan');
                }
                $row = $opd ? 9 : 8;
                $sheet->setCellValue('A'.$row, '1.');
                $sheet->setCellValue('B'.$row, 'Indikator uji');
                $sheet->setCellValue(($jenis === 'IKK' ? 'D' : 'C').$row, 'poin');
                if ($jenis === 'IKK') {
                    $sheet->setCellValue('C'.$row, 'negatif');
                }
                if ($jenis !== 'IKU') {
                    $sheet->setCellValue(($jenis === 'IKK' ? 'E' : 'D').$row, '0');
                }
                $sheet->setCellValue([$start, $row], $opd ? '99' : '72.49-72.67');
                (new Xlsx($book))->save($directory.'/'.$file);
                $book->disconnectWorksheets();
            }
            $old = Indikator::create(['jenis' => 'IKD', 'nama' => 'Indikator uji']);
            IndikatorRealisasi::create(['indikator_id' => $old->id, 'tahun' => 2024, 'triwulan' => 4, 'realisasi' => '17', 'status' => 'diverifikasi']);
            $importer = app(RpjmdIndicatorImporter::class);
            $result = $importer->import($directory);
            $this->assertSame(5, $result['sumber']);
            $this->assertSame(3, Indikator::count());
            $this->assertSame('72.49-72.67', IndikatorTarget::where('indikator_id', $old->id)->firstOrFail()->getRawOriginal('target'));
            $source = RpjmdIndicatorSource::where('jenis', 'IKK')->whereNotNull('perangkat_daerah')->firstOrFail();
            $this->assertSame('99', $source->targets[2025]);
            $this->assertSame('0', $source->baseline_2024);
            $this->assertSame('negatif', $source->arah);
            $this->assertDatabaseHas('indikator_realisasi', ['indikator_id' => $old->id, 'realisasi' => '17', 'status' => 'diverifikasi']);
            $this->assertDatabaseMissing('indikator_target', ['tahun' => 2024]);
            $importer->import($directory);
            $this->assertSame(5, RpjmdIndicatorSource::count());
            $this->assertSame(3, Indikator::count());
        } finally {
            foreach (glob($directory.'/*.xlsx') as $path) {
                unlink($path);
            }
            rmdir($directory);
        }
    }
}
