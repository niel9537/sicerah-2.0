<?php

namespace App\Http\Requests\Program;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin_bappeda'], true);
    }

    public function rules(): array
    {
        return [
            'urusan_id'     => 'required|integer|exists:urusan,id',
            'opd_id'        => 'required|integer|exists:opd,id',
            'sasaran_pd_id' => 'nullable|integer|exists:sasaran_pd,id',
            'kode'          => 'nullable|string|max:30',
            'nama'          => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'urusan_id.required' => 'Urusan wajib dipilih.',
            'urusan_id.exists'   => 'Urusan yang dipilih tidak valid.',
            'opd_id.required'    => 'OPD wajib dipilih.',
            'opd_id.exists'      => 'OPD yang dipilih tidak valid.',
            'nama.required'      => 'Nama program wajib diisi.',
        ];
    }
}