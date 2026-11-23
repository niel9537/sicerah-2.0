@extends('layouts.app')

@section('title', 'Manajemen WebGIS')
@section('subtitle', 'Layer tematik, tampilan, dan data per desa')

@section('content')
    <x-ui.breadcrumb :items="['WebGIS' => route('webgis.index'), 'Manajemen Layer' => '']" />

    <div class="grid xl:grid-cols-[380px_1fr] gap-5 items-start">
        <x-ui.card title="Tambah Layer Tematik" subtitle="Gunakan batas desa bawaan atau unggah GeoJSON baru" class="xl:sticky xl:top-24">
            <form method="POST" action="{{ route('webgis.admin.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div><label class="block text-xs font-medium text-slate-600 mb-1">Nama layer</label><input name="name" required class="input" placeholder="Contoh: Kemiskinan Ekstrem"></div>
                <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-slate-600 mb-1">Satuan</label><input name="unit" class="input" placeholder="Persen"></div><div><label class="block text-xs font-medium text-slate-600 mb-1">Urutan</label><input type="number" name="sort_order" min="0" value="0" class="input"></div></div>
                <div><label class="block text-xs font-medium text-slate-600 mb-1">Deskripsi</label><textarea name="description" rows="2" class="textarea"></textarea></div>
                <div class="grid grid-cols-2 gap-3"><label class="text-xs text-slate-600">Warna rendah<input type="color" name="color_from" value="#fee2e2" class="mt-1 block h-10 w-full rounded-lg border border-slate-200"></label><label class="text-xs text-slate-600">Warna tinggi<input type="color" name="color_to" value="#68121D" class="mt-1 block h-10 w-full rounded-lg border border-slate-200"></label></div>
                <div><label class="block text-xs font-medium text-slate-600 mb-1">Batas klasifikasi</label><input name="breakpoints_text" class="input" placeholder="5, 10, 15, 20, 30"><p class="text-[11px] text-slate-400 mt-1">Pisahkan dengan koma. Kosongkan untuk skala otomatis.</p></div>
                <div><label class="block text-xs font-medium text-slate-600 mb-1">GeoJSON opsional</label><input type="file" name="geojson" accept=".json,.geojson" class="input"><p class="text-[11px] text-slate-400 mt-1">Jika kosong, layer memakai batas 105 desa Nias Barat.</p></div>
                <div class="grid grid-cols-2 gap-3"><label class="flex items-center gap-2 text-sm"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" checked class="rounded text-maroon-700"> Aktif</label><label class="flex items-center gap-2 text-sm"><input type="hidden" name="is_public" value="0"><input type="checkbox" name="is_public" value="1" checked class="rounded text-maroon-700"> Publik</label></div>
                <button class="btn-primary w-full justify-center">Tambah Layer</button>
            </form>
        </x-ui.card>

        <div class="space-y-4">
            <div class="flex items-center justify-between gap-3"><div><h2 class="font-bold text-slate-900">Layer tersedia</h2><p class="text-xs text-slate-500 mt-1">{{ $layers->count() }} layer terdaftar</p></div><a href="{{ route('webgis.index') }}" target="_blank" class="btn-secondary btn-sm">Lihat Peta Publik</a></div>

            @forelse($layers as $layer)
                <details class="card overflow-hidden" @if($loop->first) open @endif>
                    <summary class="list-none cursor-pointer p-5 flex items-center gap-4">
                        <span class="w-3 h-12 rounded-full shrink-0" style="background:linear-gradient({{ $layer->color_from }},{{ $layer->color_to }})"></span>
                        <div class="flex-1 min-w-0"><div class="flex flex-wrap gap-2 items-center"><h3 class="font-bold text-slate-900">{{ $layer->name }}</h3>@if($layer->is_active)<span class="badge-success">Aktif</span>@else<span class="badge-default">Nonaktif</span>@endif @if($layer->is_public)<span class="badge-info">Publik</span>@endif</div><p class="text-xs text-slate-500 mt-1">{{ $layer->values_count }} nilai · slug {{ $layer->slug }}</p></div>
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                    </summary>
                    <div class="border-t border-slate-100 p-5 grid lg:grid-cols-2 gap-6">
                        <form method="POST" action="{{ route('webgis.admin.update', $layer) }}" enctype="multipart/form-data" class="space-y-3">
                            @csrf @method('PUT')
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pengaturan layer</p>
                            <div class="grid sm:grid-cols-2 gap-3"><input name="name" value="{{ $layer->name }}" required class="input" aria-label="Nama layer"><input name="slug" value="{{ $layer->slug }}" required class="input" aria-label="Slug"></div>
                            <div class="grid grid-cols-2 gap-3"><input name="unit" value="{{ $layer->unit }}" class="input" placeholder="Satuan"><input type="number" name="sort_order" min="0" value="{{ $layer->sort_order }}" class="input" placeholder="Urutan"></div>
                            <textarea name="description" rows="2" class="textarea" placeholder="Deskripsi">{{ $layer->description }}</textarea>
                            <div class="grid grid-cols-2 gap-3"><input type="color" name="color_from" value="{{ $layer->color_from }}" class="h-10 w-full rounded-lg border border-slate-200"><input type="color" name="color_to" value="{{ $layer->color_to }}" class="h-10 w-full rounded-lg border border-slate-200"></div>
                            <input name="breakpoints_text" value="{{ collect($layer->breakpoints)->implode(', ') }}" class="input" placeholder="5, 10, 15, 20, 30">
                            <input type="file" name="geojson" accept=".json,.geojson" class="input">
                            <div class="flex gap-4"><label class="flex items-center gap-2 text-sm"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked($layer->is_active) class="rounded text-maroon-700"> Aktif</label><label class="flex items-center gap-2 text-sm"><input type="hidden" name="is_public" value="0"><input type="checkbox" name="is_public" value="1" @checked($layer->is_public) class="rounded text-maroon-700"> Publik</label></div>
                            <button class="btn-primary btn-sm">Simpan Pengaturan</button>
                        </form>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Impor nilai per desa</p>
                            <p class="text-xs text-slate-500 mt-2 leading-5">Unggah Excel atau CSV dengan header <strong>kode_desa</strong>, <strong>tahun</strong>, dan <strong>nilai</strong>. Nilai rentang maupun desimal koma/titik akan dinormalisasi untuk pewarnaan peta, sementara teks aslinya tetap disimpan.</p>
                            <form method="POST" action="{{ route('webgis.admin.values.import', $layer) }}" enctype="multipart/form-data" class="mt-3 space-y-3">
                                @csrf
                                <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="input">
                                <button class="btn-secondary btn-sm">Impor Data Layer</button>
                            </form>
                            <form method="POST" action="{{ route('webgis.admin.destroy', $layer) }}" onsubmit="return confirm('Hapus layer dan semua datanya?')" class="mt-6 pt-4 border-t border-slate-100">
                                @csrf @method('DELETE')<button class="text-xs font-semibold text-amber-700 hover:text-amber-900">Hapus layer ini</button>
                            </form>
                        </div>
                    </div>
                </details>
            @empty
                <x-ui.empty-state title="Belum ada layer WebGIS" description="Tambahkan layer pertama melalui formulir." />
            @endforelse
        </div>
    </div>
@endsection
