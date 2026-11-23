<?php

namespace Tests\Feature;

use App\Models\Indikator;
use App\Models\IndikatorTarget;
use App\Services\BankDataIndicatorImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankDataIndicatorImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_only_aspect_rows_and_leaves_ikk_untouched(): void
    {
        $ikk = Indikator::create(['jenis' => 'IKK', 'nama' => 'IKK uji']);

        $result = app(BankDataIndicatorImporter::class)->import(storage_path('app/imports/Bank Data.xlsx'));

        $this->assertSame(119, $result['indikator']);
        $this->assertSame(12, $result['iku']);
        $this->assertSame(9, $result['makro']);
        $this->assertSame(119, Indikator::ikd()->bankData()->count());
        $this->assertDatabaseHas('indikator', ['id' => $ikk->id, 'jenis' => 'IKK', 'nama' => 'IKK uji']);

        $lifeExpectancy = Indikator::where('nama', 'Usia Harapan Hidup')->firstOrFail();
        $this->assertSame('72,49-72,67', IndikatorTarget::where('indikator_id', $lifeExpectancy->id)->where('tahun', 2025)->firstOrFail()->getRawOriginal('target'));
    }
}
