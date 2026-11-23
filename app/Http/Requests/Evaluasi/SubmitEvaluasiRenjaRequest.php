<?php

namespace App\Http\Requests\Evaluasi;

use Illuminate\Foundation\Http\FormRequest;

class SubmitEvaluasiRenjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['operator_opd', 'admin_bappeda', 'super_admin'], true);
    }

    public function rules(): array
    {
        return [
            'realisasi_fisik'    => 'required|numeric|min:0',
            'realisasi_keuangan' => 'required|numeric|min:0',
            'keterangan'         => 'required|string|min:10',
            'analisis'           => 'nullable|string',
            'kendala'            => 'nullable|string',
            'solusi'             => 'nullable|string',
            'tindak_lanjut'      => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'realisasi_fisik.required'    => 'Realisasi fisik wajib diisi sebelum diajukan.',
            'realisasi_keuangan.required' => 'Realisasi keuangan wajib diisi sebelum diajukan.',
            'keterangan.required'         => 'Keterangan wajib diisi sebelum diajukan untuk verifikasi.',
            'keterangan.min'              => 'Keterangan minimal 10 karakter, jelaskan progres secara ringkas.',
        ];
    }
}