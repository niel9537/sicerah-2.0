<?php

namespace App\Http\Requests\Lkpj;

use Illuminate\Foundation\Http\FormRequest;

class StoreLkpjRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['operator_opd', 'admin_bappeda', 'super_admin'], true);
    }

    public function rules(): array
    {
        $isOperator = auth()->user()->role === 'operator_opd';

        return [
            'urusan_id'      => 'required|integer|exists:urusan,id',
            'opd_id'         => $isOperator ? 'nullable' : 'required|integer|exists:opd,id',
            'tahun'          => 'required|integer|digits:4|min:2000|max:2100',
            'kebijakan'      => 'nullable|string',
            'uraian_program' => 'nullable|string',
            'target'         => 'nullable|string',
            'realisasi'      => 'nullable|string',
            'permasalahan'   => 'nullable|string',
            'upaya'          => 'nullable|string',
            'rekomendasi'    => 'nullable|string',
            'status'         => $isOperator ? 'nullable|in:draft,diajukan' : 'required|in:draft,diajukan,diverifikasi',
        ];
    }

    public function messages(): array
    {
        return [
            'urusan_id.required' => 'Urusan wajib dipilih.',
            'opd_id.required'    => 'OPD wajib dipilih.',
            'tahun.required'     => 'Tahun wajib diisi.',
        ];
    }
}