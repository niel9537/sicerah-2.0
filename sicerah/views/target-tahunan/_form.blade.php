<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Sub Kegiatan <span class="text-red-500">*</span></label>
        <select name="subkegiatan_id" required class="select">
            <option value="">— Pilih Sub Kegiatan —</option>
            @foreach($subKegiatanList as $s)
                <option value="{{ $s->id }}" @selected(old('subkegiatan_id', $target->subkegiatan_id ?? null) == $s->id)>
                    {{ $s->kode }} — {{ Str::limit($s->nama, 50) }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" value="{{ old('tahun', $target->tahun ?? $tahunDefault ?? now()->year) }}"
               required class="input" min="2000" max="2100">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Target Kinerja <span class="text-red-500">*</span></label>
        <input type="number" step="0.01" name="target_kinerja" value="{{ old('target_kinerja', $target->target_kinerja ?? '') }}"
               required class="input" placeholder="Sesuai satuan sub kegiatan">
    </div>

    <div class="grid grid-cols-3 gap-4 pt-2 border-t border-slate-100">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Pagu Renstra (Rp)</label>
            <input type="number" step="0.01" name="pagu_renstra" value="{{ old('pagu_renstra', $target->pagu_renstra ?? '') }}" class="input">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Pagu Renja — APBD Murni (Rp) <span class="text-red-500">*</span></label>
            <input type="number" step="0.01" name="pagu_renja" value="{{ old('pagu_renja', $target->pagu_renja ?? '') }}" required class="input">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Pagu Perubahan — APBD-P (Rp)</label>
            <input type="number" step="0.01" name="pagu_perubahan" value="{{ old('pagu_perubahan', $target->pagu_perubahan ?? '') }}" class="input" placeholder="Isi jika sudah ada perubahan APBD">
        </div>
    </div>
    <p class="text-xs text-slate-400">Pagu efektif otomatis mengikuti Pagu Perubahan jika sudah diisi, jika belum mengikuti Pagu Renja (APBD Murni).</p>
</div>