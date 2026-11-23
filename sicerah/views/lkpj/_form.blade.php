@php $user = auth()->user(); @endphp
<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Urusan <span class="text-red-500">*</span></label>
            <select name="urusan_id" required class="select">
                <option value="">— Pilih Urusan —</option>
                @foreach($urusanList as $u)
                    <option value="{{ $u->id }}" @selected(old('urusan_id', $lkpj->urusan_id ?? null) == $u->id)>{{ $u->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tahun <span class="text-red-500">*</span></label>
            <input type="number" name="tahun" value="{{ old('tahun', $lkpj->tahun ?? $tahunDefault ?? now()->year) }}" required class="input" min="2000" max="2100">
        </div>
    </div>

    @if($user->role !== 'operator_opd')
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">OPD <span class="text-red-500">*</span></label>
            <select name="opd_id" required class="select">
                <option value="">— Pilih OPD —</option>
                @foreach($opdList as $o)
                    <option value="{{ $o->id }}" @selected(old('opd_id', $lkpj->opd_id ?? null) == $o->id)>{{ $o->nama }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Kebijakan</label>
        <textarea name="kebijakan" rows="2" class="textarea">{{ old('kebijakan', $lkpj->kebijakan ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Uraian Program</label>
        <textarea name="uraian_program" rows="2" class="textarea">{{ old('uraian_program', $lkpj->uraian_program ?? '') }}</textarea>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Target</label>
            <textarea name="target" rows="2" class="textarea">{{ old('target', $lkpj->target ?? '') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Realisasi</label>
            <textarea name="realisasi" rows="2" class="textarea">{{ old('realisasi', $lkpj->realisasi ?? '') }}</textarea>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Permasalahan</label>
        <textarea name="permasalahan" rows="2" class="textarea">{{ old('permasalahan', $lkpj->permasalahan ?? '') }}</textarea>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Upaya</label>
            <textarea name="upaya" rows="2" class="textarea">{{ old('upaya', $lkpj->upaya ?? '') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Rekomendasi</label>
            <textarea name="rekomendasi" rows="2" class="textarea">{{ old('rekomendasi', $lkpj->rekomendasi ?? '') }}</textarea>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
        <select name="status" class="select">
            <option value="draft" @selected(old('status', $lkpj->status ?? 'draft') === 'draft')>Draft</option>
            <option value="diajukan" @selected(old('status', $lkpj->status ?? '') === 'diajukan')>Diajukan</option>
            @if($user->role !== 'operator_opd')
                <option value="diverifikasi" @selected(old('status', $lkpj->status ?? '') === 'diverifikasi')>Diverifikasi</option>
            @endif
        </select>
    </div>
</div>