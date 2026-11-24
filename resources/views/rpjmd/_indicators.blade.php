@if($rows->isNotEmpty())
<div class="border-t border-slate-200 p-5 sm:p-6">
    @foreach($rows->groupBy('sasaran') as $sasaran => $items)
    <details class="mb-4 rounded-xl border border-slate-200 bg-white">
        <summary class="cursor-pointer p-4 text-base font-bold text-slate-800">{{ $sasaran ?: 'Indikator tujuan' }} <span class="text-sm font-medium text-slate-500">({{ $items->count() }} indikator)</span></summary>
        <div class="overflow-x-auto rounded-b-xl" role="region" aria-label="Indikator dan target tahunan" tabindex="0">
            <table class="w-full text-sm text-left border-collapse">
                <thead class="bg-slate-100 text-slate-700"><tr><th class="p-4 min-w-72">Indikator / satuan</th>@foreach(range(2025,2030) as $year)<th class="p-4 whitespace-nowrap">Target {{ $year }}</th>@endforeach</tr></thead>
                <tbody>
                    @foreach($items as $row)
                    <tr class="border-t border-slate-100 align-top">
                        <td class="p-4"><p class="font-semibold text-slate-800">{{ $row['nama'] }}</p>@if(!empty($row['note']))<p class="mt-1 text-xs text-slate-500">{{ $row['note'] }}</p>@endif
                            @if($row['indikator'])
                            <p class="mt-1 text-slate-500">{{ $row['indikator']->jenis }} · {{ $row['indikator']->satuan }}</p>
                            <a class="block mt-2 text-red-800 underline" href="{{ route('publik.rpjmd.indikator', $row['indikator']->id) }}">Lihat data indikator</a>
                            @if(auth()->check() && in_array(auth()->user()->role, ['super_admin','admin_bappeda']))<a class="block mt-2 text-red-800 underline" href="{{ route('indikator.kelola', $row['indikator']->id) }}">Kelola target dan capaian</a>@endif
                            @else<p class="mt-2 text-slate-500">{{ $row['ambiguous'] ? 'Perlu pemilihan indikator di CMS' : 'Belum terhubung ke tabel indikator' }}</p>@endif
                        </td>
                        @foreach(range(2025,2030) as $year)
                        <td class="p-4 whitespace-nowrap"><p class="font-bold text-slate-800">{{ $row['targets'][$year] !== '' ? $row['targets'][$year] : '—' }}</p><p class="mt-1 text-xs text-slate-500">{{ $row['target_origins'][$year] }}</p>@if($row['active_targets'][$year] !== null && (string)$row['active_targets'][$year] !== (string)$row['targets'][$year])<p class="mt-2 text-xs text-slate-500">Target indikator aktif: {{ $row['active_targets'][$year] }}</p>@endif</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </details>
    @endforeach
</div>
@endif
