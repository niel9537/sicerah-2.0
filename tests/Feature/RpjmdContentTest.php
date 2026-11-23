<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\RpjmdContent;
use Tests\TestCase;

class RpjmdContentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array', 'session.driver' => 'array']);
        \Illuminate\Support\Facades\DB::purge('sqlite');
        \Illuminate\Support\Facades\Schema::create('indikator', function ($table) {
            $table->id(); $table->text('nama'); $table->string('jenis')->default('IKU'); $table->string('satuan')->nullable(); $table->string('kode')->nullable(); $table->timestamp('updated_at')->nullable();
        });
        \Illuminate\Support\Facades\Schema::create('indikator_target', function ($table) {
            $table->id(); $table->integer('indikator_id'); $table->integer('tahun'); $table->string('target'); $table->timestamps();
        });
        (require database_path('migrations/2026_11_07_230000_create_rpjmd_indicator_sources.php'))->up();
        $this->withoutVite();
    }

    private function defaults(): array
    {
        return json_decode(file_get_contents(resource_path('data/rpjmd.json')), true);
    }

    public function test_public_page_shows_five_pairs_and_navigation(): void
    {
        $data = $this->defaults();
        $this->mock(RpjmdContent::class, fn ($mock) => $mock->shouldReceive('get')->andReturn($data));
        $response = $this->get(route('publik.rpjmd'))->assertOk()->assertSee('Misi 5')->assertSee('Tujuan 5');
        foreach ($data['pairs'] as $pair) {
            $response->assertSee($pair['misi'])->assertSee($pair['tujuan']);
        }
    }

    public function test_cms_requires_super_admin(): void
    {
        $this->get(route('cms.rpjmd.edit'))->assertRedirect(route('login'));
        $this->actingAs(new User(['role' => 'operator_opd']))->get(route('cms.rpjmd.edit'))->assertForbidden();
        $this->put(route('cms.rpjmd.update'), $this->defaults())->assertForbidden();
    }

    public function test_admin_can_save_exactly_five_pairs(): void
    {
        $data = $this->defaults();
        $this->mock(RpjmdContent::class, function ($mock) use ($data) {
            $mock->shouldReceive('get')->andReturn($data);
            $mock->shouldReceive('save')->once()->with($data);
        });
        $this->actingAs(new User(['role' => 'super_admin']))
            ->put(route('cms.rpjmd.update'), $data)->assertRedirect(route('cms.rpjmd.edit'));
    }

    public function test_incomplete_pairs_are_rejected(): void
    {
        $data = $this->defaults();
        array_pop($data['pairs']);
        $this->mock(RpjmdContent::class, fn ($mock) => $mock->shouldNotReceive('save'));
        $this->actingAs(new User(['role' => 'super_admin']))
            ->put(route('cms.rpjmd.update'), $data)->assertSessionHasErrors('pairs');
    }

    public function test_linked_targets_are_live_and_source_values_are_preserved(): void
    {
        $indicator = \App\Models\Indikator::create(['nama' => 'Harapan Lama Sekolah (HLS)', 'jenis' => 'IKU']);
        $indicator->targets()->create(['tahun' => 2025, 'target' => '0']);
        $service = app(\App\Services\RpjmdHierarchy::class);
        $rows = $service->present($this->defaults());
        $row = collect($rows)->firstWhere('row', 8);
        $this->assertSame($indicator->id, $row['indikator']->id);
        $this->assertSame('0', $row['targets'][2025]);
        $this->assertSame('13.08-13.28', $row['source_targets'][2025]);
        $indicator->targets()->where('tahun', 2025)->update(['target' => '14.5']);
        $this->assertSame('14.5', collect($service->present($this->defaults()))->firstWhere('row', 8)['targets'][2025]);
        $this->get(route('publik.rpjmd.indikator', $indicator))->assertOk()->assertSee('14.5');
    }

    public function test_manual_mapping_resolves_ambiguous_names_and_uses_regional_targets(): void
    {
        $one = \App\Models\Indikator::create(['nama' => 'Harapan Lama Sekolah', 'jenis' => 'IKD']);
        $two = \App\Models\Indikator::create(['nama' => 'Harapan Lama Sekolah (Tahun)', 'jenis' => 'IKU']);
        \App\Models\RpjmdIndicatorSource::create(['indikator_id' => $two->id, 'file' => 'Daerah.xls', 'baris' => 1, 'jenis' => 'IKU', 'nama' => $two->nama, 'targets' => [2025 => '14-15']]);
        $service = app(\App\Services\RpjmdHierarchy::class);
        $row = collect($service->present($this->defaults()))->firstWhere('row', 8);
        $this->assertSame($one->id, $row['indikator']->id);
        $data = $this->defaults(); $data['links'] = [8 => $two->id];
        $row = collect($service->present($data))->firstWhere('row', 8);
        $this->assertSame($two->id, $row['indikator']->id);
        $this->assertSame('14-15', $row['targets'][2025]);
        $this->mock(RpjmdContent::class, fn ($mock) => $mock->shouldReceive('save')->once()->with($data));
        $this->actingAs(new User(['role' => 'super_admin']))->put(route('cms.rpjmd.update'), $data)->assertSessionHasNoErrors();
    }

    public function test_mapping_cannot_reference_a_missing_indicator(): void
    {
        $data = $this->defaults(); $data['links'] = [8 => 999999];
        $this->mock(RpjmdContent::class, fn ($mock) => $mock->shouldNotReceive('save'));
        $this->actingAs(new User(['role' => 'super_admin']))->put(route('cms.rpjmd.update'), $data)->assertSessionHasErrors('links.8');
    }

    public function test_source_contains_all_sixty_indicators_and_six_years(): void
    {
        $rows = app(\App\Services\RpjmdHierarchy::class)->rows();
        $this->assertCount(60, $rows);
        $this->assertCount(11, collect($rows)->filter(fn ($row) => $row['sasaran'] !== '')->unique(fn ($row) => $row['pair'].'|'.$row['sasaran']));
        foreach ($rows as $row) $this->assertSame(range(2025,2030), array_keys($row['targets']));
    }
}
