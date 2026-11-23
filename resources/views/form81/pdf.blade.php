<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form 81 — {{ $data['header']['opd'] }}</title>
    <style>
        @page { margin: 15mm 10mm; }
        body { font-family: Inter, DejaVu Sans, sans-serif; font-size: 8px; color: #1e293b; font-style: normal; }
        em, i { font-style: normal; }
        h1 { font-size: 12px; margin: 0; text-align: center; }
        h2 { font-size: 10px; margin: 2px 0; text-align: center; font-weight: normal; }
        h3 { font-size: 10px; margin: 4px 0 10px; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 0.5px solid #64748b; padding: 3px 4px; vertical-align: middle; }
        thead { background: #E8E8E8; }
        th { font-weight: bold; text-align: center; font-size: 7px; }
        .num { text-align: right; }
        .center { text-align: center; }
        .row-urusan   { background: #761D2E; color: #fff; font-weight: bold; }
        .row-program  { background: #C72C3D; color: #fff; font-weight: bold; }
        .row-kegiatan { background: #F2A7AD; font-weight: bold; }
        .row-total    { background: #FDF3F4; font-weight: bold; }
        .pl-p  { padding-left: 8px; }
        .pl-k  { padding-left: 16px; }
        .pl-sk { padding-left: 24px; }
        .sign { margin-top: 20px; width: 100%; }
        .sign td { border: 0; text-align: center; }
    </style>
</head>
<body>
    <h1>EVALUASI TERHADAP HASIL RENCANA KERJA SKPD</h1>
    <h2>PEMERINTAH KABUPATEN NIAS BARAT — TAHUN {{ $data['header']['tahun'] }}</h2>
    <h3>SKPD: {{ strtoupper($data['header']['opd']) }}</h3>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width:3%">No</th>
                <th rowspan="2" style="width:7%">Kode</th>
                <th rowspan="2" style="width:22%">Urusan / Program / Kegiatan / Sub Kegiatan</th>
                <th rowspan="2" style="width:11%">Indikator</th>
                <th rowspan="2" style="width:4%">Sat.</th>
                <th colspan="2">Target Renja</th>
                <th colspan="2">TW I</th>
                <th colspan="2">TW II</th>
                <th colspan="2">TW III</th>
                <th colspan="2">TW IV</th>
                <th colspan="2">Realisasi s/d</th>
                <th rowspan="2" style="width:5%">Cap %</th>
                <th rowspan="2" style="width:10%">Unit SKPD</th>
            </tr>
            <tr>
                @foreach(['K','Rp','K','Rp','K','Rp','K','Rp','K','Rp','K','Rp'] as $h)
                    <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data['rows'] as $r)
                @php
                    $cls = 'row-'.$r['level'];
                    $pl  = ['urusan'=>'', 'program'=>'pl-p', 'kegiatan'=>'pl-k', 'subkegiatan'=>'pl-sk'][$r['level']];
                @endphp
                <tr class="{{ $r['level'] !== 'subkegiatan' ? $cls : '' }}">
                    <td class="center">{{ $r['level'] === 'subkegiatan' ? $r['no'] : '' }}</td>
                    <td>{{ $r['kode'] }}</td>
                    <td class="{{ $pl }}">{{ $r['uraian'] }}</td>
                    <td>{{ $r['indikator'] }}</td>
                    <td class="center">{{ $r['satuan'] }}</td>
                    <td class="num">{{ $r['target_k'] !== null ? number_format($r['target_k'], 2, ',', '.') : '' }}</td>
                    <td class="num">{{ $r['target_rp'] !== null ? number_format($r['target_rp'], 0, ',', '.') : '' }}</td>
                    @foreach([1,2,3,4] as $tw)
                        <td class="num">{{ $r["tw_{$tw}_k"] !== null ? number_format($r["tw_{$tw}_k"], 2, ',', '.') : '' }}</td>
                        <td class="num">{{ $r["tw_{$tw}_rp"] !== null ? number_format($r["tw_{$tw}_rp"], 0, ',', '.') : '' }}</td>
                    @endforeach
                    <td class="num">{{ $r['real_k'] !== null ? number_format($r['real_k'], 2, ',', '.') : '' }}</td>
                    <td class="num">{{ $r['real_rp'] !== null ? number_format($r['real_rp'], 0, ',', '.') : '' }}</td>
                    <td class="center">{{ $r['pct_k'] !== null ? number_format($r['pct_k'], 1) . '%' : '' }}</td>
                    <td>{{ $r['opd'] }}</td>
                </tr>
            @endforeach

            <tr class="row-total">
                <td colspan="6" class="num"><strong>TOTAL</strong></td>
                <td class="num">{{ number_format($data['footer']['total_target_rp'], 0, ',', '.') }}</td>
                <td colspan="8"></td>
                <td></td>
                <td class="num">{{ number_format($data['footer']['total_real_rp'], 0, ',', '.') }}</td>
                <td class="center">{{ number_format($data['footer']['rata_pct_rp'] ?? 0, 1) }}%</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table class="sign">
        <tr>
            <td style="width:70%"></td>
            <td>
                <p>Nias Barat, {{ now()->translatedFormat('d F Y') }}</p>
                <p style="margin-top:2px">Kepala SKPD</p>
                <br><br><br>
                <p><strong>(_______________________)</strong></p>
                <p>NIP.</p>
            </td>
        </tr>
    </table>
</body>
</html>
