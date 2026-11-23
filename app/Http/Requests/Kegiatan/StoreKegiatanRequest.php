<?php

namespace App\Http\Requests\Kegiatan;

use Illuminate\Foundation\Http\FormRequest;

class StoreKegiatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin_bappeda'], true);
    }

    public function rules(): array
    {
        return [
            'program_id' => 'required|integer|exists:program,id',
            'kode'       => 'nullable|string|max:30',
            'nama'       => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'program_id.required' => 'Program wajib dipilih.',
            'program_id.exists'   => 'Program yang dipilih tidak valid.',
            'nama.required'       => 'Nama kegiatan wajib diisi.',
        ];
    }
}