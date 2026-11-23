<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tahun <span class="text-red-500">*</span></label>
            <input type="number" name="tahun" value="{{ old('tahun', $periode->tahun ?? date('Y')) }}" required
                   class="input" min="2000" max="2100">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Triwulan <span class="text-red-500">*</span></label>
            <select name="triwulan" required class="select">
                @foreach([1,2,3,4] as $tw)
                    <option value="{{ $tw }}" @selected(old('triwulan', $periode->triwulan ?? null) == $tw)>
                        Triwulan {{ ['I','II','III','IV'][$tw-1] }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama (opsional)</label>
        <input type="text" name="nama" value="{{ old('nama', $periode->nama ?? '') }}" class="input" placeholder="Cth: Triwulan I 2026">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Status <span class="text-red-500">*</span></label>
        <select name="status" required class="select">
            <option value="aktif" @selected(old('status', $periode->status ?? 'aktif') === 'aktif')>Aktif</option>
            <option value="tutup" @selected(old('status', $periode->status ?? '') === 'tutup')>Tutup</option>
        </select>
    </div>
</div>