@extends('layouts.app')
@section('title','Pengaturan peta tematik')
@section('content')
@include('webgis._style')
<div class="wg"><a href="{{ route('webgis.admin.index') }}">← Daftar tema</a><h1>{{ $theme->name }}</h1>
<div class="wg-card"><h2>Pengaturan tema</h2><form method="POST" action="{{ route('webgis.admin.update',$theme) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('webgis.admin._form')</form></div>
<div class="wg-card"><h2>Tambah / perbarui nilai</h2><p>Pilih kode desa dan tahun yang sama untuk mengganti nilai sebelumnya. Nilai kosong bukan nol.</p>
<form method="POST" action="{{ route('webgis.admin.value',$theme) }}">@csrf
<div class="wg-grid"><label>Desa<select name="kode_desa" required>@foreach($desa as $d)<option value="{{ $d->kode_desa }}" @selected(old('kode_desa')===$d->kode_desa)>{{ $d->nama_desa }} — {{ $d->nama_kecamatan }} ({{ $d->kode_desa }})</option>@endforeach</select></label>
<label>Tahun<input type="number" name="year" min="1900" max="2200" value="{{ old('year',date('Y')) }}" required></label><label>Nilai ({{ $theme->unit }})<input name="value" type="number" step="0.0001" value="{{ old('value') }}" required></label><label>Catatan internal<input name="note" maxlength="500" value="{{ old('note') }}"></label></div><button class="wg-button">Simpan nilai</button></form></div>
<div class="wg-card"><h2>Impor CSV</h2><p>Header: <code>kode_desa,year,value,note</code>. Pemisah koma atau titik koma; desimal memakai titik. Baris dengan kode/tahun sama akan diperbarui. Satu baris tidak valid membatalkan seluruh impor.</p><form method="POST" enctype="multipart/form-data" action="{{ route('webgis.admin.csv',$theme) }}">@csrf<input type="file" name="csv" accept=".csv" required><button class="wg-button">Impor CSV</button></form></div>
<div class="wg-card wg-scroll"><h2>Nilai tersimpan</h2><table><thead><tr><th>Kode desa</th><th>Tahun</th><th>Nilai</th><th>Catatan</th><th>Aksi</th></tr></thead><tbody>
@foreach($values as $v)<tr><td>{{ $v->kode_desa }}</td><td>{{ $v->year }}</td><td>{{ $v->value }}</td><td>{{ $v->note }}</td><td><form method="POST" action="{{ route('webgis.admin.deleteValue',[$theme,$v->id]) }}" onsubmit="return confirm('Hapus nilai ini?')">@csrf @method('DELETE')<button>Hapus</button></form></td></tr>@endforeach</tbody></table>{{ $values->links() }}</div>
<form method="POST" action="{{ route('webgis.admin.destroy',$theme) }}" onsubmit="return confirm('Hapus tema beserta SEMUA nilainya? Tindakan ini tidak bisa dibatalkan.')">@csrf @method('DELETE')<button class="wg-button wg-danger">Hapus tema dan seluruh nilai</button></form></div>
@endsection