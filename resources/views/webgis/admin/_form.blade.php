<div class="wg-grid">
<label>Nama tema<input name="name" value="{{ old('name',$theme->name) }}" required maxlength="150"></label>
<label>Slug unik<input name="slug" value="{{ old('slug',$theme->slug) }}" placeholder="akses-air-minum" required></label>
<label>Satuan<input name="unit" value="{{ old('unit',$theme->unit) }}" placeholder="rumah tangga / % / jiwa" required></label>
<label>Visibilitas<select name="is_public"><option value="0" @selected(old('is_public',$theme->is_public)==0)>Draf — admin saja</option><option value="1" @selected(old('is_public',$theme->is_public)==1)>Publik — tampil di landing page</option></select></label>
<label>Deskripsi<textarea name="description">{{ old('description',$theme->description) }}</textarea></label>
<label>Sumber data<input name="source" value="{{ old('source',$theme->source) }}"></label>
<label>Batas kelas (naik, dipisah koma)<input name="breaks_text" value="{{ old('breaks_text',implode(',',$theme->breaks ?? [10,20])) }}" required><small>10,20 berarti ≤10; >10–20; >20. Sesuaikan menurut indikator.</small></label>
<label>Warna kelas (jumlah batas + 1)<input name="colors_text" value="{{ old('colors_text',implode(',',$theme->colors ?? ['#22c55e','#f59e0b','#ef4444'])) }}" required></label>
<label>GeoJSON opsional<input type="file" name="geojson" accept=".geojson,.json"><small>Kosong: pertahankan peta, atau gunakan 105 desa bawaan untuk tema baru. Maksimal 10 MB.</small></label>
<label>Atribut kode pada berkas GeoJSON<input name="code_property" value="{{ old('code_property','KDEPUM') }}" required><small>Gunakan kode_desa jika berkas sudah dinormalisasi.</small></label>
</div><button class="wg-button">Simpan tema</button>