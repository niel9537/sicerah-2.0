@extends('layouts.app')
@section('title','Pendidikan per Kecamatan')
@section('content')
@include('webgis._style')
<div class="wg"><h1>Pendidikan per Kecamatan</h1><p>Agregat pendidikan Kabupaten Nias Barat. PD: peserta didik; GTK: guru dan tenaga kependidikan. Persentase dihitung dari sekolah yang sudah sinkronisasi.</p>
<a href="{{ route('webgis.dashboard') }}">Lihat peta</a> ? <a href="https://dapo.kemendikdasmen.go.id/progres/070000/072700" target="_blank" rel="noopener">Sumber Dapodik</a>
@if(session('success'))<div class="wg-status" role="status">{{ session('success') }}</div>@endif
@if($errors->any())<div class="wg-status" role="alert">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
<div class="wg-card"><h2>{{ $editing ? 'Ubah data kecamatan' : 'Tambah data kecamatan' }}</h2>
<form method="POST" action="{{ $editing ? route('webgis.education.update',$editing->id) : route('webgis.education.store') }}">@csrf @if($editing) @method('PUT') @endif
<div class="wg-grid"><label>Kecamatan<select name="kode_kecamatan" required>@foreach($districts as $d)<option value="{{ $d->kode_kecamatan }}" @selected(old('kode_kecamatan',$editing?->kode_kecamatan)===$d->kode_kecamatan)>{{ $d->nama_kecamatan }}</option>@endforeach</select></label>
@foreach(['schools'=>'Jumlah sekolah','students'=>'Jumlah peserta didik (PD)','teachers'=>'Jumlah GTK','synced'=>'Sudah sinkronisasi','unsynced'=>'Belum sinkronisasi'] as $key=>$label)
<label>{{ $label }}<input type="number" name="{{ $key }}" min="0" max="100000000" step="1" value="{{ old($key,$editing?->$key) }}" required></label>@endforeach
<label>Sumber<input name="source" maxlength="255" value="{{ old('source',$editing?->source ?? 'Dapodik Kab. Nias Barat') }}" required></label></div>
<button class="wg-button">{{ $editing ? 'Simpan perubahan' : 'Tambah' }}</button>@if($editing)<a href="{{ route('webgis.education.index') }}">Batal</a>@endif</form></div>
<div class="wg-card"><h2>Impor CSV Dapodik</h2><p>Unggah ekspor progres dengan header: No., Kecamatan, Jumlah Sekolah, Jumlah PD, Jumlah GTK, Sudah Sinkronisasi, Belum Sinkronisasi, Persentase (%). Data kecamatan yang sama akan diperbarui. CSV tidak memuat periode; data ditandai sebagai snapshot dengan waktu pembaruan, bukan data tahunan atau realtime.</p>
<form method="POST" action="{{ route('webgis.education.csv') }}" enctype="multipart/form-data">@csrf<label>File CSV<input type="file" name="csv" accept=".csv,text/csv" required></label><button class="wg-button">Impor CSV</button></form></div>
<div class="wg-card wg-scroll"><table><thead><tr><th>Kecamatan</th><th>Sekolah</th><th>PD</th><th>GTK</th><th>Sudah sinkron</th><th>Belum sinkron</th><th>%</th><th>Sumber / diperbarui</th><th>Aksi</th></tr></thead><tbody>
@foreach($districts as $d) @php($row=$rows->get($d->kode_kecamatan))
<tr><td>{{ $d->nama_kecamatan }}</td>@if($row)
@foreach(['schools','students','teachers','synced','unsynced'] as $key)<td>{{ number_format($row->$key,0,',','.') }}</td>@endforeach
<td>{{ $row->schools ? number_format(100*$row->synced/$row->schools,1,',','.') . '%' : '?' }}</td><td>{{ $row->source }}<br>{{ $row->updated_at }}</td><td><a href="{{ route('webgis.education.index',['edit'=>$row->id]) }}">Ubah</a><form method="POST" action="{{ route('webgis.education.destroy',$row->id) }}" onsubmit="return confirm('Hapus data pendidikan kecamatan ini?')">@csrf @method('DELETE')<button class="wg-button wg-danger">Hapus</button></form></td>
@else<td colspan="8">Belum ada data</td>@endif</tr>@endforeach
</tbody></table></div></div>
@endsection
