<?php

namespace App\Http\Requests\SubKegiatan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubKegiatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin_bappeda'], true);
    }

    public function rules(): array
    {
        return [
            'kegiatan_id' => 'required|integer|exists:kegiatan,id',
            'kode'        => 'nullable|string|max:30',
            'nama'        => 'required|string|max:255',
            'indikator'   => 'nullable|string|max:255',
            'satuan'      => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'kegiatan_id.required' => 'Kegiatan wajib dipilih.',
            'nama.required'        => 'Nama sub kegiatan wajib diisi.',
        ];
    }
}