<?php

namespace Tests\Feature;

use App\Services\KonvergensiService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KonvergensiIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.konvergensi.cache_store' => 'array', 'session.driver' => 'array',
            'services.konvergensi.snapshot_path' => base_path('tests/Fixtures/no-snapshots')]);
        Cache::store('array')->flush();
        Http::preventStrayRequests();
        $this->withoutVite();
    }

    private function fixtures(): array
    {
        return json_decode(file_get_contents(base_path('tests/Fixtures/konvergensi-nias-barat.json')), true, 512, JSON_THROW_ON_ERROR);
    }

    private function fakeSource(?array $fixtures = null): void
    {
        $fixtures ??= $this->fixtures();
        Http::swap(new \Illuminate\Http\Client\Factory());
        Http::preventStrayRequests();
        Http::fake([
            '*progreskab/*' => Http::response($fixtures['districts']),
            '*progreskec/*' => Http::response($fixtures['subdistricts']),
        ]);
    }

    public function test_only_nias_barat_is_shown_and_table_precision_is_preserved_and_cached(): void
    {
        $fixtures = $this->fixtures();
        $otherDistrict = $fixtures['districts'][0];
        $otherDistrict['idkab'] = '1201';
        $otherDistrict['tabel'] = str_replace('Nias Barat', 'Kabupaten Lain', $otherDistrict['tabel']);
        $fixtures['districts'][] = $otherDistrict;
        $fixtures['subdistricts'][] = ['idkec' => '1201010', 'tabel' => '<tr><td>Wilayah Lain</td></tr>'];
        $this->fakeSource($fixtures);
        $this->get(route('konvergensi.index', ['idkab' => '1201']))->assertOk()
            ->assertSee('47.2%')->assertSee('105')->assertSee('Sirombu')->assertSee('Lahomi')
            ->assertSee('31.25%')->assertSee('Tabel Rincian Kecamatan')->assertSee('—')
            ->assertDontSee('Kabupaten Lain')->assertDontSee('Wilayah Lain');
        Http::assertSent(fn ($request) => str_contains($request->url(), '/progreskab/12/8') && $request['idprov'] === '12' && $request->hasHeader('X-Requested-With', 'XMLHttpRequest'));
        Http::assertSent(fn ($request) => str_contains($request->url(), '/progreskec/1225/8') && $request['idkab'] === '1225');
        $this->get(route('konvergensi.index'))->assertOk();
        Http::assertSentCount(2);
        $data = app(KonvergensiService::class)->dashboard(2026);
        $this->assertCount(8, $data['subdistricts']);
        $this->assertSame(47.2, $data['district']['progress']);
        $this->assertSame('', $data['district']['cells'][8]);
        $this->assertSame('0', $data['district']['cells'][16]);
    }

    public function test_supported_year_maps_to_official_period_and_invalid_year_is_rejected(): void
    {
        $this->fakeSource();
        $this->get(route('konvergensi.index', ['tahun' => 2025]))->assertOk()->assertSee('Tahun 2025');
        Http::assertSent(fn ($request) => str_contains($request->url(), '/progreskab/12/7'));
        $this->getJson(route('konvergensi.index', ['tahun' => 2030]))->assertUnprocessable()->assertJsonValidationErrors('tahun');
        Http::assertSentCount(2);
    }

    public function test_source_failure_is_clear_and_failure_is_not_cached(): void
    {
        Http::fake(fn () => throw new ConnectionException('internal transport details'));
        $this->get(route('konvergensi.index'))->assertStatus(503)->assertSee('belum dapat dimuat')
            ->assertDontSee('internal transport details')->assertDontSee('47.2%');
        $this->fakeSource();
        $this->get(route('konvergensi.index'))->assertOk()->assertSee('47.2%');
    }

    public function test_missing_district_and_schema_changes_never_show_mislabelled_data(): void
    {
        $fixtures = $this->fixtures();
        $fixtures['districts'][0]['idkab'] = '1201';
        $this->fakeSource($fixtures);
        $this->get(route('konvergensi.index'))->assertStatus(503)->assertSee('Data Kabupaten Nias Barat belum tersedia');
        $fixtures = $this->fixtures();
        $fixtures['districts'][0]['tabel'] = '<tr><td>Nias Barat</td><td>100</td></tr>';
        $this->fakeSource($fixtures);
        $this->get(route('konvergensi.index'))->assertStatus(503)->assertSee('Struktur tabel sumber berubah');
    }

    public function test_upstream_html_is_never_rendered_and_invalid_progress_does_not_set_css(): void
    {
        $fixtures = $this->fixtures();
        $fixtures['subdistricts'][0]['tabel'] = str_replace('Sirombu', '<img src=x onerror=alert(9)>Sirombu<script>alert(8)</script>', $fixtures['subdistricts'][0]['tabel']);
        $fixtures['subdistricts'][0]['tabel'] = str_replace('31.25', '200', $fixtures['subdistricts'][0]['tabel']);
        $this->fakeSource($fixtures);
        $this->get(route('konvergensi.index'))->assertOk()->assertSee('Sirombu')
            ->assertDontSee('onerror=')->assertDontSee('alert(8)')->assertDontSee('width: 200%', false);
    }

    public function test_invalid_json_and_http_errors_are_handled(): void
    {
        Http::fake(['*' => Http::response('<html>Unavailable</html>', 200)]);
        $this->get(route('konvergensi.index'))->assertStatus(503);
        Http::fake(['*' => Http::response([], 500)]);
        $this->get(route('konvergensi.index'))->assertStatus(503);
    }

    public function test_verified_snapshot_keeps_tables_visible_during_source_outage(): void
    {
        config(['services.konvergensi.snapshot_path' => resource_path('data/konvergensi')]);
        Http::fake(fn () => throw new ConnectionException('upstream unavailable'));
        $this->get(route('konvergensi.index'))->assertOk()->assertSee('data cadangan terakhir')
            ->assertSee('Sirombu')->assertSee('47.2%')->assertSee('Tabel Rincian Kecamatan');
    }

    public function test_home_summary_uses_same_region_data_and_preserves_fallback_status(): void
    {
        $this->fakeSource();
        $this->getJson(route('konvergensi.summary'))->assertOk()->assertJsonPath('progress', '47.2')
            ->assertJsonPath('villages', '105')->assertJsonPath('subdistricts', 8)
            ->assertJsonPath('is_fallback', false)->assertJsonPath('year', 2026);
        Cache::store('array')->flush();
        config(['services.konvergensi.snapshot_path' => resource_path('data/konvergensi')]);
        Http::swap(new \Illuminate\Http\Client\Factory());
        Http::fake(fn () => throw new ConnectionException('upstream unavailable'));
        $this->getJson(route('konvergensi.summary'))->assertOk()->assertJsonPath('is_fallback', true);
    }

    public function test_home_section_renders_link_and_loads_data_without_blocking_home_render(): void
    {
        $this->view('landing._konvergensi')->assertSee('Aksi Konvergensi Penurunan Stunting')
            ->assertSee(route('konvergensi.index'), false)->assertSee('Lihat Data &amp; Tabel', false)
            ->assertSee('Data cadangan terakhir');
        Http::assertNothingSent();
        Http::fake(fn () => throw new ConnectionException('private connection details'));
        $this->getJson(route('konvergensi.summary'))->assertStatus(503)
            ->assertJsonPath('message', 'Ringkasan konvergensi belum dapat dimuat.')
            ->assertDontSee('private connection details');
    }

    public function test_last_success_is_preserved_and_refresh_recovers_after_outage(): void
    {
        $this->fakeSource();
        $data = app(KonvergensiService::class)->dashboard(2026);
        Cache::store('array')->forget('konvergensi:v1:1225:2026');
        Http::swap(new \Illuminate\Http\Client\Factory());
        Http::fake(fn () => throw new ConnectionException('upstream unavailable'));
        $fallback = app(KonvergensiService::class)->dashboard(2026);
        $this->assertTrue($fallback['is_fallback']);
        $this->assertSame($data['retrieved_at'], $fallback['retrieved_at']);
        $this->fakeSource();
        $this->assertFalse(app(KonvergensiService::class)->dashboard(2026)['is_fallback']);
    }
}
