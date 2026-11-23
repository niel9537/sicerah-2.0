<?php

namespace Tests\Feature;

use App\Services\BpsService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BpsIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.bps.api_key' => 'private-test-bps-key', 'services.bps.domain' => '1225',
            'services.bps.cache_store' => 'array', 'session.driver' => 'array']);
        Cache::store('array')->flush();
        Http::preventStrayRequests();
        $this->withoutVite();
    }

    private function payload(array $records, array $meta = []): array
    {
        return ['status' => 'OK', 'data-availability' => 'available', 'data' => [
            array_merge(['page' => 1, 'pages' => 1, 'total' => count($records)], $meta), $records,
        ]];
    }

    public function test_public_bps_menu_queries_domain_and_preserves_filters_in_pagination(): void
    {
        Http::fake(['*' => Http::response($this->payload([['table_id' => 86, 'title' => 'Penduduk Nias Barat', 'subj' => 'Kependudukan', 'updt_date' => '2025-05-15', 'excel' => 'https://webapi.bps.go.id/download.php?f=abc']], ['pages' => 2, 'total' => 11]))]);
        $this->get(route('bps.index', ['q' => 'penduduk', 'tahun' => 2025]))->assertOk()->assertSee('Penduduk Nias Barat')->assertSee('1225')->assertSee('Lihat Tabel')->assertSee('Unduh Excel')->assertSee('page=2', false)->assertSee('q=penduduk', false)->assertDontSee('private-test-bps-key');
        Http::assertSent(fn ($request) => str_contains($request->url(), '/domain/1225/key/private-test-bps-key') && $request['keyword'] === 'penduduk' && (int) $request['year'] === 2025);
        $this->get(route('bps.index', ['q' => 'penduduk', 'tahun' => 2025]))->assertOk();
        Http::assertSentCount(1);
    }

    public function test_publications_use_real_bps_fields_and_allow_only_official_safe_links(): void
    {
        Http::fake(['*' => Http::response($this->payload([
            ['pub_id' => 'abc', 'title' => 'Nias Barat Dalam Angka', 'abstract' => '<b>Statistik daerah</b>', 'rl_date' => '2026-02-28', 'pdf' => 'https://webapi.bps.go.id/download.php?f=abc', 'cover' => 'https://niasbaratkab.bps.go.id/cover.jpg'],
            ['title' => 'Tidak aman', 'pdf' => 'https://evil.test/file.pdf', 'cover' => 'javascript:alert(1)'],
            ['title' => 'Rahasia', 'pdf' => 'https://webapi.bps.go.id/file?key=private-test-bps-key'],
        ]))]);
        $this->get(route('bps.index', ['kategori' => 'publication']))->assertOk()->assertSee('Nias Barat Dalam Angka')->assertSee('Statistik daerah')->assertSee('Unduh PDF')->assertDontSee('evil.test')->assertDontSee('javascript:')->assertDontSee('private-test-bps-key');
    }

    public function test_empty_indicators_show_honest_empty_state(): void
    {
        Http::fake(['*' => Http::response(['status' => 'OK', 'data-availability' => 'list-not-available', 'data' => ''])]);
        $this->get(route('bps.index', ['kategori' => 'indicators']))->assertOk()->assertSee('Indikator strategis belum tersedia')->assertSee('Tabel Statistik');
    }

    public function test_upstream_errors_timeouts_and_missing_key_never_expose_credentials(): void
    {
        Http::fake(['*' => Http::response(['status' => 'Error', 'message' => 'private-test-bps-key'], 403)]);
        $this->get(route('bps.index'))->assertStatus(503)->assertSee('Layanan BPS sedang tidak dapat diakses')->assertDontSee('private-test-bps-key');
        Http::fake(fn () => throw new ConnectionException('URL includes private-test-bps-key'));
        $this->get(route('bps.index'))->assertStatus(503)->assertDontSee('private-test-bps-key');
        config(['services.bps.api_key' => '']);
        $this->get(route('bps.index'))->assertStatus(503)->assertSee('Koneksi BPS belum dikonfigurasi');
    }

    public function test_detail_preserves_table_spans_and_escapes_untrusted_html(): void
    {
        Http::fake(['*' => Http::response(['status' => 'OK', 'data-availability' => 'available', 'data' => [
            'table_id' => 86, 'title' => 'Data Penduduk', 'table' => '<table><tr><th colspan="2">Wilayah</th></tr><tr><td onclick="alert(1)">Nias Barat<script>alert(2)</script></td><td>100</td></tr></table>',
        ]])]);
        $this->get(route('bps.table', 86))->assertOk()->assertSee('Data Penduduk')->assertSee('Nias Barat')->assertSee('colspan="2"', false)->assertDontSee('onclick=')->assertDontSee('alert(2)')->assertDontSee('private-test-bps-key');
    }

    public function test_unknown_table_and_invalid_inputs_do_not_call_bps(): void
    {
        Http::fake(['*' => Http::response(['status' => 'OK', 'data-availability' => 'not-available', 'data' => ''])]);
        $this->get(route('bps.table', 999))->assertNotFound();
        $this->getJson(route('bps.index', ['kategori' => 'evil', 'page' => -1, 'tahun' => 1800]))->assertUnprocessable()->assertJsonValidationErrors(['kategori', 'page', 'tahun']);
        Http::assertSentCount(1);
    }

    public function test_cache_is_separate_for_each_page_and_category(): void
    {
        Http::fake(['*' => Http::response($this->payload([]))]);
        $bps = app(BpsService::class);
        $bps->listing('statictable', 1); $bps->listing('statictable', 1);
        $bps->listing('statictable', 2); $bps->listing('publication', 1);
        Http::assertSentCount(3);
    }
}
