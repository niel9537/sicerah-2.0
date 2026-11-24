<?php

namespace App\Http\Controllers;

use App\Services\KonvergensiService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class KonvergensiController extends Controller
{
    public function summary(KonvergensiService $service)
    {
        try {
            $data = $service->dashboard(max(array_keys(KonvergensiService::PERIODS)));
            return response()->json([
                'year' => $data['year'], 'progress' => $data['district']['cells'][22],
                'villages' => $data['district']['cells'][1], 'subdistricts' => count($data['subdistricts']),
                'is_fallback' => $data['is_fallback'],
                'retrieved_at' => \Carbon\Carbon::parse($data['retrieved_at'])->timezone('Asia/Jakarta')->format('d/m/Y H:i').' WIB',
            ]);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => 'Ringkasan konvergensi belum dapat dimuat.'], 503);
        }
    }

    public function index(Request $request, KonvergensiService $service)
    {
        $validated = $request->validate(['tahun' => ['nullable', 'integer', Rule::in(array_keys(KonvergensiService::PERIODS))]]);
        $year = (int) ($validated['tahun'] ?? 2026);
        $data = null;
        $error = null;
        try { $data = $service->dashboard($year); }
        catch (RuntimeException $exception) { $error = $exception->getMessage(); }
        return response()->view('konvergensi.index', compact('year', 'data', 'error'), $error ? 503 : 200);
    }
}
