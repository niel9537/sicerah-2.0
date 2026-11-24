<?php

namespace App\Services;

use App\Models\Indikator;
use App\Models\RpjmdIndicatorSource;
use Illuminate\Support\Collection;

class RpjmdHierarchy
{
    public function rows(): array
    {
        return json_decode(file_get_contents(resource_path('data/rpjmd-hierarchy.json')), true, 512, JSON_THROW_ON_ERROR);
    }

    public function indicators(): Collection
    {
        return Indikator::with('targets')->orderBy('nama')->orderBy('id')->get();
    }

    public function present(array $content, ?Collection $indicators = null): array
    {
        $indicators ??= $this->indicators();
        $byId = $indicators->keyBy('id');
        $byName = $indicators->groupBy(fn ($item) => $this->normalize($item->nama));
        $sources = RpjmdIndicatorSource::whereNull('perangkat_daerah')->orderBy('id')->get()->groupBy('indikator_id');
        $rows = [];
        foreach ($this->rows() as $row) {
            $key = (string) $row['row'];
            $manualId = $content['links'][$key] ?? null;
            $matches = collect([$row['nama'], ...($row['aliases'] ?? [])])
                ->flatMap(fn ($name) => $byName->get($this->normalize($name), collect()))
                ->unique('id');
            // Follow the existing importer's canonical choice: regional sources,
            // then Bank Data, then the oldest ID. Keep existing achievement IDs.
            $ranked = $matches->sortBy(fn ($item) => [
                $item->jenis === 'IKD' ? 0 : ($item->jenis === 'IKU' ? 1 : 2),
                $sources->has($item->id) ? 0 : 1,
                $item->bank_data_row !== null ? 0 : 1,
                $item->id,
            ]);
            $indicator = $manualId ? $byId->get($manualId) : $ranked->first();
            $row['indikator'] = $indicator;
            $row['ambiguous'] = false;
            $row['source_targets'] = $row['targets'];
            $row['active_targets'] = [];
            $row['target_origins'] = [];
            foreach (range(2025, 2030) as $year) {
                $master = $indicator?->targets->firstWhere('tahun', $year)?->target;
                $regional = $indicator ? $sources->get($indicator->id, collect())->map(fn ($source) => $source->targets[$year] ?? null)
                    ->filter(fn ($value) => $value !== null && $value !== '')->unique()->values() : collect();
                $row['active_targets'][$year] = $master !== null && $master !== ''
                    ? $master : ($regional->count() === 1 ? $regional->first() : null);
                $row['target_origins'][$year] = 'RPJMD · Tabel 3.4';
            }
            $rows[] = $row;
        }
        return $rows;
    }

    private function normalize(string $name): string
    {
        // Keep meaningful qualifiers (e.g. age ranges, sex, disease definition).
        $name = preg_replace('/\((?:HLS|IKP|IPA|IKG|IKUB|AKI|ADHB|tahun|angka|poin|indeks|nilai|level|opini|%|per\s*100[.,]?000\s*KH)\)/iu', ' ', $name);
        $name = mb_strtolower($name);
        return trim(preg_replace('/[^\pL\pN]+/u', ' ', $name));
    }
}
