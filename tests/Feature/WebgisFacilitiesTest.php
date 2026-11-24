<?php

namespace Tests\Feature;

use App\Services\WebgisFacilities;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WebgisFacilitiesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        foreach (['webgis.facilities.v1', 'webgis.facilities.unavailable'] as $key) {
            Cache::store('file')->forget($key);
        }
    }

    protected function tearDown(): void
    {
        foreach (['webgis.facilities.v1', 'webgis.facilities.unavailable'] as $key) {
            Cache::store('file')->forget($key);
        }
        parent::tearDown();
    }

    public function test_classifies_actual_geometry_without_inventing_building_footprints(): void
    {
        $ring = [['lon'=>97.4,'lat'=>1.0], ['lon'=>97.41,'lat'=>1.0], ['lon'=>97.41,'lat'=>1.01], ['lon'=>97.4,'lat'=>1.0]];
        $elements = [
            ['type'=>'way','id'=>1,'tags'=>['amenity'=>'school','name'=>'Sekolah Uji'],'geometry'=>$ring],
            ['type'=>'node','id'=>2,'tags'=>['healthcare'=>'clinic','name'=>'Klinik Uji'],'lon'=>97.4,'lat'=>1.0],
            ['type'=>'way','id'=>3,'tags'=>['office'=>'government','building'=>'yes'],'geometry'=>$ring],
            ['type'=>'relation','id'=>4,'tags'=>['amenity'=>'hospital'],'center'=>['lon'=>97.4,'lat'=>1.0]],
            ['type'=>'node','id'=>5,'tags'=>['amenity'=>'cafe'],'lon'=>97.4,'lat'=>1.0],
        ];
        $data = app(WebgisFacilities::class)->convert(['elements'=>$elements]);
        $this->assertCount(4, $data['features']);
        $this->assertSame('school', $data['features'][0]['properties']['category']);
        $this->assertSame('Polygon', $data['features'][0]['geometry']['type']);
        $this->assertSame('Area fasilitas (bukan tapak bangunan)', $data['features'][0]['properties']['geometry_label']);
        $this->assertSame('health', $data['features'][1]['properties']['category']);
        $this->assertSame('Point', $data['features'][1]['geometry']['type']);
        $this->assertSame('government', $data['features'][2]['properties']['category']);
        $this->assertSame('Tapak bangunan', $data['features'][2]['properties']['geometry_label']);
        $this->assertSame('Point', $data['features'][3]['geometry']['type']);
    }

    public function test_public_endpoint_caches_and_queries_only_the_regency(): void
    {
        Http::fake(['*overpass*'=>Http::response(['elements'=>[
            ['type'=>'count','tags'=>['areas'=>'1']],
            ['type'=>'node','id'=>1,'lat'=>1.0,'lon'=>97.4,'tags'=>['office'=>'government']],
        ]])]);
        $this->getJson(route('webgis.facilities'))->assertOk()
            ->assertJsonPath('features.0.properties.category', 'government');
        $this->getJson(route('webgis.facilities'))->assertOk();
        Http::assertSentCount(1);
        Http::assertSent(fn ($r) => str_contains($r['data'], 'Nias Barat') && str_contains($r['data'], '"admin_level"="5"'));
    }


    public function test_missing_administrative_area_is_not_reported_as_zero_facilities(): void
    {
        Http::fake(['*'=>Http::response(['elements'=>[['type'=>'count','tags'=>['areas'=>'0']]]])]);
        $this->getJson(route('webgis.facilities'))->assertStatus(503);
    }

    public function test_upstream_failure_does_not_report_an_empty_success(): void
    {
        Http::fake(['*'=>Http::response(['remark'=>'timeout'], 200)]);
        $this->getJson(route('webgis.facilities'))->assertStatus(503);
        $this->getJson(route('webgis.facilities'))->assertStatus(503);
        Http::assertSentCount(2);
    }
}
