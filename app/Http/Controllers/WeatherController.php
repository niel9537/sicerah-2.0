<?php

namespace App\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class WeatherController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            $forecast = Cache::remember('weather.bmkg.onolimbu.v1', now()->addMinutes(15), function () {
                $data = Http::acceptJson()->connectTimeout(3)->timeout(8)
                    ->get('https://api.bmkg.go.id/publik/prakiraan-cuaca', ['adm4' => '12.25.01.2008'])
                    ->throw()->json();
                if (data_get($data, 'lokasi.adm4') !== '12.25.01.2008') {
                    throw new \UnexpectedValueException('Unexpected BMKG location.');
                }
                $slots = collect(data_get($data, 'data.0.cuaca', []))->flatten(1)
                    ->filter(fn ($slot) => is_array($slot) && isset($slot['utc_datetime'], $slot['t'], $slot['weather_desc']))
                    ->map(function ($slot) {
                        $slot['timestamp'] = CarbonImmutable::parse($slot['utc_datetime'], 'UTC')->timestamp;
                        return $slot;
                    })->sortBy('timestamp')->values()->all();
                if (! $slots) {
                    throw new \UnexpectedValueException('Empty BMKG forecast.');
                }
                return $slots;
            });
            $slot = collect($forecast)->first(fn ($slot) => $slot['timestamp'] + 10800 > now()->timestamp);
            if (! $slot) {
                Cache::forget('weather.bmkg.onolimbu.v1');
                return response()->json(['message' => 'Prakiraan cuaca belum tersedia.'], 503);
            }
            return response()->json([
                'location' => 'Onolimbu, Lahomi, Nias Barat',
                'temperature' => $slot['t'],
                'description' => $slot['weather_desc'],
                'forecast_at' => CarbonImmutable::createFromTimestamp($slot['timestamp'], 'Asia/Jakarta')->format('d/m H:i').' WIB',
                'source' => 'BMKG',
            ]);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json(['message' => 'Cuaca sementara tidak tersedia.'], 503);
        }
    }
}
