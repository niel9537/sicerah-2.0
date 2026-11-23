<?php

namespace App\Services;

use App\Models\Indikator;
use App\Models\RpjmdIndicatorSource;
use Illuminate\Support\Collection;

class IndicatorDataService
{
    public function __construct(private readonly IndicatorValueParser $parser)
    {
    }

    /**
     * @param  array<int>  $years
     * @return array<string,mixed>
     */
    public function present(Indikator $indicator, array $years, int $selectedYear): array
    {
        $targets = $indicator->relationLoaded('targets') ? $indicator->targets : collect();
        $realisations = $indicator->relationLoaded('realisasis') ? $indicator->realisasis : collect();
        $points = [];

        foreach ($years as $year) {
            $targetModel = $targets->firstWhere('tahun', $year);
            $actualModel = $this->lastRealisation($realisations, $year);
            $target = $this->parser->parse($targetModel?->getRawOriginal('target'));
            $actual = $this->parser->parse($actualModel?->getRawOriginal('realisasi'));
            $hasActual = $actual['type'] !== 'empty';
            $hasTarget = $target['type'] !== 'empty';
            $chosen = $hasActual ? $actual : $target;

            $points[$year] = [
                'year' => $year,
                'source' => $hasActual ? 'capaian' : ($hasTarget ? 'target' : null),
                'raw' => $chosen['raw'],
                'type' => $chosen['type'],
                'graph' => $chosen['graph'],
                'min' => $chosen['min'],
                'max' => $chosen['max'],
                'target_raw' => $target['raw'],
                'target_graph' => $target['graph'],
                'actual_raw' => $actual['raw'],
                'actual_graph' => $actual['graph'],
                'updated_at' => collect([$targetModel?->updated_at, $actualModel?->updated_at, $indicator->updated_at])
                    ->filter()->sort()->last(),
            ];
        }

        $selected = $points[$selectedYear] ?? [
            'year' => $selectedYear,
            'source' => null,
            'raw' => null,
            'type' => 'empty',
            'graph' => null,
            'min' => null,
            'max' => null,
            'updated_at' => null,
        ];

        return [
            'id' => $indicator->id,
            'kode' => $indicator->kode,
            'nama' => $indicator->nama,
            'satuan' => $indicator->satuan,
            'aspek' => $indicator->aspek,
            'is_iku' => (bool) $indicator->is_iku,
            'is_makro' => (bool) $indicator->is_makro,
            'sumber_data' => $indicator->sumber_data,
            'keterangan' => $indicator->keterangan,
            'selected' => $selected,
            'points' => array_values($points),
            'has_chart' => collect($points)->contains(fn (array $point) => $point['graph'] !== null),
        ];
    }

    /**
     * Sajikan data indikator RPJMD (sumber resmi RPJMD 2025–2030) lengkap dengan
     * baseline 2024, target tahunan, dan capaian riil bila terhubung ke indikator aktif.
     *
     * @param  array<int>  $years
     * @return array<string,mixed>
     */
    public function presentRpjmd(RpjmdIndicatorSource $source, array $years, int $selectedYear): array
    {
        $indicator = $source->relationLoaded('indikator') ? $source->indikator : null;
        $realisations = $indicator?->relationLoaded('realisasis') ? $indicator->realisasis : collect();
        $sourceTargets = is_array($source->targets) ? $source->targets : [];

        $points = [];

        foreach ($years as $year) {
            if ($year === 2024) {
                // Baseline 2024 adalah rujukan dasar indikator RPJMD
                $baselineParsed = $this->parser->parse($source->baseline_2024);
                $hasBaseline = $baselineParsed['type'] !== 'empty';

                $points[$year] = [
                    'year' => $year,
                    'source' => $hasBaseline ? 'baseline' : null,
                    'raw' => $source->baseline_2024,
                    'type' => $baselineParsed['type'],
                    'graph' => $baselineParsed['graph'],
                    'min' => $baselineParsed['min'],
                    'max' => $baselineParsed['max'],
                    'target_raw' => null,
                    'target_graph' => null,
                    'actual_raw' => $source->baseline_2024,
                    'actual_graph' => $baselineParsed['graph'],
                    'updated_at' => $source->updated_at,
                ];
                continue;
            }

            // Target tahun 2025 s/d 2030 dari dokumen resmi RPJMD
            $targetRaw = $sourceTargets[(string) $year] ?? $sourceTargets[$year] ?? null;
            $targetParsed = $this->parser->parse($targetRaw);

            // Bila indikator sudah terhubung ke modul pelaporan aktif, periksa realisasi riilnya
            $actualModel = $this->lastRealisation($realisations, $year);
            $actualParsed = $this->parser->parse($actualModel?->getRawOriginal('realisasi'));

            $hasActual = $actualParsed['type'] !== 'empty';
            $hasTarget = $targetParsed['type'] !== 'empty';
            $chosen = $hasActual ? $actualParsed : $targetParsed;

            $points[$year] = [
                'year' => $year,
                'source' => $hasActual ? 'capaian' : ($hasTarget ? 'target' : null),
                'raw' => $chosen['raw'],
                'type' => $chosen['type'],
                'graph' => $chosen['graph'],
                'min' => $chosen['min'],
                'max' => $chosen['max'],
                'target_raw' => $targetParsed['raw'],
                'target_graph' => $targetParsed['graph'],
                'actual_raw' => $actualParsed['raw'],
                'actual_graph' => $actualParsed['graph'],
                'updated_at' => collect([$actualModel?->updated_at, $source->updated_at])
                    ->filter()->sort()->last(),
            ];
        }

        $selected = $points[$selectedYear] ?? [
            'year' => $selectedYear,
            'source' => null,
            'raw' => null,
            'type' => 'empty',
            'graph' => null,
            'min' => null,
            'max' => null,
            'updated_at' => null,
        ];

        return [
            'id' => $source->id,
            'indikator_id' => $source->indikator_id,
            'kode' => $source->jenis . '-' . str_pad((string) $source->id, 4, '0', STR_PAD_LEFT),
            'nama' => $source->nama,
            'jenis' => $source->jenis,
            'satuan' => $source->satuan,
            'arah' => $source->arah,
            'kelompok' => $source->kelompok,
            'aspek' => $source->kelompok,
            'perangkat_daerah' => $source->perangkat_daerah,
            'lingkup' => $source->perangkat_daerah ? 'opd' : 'daerah',
            'baseline_2024' => $source->baseline_2024,
            'is_iku' => $source->jenis === 'IKU',
            'is_makro' => $source->jenis === 'IKU' || ($source->jenis === 'IKD' && $source->perangkat_daerah === null),
            'is_ikd' => $source->jenis === 'IKD',
            'is_ikk' => $source->jenis === 'IKK',
            'sumber_data' => $source->file . ($source->baris ? " (Baris {$source->baris})" : ''),
            'keterangan' => $source->keterangan,
            'file' => $source->file,
            'baris' => $source->baris,
            'targets' => $sourceTargets,
            'selected' => $selected,
            'points' => array_values($points),
            'has_chart' => collect($points)->contains(fn (array $point) => $point['graph'] !== null),
        ];
    }

    private function lastRealisation(Collection $realisations, int $year): mixed
    {
        return $realisations
            ->where('tahun', $year)
            ->filter(fn ($item) => $this->parser->parse($item->getRawOriginal('realisasi'))['type'] !== 'empty')
            ->sortByDesc('triwulan')
            ->first();
    }
}
