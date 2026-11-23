<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Kode Kegiatan</label>
            <input type="text" name="kode" value="{{ old('kode', $kegiatan->kode ?? '') }}"
                   class="input" placeholder="Cth: 1.01.01.01">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Program <span class="text-red-500">*</span></label>
            <select name="program_id" required class="select">
                <option value="">— Pilih Program —</option>
                @foreach($programList as $p)
                    <option value="{{ $p->id }}" @selected(old('program_id', $kegiatan->program_id ?? null) == $p->id)>{{ $p->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Kegiatan <span class="text-red-500">*</span></label>
        <textarea name="nama" rows="2" required class="textarea">{{ old('nama', $kegiatan->nama ?? '') }}</textarea>
    </div>
</div>