<?php

namespace App\Http\Requests\Program;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgramRequest extends FormRequest
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
            'opd_id.required'    => 'OPD wajib dipilih.',
            'nama.required'      => 'Nama program wajib diisi.',
        ];
    }
}