<?php

namespace App\Http\Controllers;

use App\Services\WebgisFacilities;
use Illuminate\Support\Facades\Cache;

class WebgisFacilityController extends Controller
{
    public function __invoke(WebgisFacilities $facilities)
    {
        $cache = Cache::store('file');
        if ($data = $cache->get('webgis.facilities.v1')) {
            return response()->json($data);
        }
        if ($cache->has('webgis.facilities.unavailable')) {
            return response()->json(['message' => 'Data fasilitas sementara tidak tersedia.'], 503);
        }
        $lock = $cache->lock('webgis.facilities.loading', 45);
        if (! $lock->get()) {
            return response()->json(['message' => 'Data fasilitas sedang dimuat.'], 503);
        }
        try {
            $data = $facilities->fetch();
            $cache->put('webgis.facilities.v1', $data, now()->addDay());
            return response()->json($data);
        } catch (\Throwable $e) {
            report($e);
            $cache->put('webgis.facilities.unavailable', true, 60);
            return response()->json(['message' => 'Data fasilitas sementara tidak tersedia.'], 503);
        } finally {
            $lock->release();
        }
    }
}
