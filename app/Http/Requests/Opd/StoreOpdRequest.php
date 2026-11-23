<?php

namespace App\Http\Requests\Opd;

use Illuminate\Foundation\Http\FormRequest;

class StoreOpdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin_bappeda'], true);
    }

    public function rules(): array
    {
        return [
            'kode'    => 'nullable|string|max:20|unique:opd,kode',
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
            'email.email'   => 'Format email tidak valid.',
        ];
    }
}