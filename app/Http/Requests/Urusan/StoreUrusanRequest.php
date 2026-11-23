<?php

namespace App\Http\Requests\Urusan;

use Illuminate\Foundation\Http\FormRequest;

class StoreUrusanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin_bappeda'], true);
    }

    public function rules(): array
    {
        return [
            'kode' => 'required|string|max:20|unique:urusan,kode',
            'nama' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'kode.required' => 'Kode urusan wajib diisi.',
            'kode.unique'   => 'Kode urusan sudah digunakan.',
            'nama.required' => 'Nama urusan wajib diisi.',
        ];
    }
}