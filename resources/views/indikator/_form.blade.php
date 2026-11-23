<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Jenis <span class="text-red-500">*</span></label>
            <select name="jenis" required class="select">
                <option value="IKU" @selected(old('jenis', $indikator->jenis ?? $jenisDefault ?? 'IKU') === 'IKU')>IKU</option>
                <option value="IKK" @selected(old('jenis', $indikator->jenis ?? '') === 'IKK')>IKK</option>
                <option value="IKD" @selected(old('jenis', $indikator->jenis ?? '') === 'IKD')>IKD</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Satuan</label>
            <input type="text" name="satuan" value="{{ old('satuan', $indikator->satuan ?? '') }}" class="input" placeholder="Cth: %, Poin, Unit">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Indikator <span class="text-red-500">*</span></label>
        <textarea name="nama" rows="2" required class="textarea">{{ old('nama', $indikator->nama ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Aspek IKD</label>
        <select name="aspek" class="select">
            <option value="">— Belum ditentukan —</option>
            @foreach([
                'ASPEK GEOGRAFI DAN DEMOGRAFI',
                'ASPEK KESEJAHTERAAN MASYARAKAT',
                'ASPEK DAYA SAING DAERAH',
                'ASPEK PELAYANAN UMUM',
            ] as $aspekOption)
                <option value="{{ $aspekOption }}" @selected(old('aspek', $indikator->aspek ?? '') === $aspekOption)>
                    {{ Str::headline(Str::lower(str_replace('ASPEK ', '', $aspekOption))) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-3 bg-white">
            <input type="hidden" name="is_iku" value="0">
            <input type="checkbox" name="is_iku" value="1" class="mt-0.5 rounded border-slate-300 text-maroon-700 focus:ring-maroon-500"
                   @checked((bool) old('is_iku', $indikator->is_iku ?? false))>
            <span><span class="block text-sm font-medium text-slate-700">Termasuk IKU</span><span class="block text-xs text-slate-500">Ditampilkan pada kelompok Indikator Kinerja Utama.</span></span>
        </label>
        <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-3 bg-white">
            <input type="hidden" name="is_makro" value="0">
            <input type="checkbox" name="is_makro" value="1" class="mt-0.5 rounded border-slate-300 text-maroon-700 focus:ring-maroon-500"
                   @checked((bool) old('is_makro', $indikator->is_makro ?? $isMakroDefault ?? false))>
            <span><span class="block text-sm font-medium text-slate-700">Termasuk indikator makro</span><span class="block text-xs text-slate-500">Ditampilkan pada sorotan makro publik.</span></span>
        </label>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">OPD Pengampu</label>
            <select name="opd_id" class="select">
                <option value="">— Tidak terikat OPD —</option>
                @foreach($opdList as $o)
                    <option value="{{ $o->id }}" @selected(old('opd_id', $indikator->opd_id ?? null) == $o->id)>{{ $o->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Urusan</label>
            <select name="urusan_id" class="select">
                <option value="">— Tidak terikat Urusan —</option>
                @foreach($urusanList as $u)
                    <option value="{{ $u->id }}" @selected(old('urusan_id', $indikator->urusan_id ?? null) == $u->id)>{{ $u->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Sasaran RPJMD Terkait</label>
            <select name="sasaran_rpjmd_id" class="select">
                <option value="">— Tidak ada —</option>
                @foreach($sasaranList as $s)
                    <option value="{{ $s->id }}" @selected(old('sasaran_rpjmd_id', $indikator->sasaran_rpjmd_id ?? null) == $s->id)>{{ Str::limit($s->nama, 50) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Sumber Data</label>
            <input type="text" name="sumber_data" value="{{ old('sumber_data', $indikator->sumber_data ?? '') }}" class="input" placeholder="Cth: BPS, Dinas Pendidikan">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan sumber</label>
        <textarea name="keterangan" rows="3" class="textarea" placeholder="Catatan, proyeksi, atau rincian sumber data">{{ old('keterangan', $indikator->keterangan ?? '') }}</textarea>
    </div>
</div>
