<?php

namespace App\Http\Requests\Evaluasi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEvaluasiRenjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['operator_opd', 'admin_bappeda', 'super_admin'], true);
    }

    public function rules(): array
    {
        return [
            'realisasi_fisik'    => 'nullable|numeric|min:0',
            'realisasi_keuangan' => 'nullable|numeric|min:0',
            'keterangan'         => 'nullable|string',
            'analisis'           => 'nullable|string',
            'kendala'            => 'nullable|string',
            'solusi'             => 'nullable|string',
            'tindak_lanjut'      => 'nullable|string',
        ];
    }
}