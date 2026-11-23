@extends('layouts.app')

@section('title', 'Form 81 — Evaluasi Renja')
@section('subtitle', 'Preview & Export Laporan Evaluasi Hasil Renja SKPD')

@section('content')
    <x-ui.breadcrumb :items="['Form 81' => '']" />

    {{-- Panel filter --}}
    <x-ui.card class="mb-4">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <select name="tahun" class="select">
                @foreach(range(now()->year + 1, now()->year - 3) as $y)
                    <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                @endforeach
            </select>

            @if(auth()->user()->role !== 'operator_opd')
                <select name="opd_id" class="select">
                    <option value="">— Seluruh SKPD —</option>
                    @foreach($opdList as $o)
                        <option value="{{ $o->id }}" @selected($opdId == $o->id)>{{ $o->nama }}</option>
                    @endforeach
                </select>
            @endif

            <button type="submit" class="btn-secondary justify-center">Terapkan Filter</button>

            <div class="flex gap-2">
                <a href="{{ route('form81.excel', request()->query()) }}" class="btn-success btn flex-1 justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </a>
                <a href="{{ route('form81.pdf', request()->query()) }}" class="btn-danger btn flex-1 justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    PDF
                </a>
            </div>
        </form>
    </x-ui.card>

    {{-- Preview --}}
    <x-ui.card>
        <div class="text-center mb-6">
            <h2 class="text-lg font-bold text-slate-800 uppercase">Evaluasi Terhadap Hasil Rencana Kerja SKPD</h2>
            <p class="text-sm text-slate-600">Pemerintah Kabupaten Nias Barat — Tahun {{ $data['header']['tahun'] }}</p>
            <p class="text-sm font-semibold mt-1">SKPD: {{ strtoupper($data['header']['opd']) }}</p>
        </div>

        <div class="overflow-x-auto -mx-6 sm:mx-0">
            <table class="min-w-full text-[10px] border-collapse">
                <thead class="bg-slate-100">
                    <tr>
                        <th rowspan="2" class="border border-slate-300 px-1 py-2">No</th>
                        <th rowspan="2" class="border border-slate-300 px-1 py-2">Kode</th>
                        <th rowspan="2" class="border border-slate-300 px-2 py-2 min-w-[240px] text-left">Urusan / Program / Kegiatan / Sub Kegiatan</th>
                        <th rowspan="2" class="border border-slate-300 px-1 py-2 min-w-[120px]">Indikator</th>
                        <th rowspan="2" class="border border-slate-300 px-1 py-2">Sat.</th>
                        <th colspan="2" class="border border-slate-300 px-1 py-2">Target Renja {{ $data['header']['tahun'] }}</th>
                        <th colspan="2" class="border border-slate-300 px-1 py-2">TW I</th>
                        <th colspan="2" class="border border-slate-300 px-1 py-2">TW II</th>
                        <th colspan="2" class="border border-slate-300 px-1 py-2">TW III</th>
                        <th colspan="2" class="border border-slate-300 px-1 py-2">TW IV</th>
                        <th colspan="2" class="border border-slate-300 px-1 py-2">Realisasi s/d Tahun Berjalan</th>
                        <th rowspan="2" class="border border-slate-300 px-1 py-2">Capaian %</th>
                        <th rowspan="2" class="border border-slate-300 px-1 py-2 min-w-[140px]">Unit SKPD</th>
                    </tr>
                    <tr>
                        @foreach(['K','Rp','K','Rp','K','Rp','K','Rp','K','Rp','K','Rp'] as $h)
                            <th class="border border-slate-300 px-1 py-1">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['rows'] as $r)
                        @php
                            $rowClass = match($r['level']) {
                                'urusan'   => 'bg-maroon-900 text-white font-bold',
                                'program'  => 'bg-maroon-600 text-white font-semibold',
                                'kegiatan' => 'bg-maroon-100 font-semibold',
                                default    => '',
                            };
                            $indent = ['urusan' => '', 'program' => 'pl-4', 'kegiatan' => 'pl-8', 'subkegiatan' => 'pl-12'][$r['level']];
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="border border-slate-300 px-1 py-1 text-center">{{ $r['level'] === 'subkegiatan' ? $r['no'] : '' }}</td>
                            <td class="border border-slate-300 px-1 py-1 font-mono text-[9px]">{{ $r['kode'] }}</td>
                            <td class="border border-slate-300 px-2 py-1 {{ $indent }}">{{ $r['uraian'] }}</td>
                            <td class="border border-slate-300 px-1 py-1">{{ $r['indikator'] }}</td>
                            <td class="border border-slate-300 px-1 py-1 text-center">{{ $r['satuan'] }}</td>
                            <td class="border border-slate-300 px-1 py-1 text-right">{{ $r['target_k'] !== null ? number_format($r['target_k'], 2, ',', '.') : '' }}</td>
                            <td class="border border-slate-300 px-1 py-1 text-right">{{ $r['target_rp'] !== null ? number_format($r['target_rp'], 0, ',', '.') : '' }}</td>
                            @foreach([1,2,3,4] as $tw)
                                <td class="border border-slate-300 px-1 py-1 text-right">{{ $r["tw_{$tw}_k"] !== null ? number_format($r["tw_{$tw}_k"], 2, ',', '.') : '' }}</td>
                                <td class="border border-slate-300 px-1 py-1 text-right">{{ $r["tw_{$tw}_rp"] !== null ? number_format($r["tw_{$tw}_rp"], 0, ',', '.') : '' }}</td>
                            @endforeach
                            <td class="border border-slate-300 px-1 py-1 text-right">{{ $r['real_k'] !== null ? number_format($r['real_k'], 2, ',', '.') : '' }}</td>
                            <td class="border border-slate-300 px-1 py-1 text-right">{{ $r['real_rp'] !== null ? number_format($r['real_rp'], 0, ',', '.') : '' }}</td>
                            <td class="border border-slate-300 px-1 py-1 text-center">{{ $r['pct_k'] !== null ? number_format($r['pct_k'], 1) . '%' : '' }}</td>
                            <td class="border border-slate-300 px-1 py-1 text-[9px]">{{ $r['opd'] }}</td>
                        </tr>
                    @endforeach

                    {{-- Footer total --}}
                    <tr class="bg-maroon-50 font-bold">
                        <td colspan="6" class="border border-slate-300 px-2 py-2 text-right">TOTAL</td>
                        <td class="border border-slate-300 px-1 py-2 text-right">{{ number_format($data['footer']['total_target_rp'], 0, ',', '.') }}</td>
                        <td colspan="8" class="border border-slate-300"></td>
                        <td class="border border-slate-300"></td>
                        <td class="border border-slate-300 px-1 py-2 text-right">{{ number_format($data['footer']['total_real_rp'], 0, ',', '.') }}</td>
                        <td class="border border-slate-300 px-1 py-2 text-center">{{ number_format($data['footer']['rata_pct_rp'], 1) }}%</td>
                        <td class="border border-slate-300"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <div class="text-center text-sm">
                <p>Nias Barat, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="mt-1">Kepala SKPD</p>
                <p class="mt-16">(_______________________)</p>
                <p>NIP.</p>
            </div>
        </div>

        <div class="mt-4 text-xs text-slate-500 grid grid-cols-1 sm:grid-cols-4 gap-2 pt-4 border-t border-slate-100">
            <div>Jumlah Sub Kegiatan: <strong>{{ $data['footer']['jumlah_sub'] }}</strong></div>
            <div>Rata-rata Capaian Fisik: <strong>{{ number_format($data['footer']['rata_pct_k'] ?? 0, 1) }}%</strong></div>
            <div>Rata-rata Capaian Keuangan: <strong>{{ number_format($data['footer']['rata_pct_rp'] ?? 0, 1) }}%</strong></div>
            <div>Total Pagu: <strong>Rp {{ number_format($data['footer']['total_target_rp'], 0, ',', '.') }}</strong></div>
        </div>
    </x-ui.card>
@endsection