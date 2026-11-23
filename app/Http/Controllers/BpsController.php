<?php

namespace App\Http\Controllers;

use App\Services\BpsService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class BpsController extends Controller
{
    public function index(Request $request, BpsService $bps)
    {
        $validated = $request->validate([
            'kategori' => ['nullable', Rule::in(array_keys(BpsService::SECTIONS))],
            'page' => ['nullable', 'integer', 'min:1', 'max:2000'],
            'q' => ['nullable', 'string', 'max:150'],
            'tahun' => ['nullable', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
        ]);
        $category = $validated['kategori'] ?? 'statictable';
        $keyword = trim($validated['q'] ?? '');
        $year = !empty($validated['tahun']) ? (int) $validated['tahun'] : null;
        $error = null; $result = null;
        try {
            $result = $bps->listing($category, (int) ($validated['page'] ?? 1), $keyword, $year);
        } catch (RuntimeException $exception) {
            $error = $exception->getMessage();
        }
        return response()->view('bps.index', compact('category', 'keyword', 'year', 'error', 'result'), $error ? 503 : 200);
    }

    public function table(int $id, BpsService $bps)
    {
        $error = null; $table = null;
        try { $table = $bps->table($id); }
        catch (RuntimeException $exception) { $error = $exception->getMessage(); }
        if (!$error && !$table) abort(404);
        return response()->view('bps.table', compact('table', 'error'), $error ? 503 : 200);
    }
}
