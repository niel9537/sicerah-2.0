<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin_bappeda'], true);
    }

    public function rules(): array
    {
        $id = $this->route('user')->id ?? $this->route('user');

        return [
            'nama'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => 'required|in:super_admin,admin_bappeda,operator_opd,verifikator,pimpinan,inspektorat',
            'opd_id'   => 'nullable|integer|exists:opd,id|required_if:role,operator_opd',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah digunakan pengguna lain.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'role.required'      => 'Role wajib dipilih.',
            'opd_id.required_if' => 'OPD wajib dipilih untuk role Operator OPD.',
        ];
    }
}