<?php

namespace App\Http\Controllers;

use App\Services\KonvergensiService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class KonvergensiController extends Controller
{
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
