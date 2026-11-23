<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Kode Program</label>
            <input type="text" name="kode" value="{{ old('kode', $program->kode ?? '') }}"
                   class="input" placeholder="Cth: 1.01.01">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Urusan <span class="text-red-500">*</span></label>
            <select name="urusan_id" required class="select">
                <option value="">— Pilih Urusan —</option>
                @foreach($urusanList as $u)
                    <option value="{{ $u->id }}" @selected(old('urusan_id', $program->urusan_id ?? null) == $u->id)>
                        {{ $u->kode }} — {{ $u->nama }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Program <span class="text-red-500">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $program->nama ?? '') }}" required class="input">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">OPD Pengampu <span class="text-red-500">*</span></label>
            <select name="opd_id" required class="select">
                <option value="">— Pilih OPD —</option>
                @foreach($opdList as $o)
                    <option value="{{ $o->id }}" @selected(old('opd_id', $program->opd_id ?? null) == $o->id)>{{ $o->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Sasaran PD Terkait</label>
            <select name="sasaran_pd_id" class="select">
                <option value="">— Tidak ada —</option>
                @foreach($sasaranList as $s)
                    <option value="{{ $s->id }}" @selected(old('sasaran_pd_id', $program->sasaran_pd_id ?? null) == $s->id)>{{ $s->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>