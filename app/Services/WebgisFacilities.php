<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WebgisFacilities
{
    public function fetch(): array
    {
        // Administrative area, rather than a rectangle that includes neighbouring regencies.
        $query = <<<'QL'
        [out:json][timeout:15];
        area["boundary"="administrative"]["admin_level"="5"]["name"~"^(Kabupaten )?Nias Barat$"]->.region;
        .region out count;
        (
          nwr(area.region)["office"="government"];
          nwr(area.region)["building"="government"];
          nwr(area.region)["amenity"~"^(townhall|police|courthouse|fire_station|school|kindergarten|college|university|hospital|clinic|doctors|dentist|health_post)$"];
          nwr(area.region)["healthcare"~"^(hospital|clinic|doctor|dentist|health_post|centre)$"];
        );
        out geom center;
        QL;
        $error = null;
        foreach (['https://overpass-api.de/api/interpreter', 'https://overpass.kumi.systems/api/interpreter'] as $url) {
            try {
                $data = Http::acceptJson()->connectTimeout(4)->timeout(18)->get($url, ['data' => $query])->throw()->json();
                if (! is_array($data['elements'] ?? null) || isset($data['remark'])) {
                    throw new \RuntimeException('Incomplete Overpass response.');
                }
                $region = collect($data['elements'])->firstWhere('type', 'count');
                if ((int) ($region['tags']['areas'] ?? 0) < 1) {
                    throw new \RuntimeException('Administrative area Nias Barat unavailable.');
                }
                return $this->convert($data);
            } catch (\Throwable $e) {
                $error = $e;
            }
        }
        throw $error;
    }

    public function convert(array $data): array
    {
        $features = [];
        foreach ($data['elements'] as $element) {
            $tags = $element['tags'] ?? [];
            $amenity = $tags['amenity'] ?? '';
            if (in_array($amenity, ['school', 'kindergarten', 'college', 'university'], true)) {
                $category = 'school';
            } elseif (in_array($amenity, ['hospital', 'clinic', 'doctors', 'dentist', 'health_post'], true)
                || in_array($tags['healthcare'] ?? '', ['hospital', 'clinic', 'doctor', 'dentist', 'health_post', 'centre'], true)) {
                $category = 'health';
            } elseif (($tags['office'] ?? '') === 'government' || ($tags['building'] ?? '') === 'government'
                || in_array($amenity, ['townhall', 'police', 'courthouse', 'fire_station'], true)) {
                $category = 'government';
            } else {
                continue;
            }
            $type = $element['type'] ?? '';
            if (! in_array($type, ['node', 'way', 'relation'], true) || ! is_numeric($element['id'] ?? null)) {
                continue;
            }
            $geometry = null;
            $label = 'Lokasi fasilitas (tapak belum tersedia)';
            if ($type === 'way' && count($element['geometry'] ?? []) >= 4) {
                $ring = [];
                foreach ($element['geometry'] as $point) {
                    if (! isset($point['lon'], $point['lat'])) { $ring = []; break; }
                    $ring[] = [(float) $point['lon'], (float) $point['lat']];
                }
                if (count($ring) >= 4 && $ring[0] === $ring[count($ring) - 1]) {
                    $geometry = ['type' => 'Polygon', 'coordinates' => [$ring]];
                    $label = isset($tags['building']) && $tags['building'] !== 'no' ? 'Tapak bangunan' : 'Area fasilitas (bukan tapak bangunan)';
                }
            }
            if (! $geometry) {
                $point = $type === 'node' ? $element : ($element['center'] ?? []);
                if (! isset($point['lon'], $point['lat'])) { continue; }
                $geometry = ['type' => 'Point', 'coordinates' => [(float) $point['lon'], (float) $point['lat']]];
            }
            $id = $type.'/'.$element['id'];
            $features[$id] = ['type' => 'Feature', 'id' => $id, 'geometry' => $geometry, 'properties' => [
                'name' => $tags['name'] ?? 'Fasilitas tanpa nama', 'category' => $category,
                'geometry_label' => $label, 'osm_url' => 'https://www.openstreetmap.org/'.$id,
            ]];
        }
        return ['type' => 'FeatureCollection', 'features' => array_values($features),
            'source' => 'OpenStreetMap contributors', 'updated_at' => now('Asia/Jakarta')->format('d/m/Y H:i').' WIB'];
    }
}
