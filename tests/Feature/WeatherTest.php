<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WeatherTest extends TestCase
{
    public function test_forecast_uses_current_slot_and_caches_bmkg_response(): void
    {
        Cache::flush();
        $this->travelTo(now()->setTimezone('UTC')->setTime(12, 30));
        $slot = fn ($hours, $temperature) => [
            'utc_datetime' => now()->utc()->startOfHour()->addHours($hours)->format('Y-m-d H:i:s'),
            't' => $temperature, 'weather_desc' => 'Berawan',
        ];
        Http::fake(['api.bmkg.go.id/*' => Http::response([
            'lokasi' => ['adm4' => '12.25.01.2008'],
            'data' => [['cuaca' => [[$slot(-4, 20), $slot(0, 27), $slot(3, 29)]]]],
        ])]);
        $this->getJson('/api/cuaca/nias-barat')->assertOk()
            ->assertJsonPath('temperature', 27)->assertJsonPath('source', 'BMKG');
        $this->getJson('/api/cuaca/nias-barat')->assertOk();
        Http::assertSentCount(1);
    }

    public function test_bmkg_failure_returns_safe_unavailable_response(): void
    {
        Cache::flush();
        Http::fake(['api.bmkg.go.id/*' => Http::response([], 500)]);
        $this->getJson('/api/cuaca/nias-barat')->assertStatus(503)
            ->assertExactJson(['message' => 'Cuaca sementara tidak tersedia.']);
    }

    public function test_expired_forecast_is_not_displayed(): void
    {
        Cache::flush();
        Cache::put('weather.bmkg.onolimbu.v1', [['timestamp' => now()->subHours(4)->timestamp]], 900);
        Http::preventStrayRequests();
        $this->getJson('/api/cuaca/nias-barat')->assertStatus(503);
        $this->assertFalse(Cache::has('weather.bmkg.onolimbu.v1'));
    }
}
