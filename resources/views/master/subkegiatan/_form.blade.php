<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Kode Sub Kegiatan</label>
            <input type="text" name="kode" value="{{ old('kode', $subkegiatan->kode ?? '') }}"
                   class="input" placeholder="Cth: 1.01.01.01.001">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Kegiatan <span class="text-red-500">*</span></label>
            <select name="kegiatan_id" required class="select">
                <option value="">— Pilih Kegiatan —</option>
                @foreach($kegiatanList as $k)
                    <option value="{{ $k->id }}" @selected(old('kegiatan_id', $subkegiatan->kegiatan_id ?? null) == $k->id)>{{ Str::limit($k->nama, 50) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Sub Kegiatan <span class="text-red-500">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $subkegiatan->nama ?? '') }}" required class="input">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Indikator Kinerja</label>
            <input type="text" name="indikator" value="{{ old('indikator', $subkegiatan->indikator ?? '') }}"
                   class="input" placeholder="Cth: Jumlah unit terbangun">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Satuan</label>
            <input type="text" name="satuan" value="{{ old('satuan', $subkegiatan->satuan ?? '') }}"
                   class="input" placeholder="Cth: Unit, Km, Orang">
        </div>
    </div>
</div>