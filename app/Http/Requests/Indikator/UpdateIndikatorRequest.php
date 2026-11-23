<?php

namespace App\Http\Requests\Indikator;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIndikatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin_bappeda'], true);
    }

    public function rules(): array
    {
        return [
            'jenis'            => 'required|in:IKU,IKK,IKD',
            'aspek'            => 'nullable|string|max:120',
            'is_iku'           => 'nullable|boolean',
            'is_makro'         => 'nullable|boolean',
            'opd_id'           => 'nullable|integer|exists:opd,id',
            'urusan_id'        => 'nullable|integer|exists:urusan,id',
            'sasaran_rpjmd_id' => 'nullable|integer|exists:sasaran_rpjmd,id',
            'nama'             => 'required|string|max:255',
            'satuan'           => 'nullable|string|max:100',
            'sumber_data'      => 'nullable|string|max:255',
            'keterangan'       => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'jenis.required' => 'Jenis indikator wajib dipilih.',
            'nama.required'  => 'Nama indikator wajib diisi.',
        ];
    }
}
