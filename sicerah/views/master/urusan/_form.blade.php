<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Kode Urusan <span class="text-red-500">*</span></label>
        <input type="text" name="kode" value="{{ old('kode', $urusan->kode ?? '') }}" required
               class="input" placeholder="Cth: 1">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Urusan <span class="text-red-500">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $urusan->nama ?? '') }}" required
               class="input" placeholder="Cth: Pendidikan">
    </div>
</div>