@extends('layouts.app')
@section('title','Kelola WebGIS')
@section('content')
@include('webgis._style')
<div class="wg"><h1>Kelola peta tematik</h1><p>Tambahkan indikator dan atur publikasinya. Nilai disimpan per desa dan tahun.</p>
<a class="wg-button" href="{{ route('webgis.education.index') }}">Kelola pendidikan per kecamatan</a><div class="wg-card wg-scroll"><table><thead><tr><th>Tema</th><th>Satuan</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
@foreach($themes as $item)<tr><td>{{ $item->name }}</td><td>{{ $item->unit }}</td><td>{{ $item->is_public ? 'Publik' : 'Draf' }}</td><td><a href="{{ route('webgis.admin.edit',$item) }}">Kelola</a></td></tr>@endforeach
</tbody></table></div>
<div class="wg-card"><h2>Tambah tema</h2><form method="POST" action="{{ route('webgis.admin.store') }}" enctype="multipart/form-data">@csrf @include('webgis.admin._form') </form></div></div>
@endsection