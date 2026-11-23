<?php

namespace App\Http\Requests\Opd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOpdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin_bappeda'], true);
    }

    public function rules(): array
    {
        $id = $this->route('opd')->id ?? $this->route('opd');

        return [
            'kode'    => ['nullable', 'string', 'max:20', Rule::unique('opd', 'kode')->ignore($id)],
            'nama'    => 'required|string|max:255',
            'alamat'  => 'nullable|string',
            'telepon' => 'nullable|string|max:30',
            'email'   => 'nullable|email|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama OPD wajib diisi.',
            'kode.unique'   => 'Kode OPD sudah digunakan.',
        ];
    }
}