<?php

namespace App\Http\Controllers;

use App\Models\GisArea;
use App\Models\GisLayer;
use App\Models\GisValue;
use App\Services\IndicatorValueParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GisLayerController extends Controller
{
    public function index(): View
    {
        $layers = GisLayer::withCount('values')->orderBy('sort_order')->orderBy('name')->get();

        return view('webgis.admin', compact('layers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['geojson_path'] = $this->storeGeoJson($request) ?? 'data/gis/nias-barat-desa.geojson';
        $data['breakpoints'] = $this->breakpoints($request->input('breakpoints_text'));
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_public'] = $request->boolean('is_public');
        GisLayer::create($data);

        return back()->with('success', 'Layer tematik berhasil ditambahkan.');
    }

    public function update(Request $request, GisLayer $gisLayer): RedirectResponse
    {
        $data = $this->validated($request, $gisLayer);
        $data['slug'] = Str::slug($request->input('slug') ?: $gisLayer->slug) ?: $gisLayer->slug;
        $data['breakpoints'] = $this->breakpoints($request->input('breakpoints_text'));
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_public'] = $request->boolean('is_public');
        if ($path = $this->storeGeoJson($request)) {
            $data['geojson_path'] = $path;
        }
        $gisLayer->update($data);

        return back()->with('success', 'Pengaturan layer berhasil diperbarui.');
    }

    public function destroy(GisLayer $gisLayer): RedirectResponse
    {
        $gisLayer->delete();

        return back()->with('success', 'Layer tematik dan seluruh nilainya telah dihapus.');
    }

    public function importValues(Request $request, GisLayer $gisLayer, IndicatorValueParser $parser): RedirectResponse
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:10240']);
        $rows = IOFactory::load($request->file('file')->getRealPath())->getActiveSheet()->toArray(null, true, true, false);
        if (! $rows) {
            return back()->with('error', 'File data layer kosong.');
        }

        $headers = collect(array_shift($rows))->map(fn ($value) => Str::of((string) $value)->lower()->trim()->toString());
        $codeColumn = $headers->search(fn ($value) => in_array($value, ['kode_desa', 'kode wilayah', 'kode'], true));
        $yearColumn = $headers->search(fn ($value) => in_array($value, ['tahun', 'year'], true));
        $valueColumn = $headers->search(fn ($value) => in_array($value, ['nilai', 'value', 'capaian'], true));

        if ($codeColumn === false || $yearColumn === false || $valueColumn === false) {
            return back()->with('error', 'Header wajib: kode_desa, tahun, nilai.');
        }

        $knownAreas = GisArea::pluck('code')->flip();
        $saved = 0;
        $skipped = 0;

        DB::transaction(function () use ($rows, $codeColumn, $yearColumn, $valueColumn, $knownAreas, $gisLayer, $parser, &$saved, &$skipped): void {
            foreach ($rows as $row) {
                $code = trim((string) ($row[$codeColumn] ?? ''));
                $year = (int) ($row[$yearColumn] ?? 0);
                $raw = trim((string) ($row[$valueColumn] ?? ''));

                if ($code === '' && $year === 0 && $raw === '') {
                    continue;
                }
                if (! $knownAreas->has($code) || $year < 2000 || $year > 2100) {
                    $skipped++;
                    continue;
                }

                $parsed = $parser->parse($raw);
                GisValue::updateOrCreate(
                    ['gis_layer_id' => $gisLayer->id, 'area_code' => $code, 'year' => $year],
                    [
                        'raw_value' => Str::limit($raw, 100, ''),
                        'value_type' => $parsed['type'],
                        'numeric_value' => $parsed['graph'],
                        'value_min' => $parsed['min'],
                        'value_max' => $parsed['max'],
                    ]
                );
                $saved++;
            }
        });

        return back()->with('success', "{$saved} nilai tersimpan. {$skipped} baris dilewati karena kode desa atau tahun tidak valid.");
    }

    /** @return array<string,mixed> */
    private function validated(Request $request, ?GisLayer $layer = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:150',
            'slug' => ['nullable', 'string', 'max:160', Rule::unique('gis_layers', 'slug')->ignore($layer?->id)],
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'color_from' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_to' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sort_order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
            'geojson' => 'nullable|file|max:20480',
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'layer';
        $slug = $base;
        $suffix = 2;
        while (GisLayer::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    /** @return array<float>|null */
    private function breakpoints(?string $text): ?array
    {
        $values = collect(explode(',', (string) $text))
            ->map(fn ($value) => trim($value))
            ->filter(fn ($value) => $value !== '' && is_numeric($value))
            ->map(fn ($value) => (float) $value)
            ->sort()
            ->values()
            ->all();

        return $values ?: null;
    }

    private function storeGeoJson(Request $request): ?string
    {
        $file = $request->file('geojson');
        if (! $file) {
            return null;
        }

        $decoded = json_decode((string) file_get_contents($file->getRealPath()), true);
        if (! is_array($decoded) || ($decoded['type'] ?? null) !== 'FeatureCollection') {
            abort(422, 'GeoJSON harus berupa FeatureCollection yang valid.');
        }

        $name = now()->format('YmdHis').'-'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.geojson';
        $file->move(public_path('data/gis'), $name);

        return 'data/gis/'.$name;
    }
}
