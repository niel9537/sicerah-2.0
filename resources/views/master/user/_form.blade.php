<div class="space-y-4" x-data="{ role: '{{ old('role', $user->role ?? '') }}' }">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $user->nama ?? '') }}" required class="input">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required class="input">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
                Kata Sandi {!! isset($user) ? '<span class="text-slate-400 font-normal">(kosongkan jika tidak diubah)</span>' : '<span class="text-red-500">*</span>' !!}
            </label>
            <input type="password" name="password" class="input" {{ isset($user) ? '' : 'required' }}>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Kata Sandi</label>
            <input type="password" name="password_confirmation" class="input" {{ isset($user) ? '' : 'required' }}>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Role <span class="text-red-500">*</span></label>
        <select name="role" x-model="role" required class="select">
            <option value="">— Pilih Role —</option>
            <option value="super_admin">Super Administrator</option>
            <option value="admin_bappeda">Admin Bappeda</option>
            <option value="operator_opd">Operator OPD</option>
            <option value="verifikator">Verifikator</option>
            <option value="inspektorat">Inspektorat (khusus eMonev ProSN)</option>
            <option value="pimpinan">Pimpinan</option>
        </select>
    </div>

    <div x-show="role === 'operator_opd'" x-cloak>
        <label class="block text-sm font-medium text-slate-700 mb-1">OPD <span class="text-red-500">*</span></label>
        <select name="opd_id" class="select">
            <option value="">— Pilih OPD —</option>
            @foreach($opdList as $o)
                <option value="{{ $o->id }}" @selected(old('opd_id', $user->opd_id ?? null) == $o->id)>{{ $o->nama }}</option>
            @endforeach
        </select>
    </div>
</div>