<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Kode OPD</label>
            <input type="text" name="kode" value="{{ old('kode', $opd->kode ?? '') }}"
                   class="input" placeholder="Cth: 1.01">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Telepon</label>
            <input type="text" name="telepon" value="{{ old('telepon', $opd->telepon ?? '') }}" class="input">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama OPD <span class="text-red-500">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $opd->nama ?? '') }}" required
               class="input" placeholder="Cth: Dinas Pendidikan">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
        <textarea name="alamat" rows="2" class="textarea">{{ old('alamat', $opd->alamat ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $opd->email ?? '') }}" class="input">
    </div>
</div>