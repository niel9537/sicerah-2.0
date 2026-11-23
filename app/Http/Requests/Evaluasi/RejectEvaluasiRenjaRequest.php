<?php

namespace App\Http\Requests\Evaluasi;

use Illuminate\Foundation\Http\FormRequest;

class RejectEvaluasiRenjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['verifikator', 'admin_bappeda', 'super_admin'], true);
    }

    public function rules(): array
    {
        return [
            'catatan_verifikasi' => 'required|string|min:10',
        ];
    }

    public function messages(): array
    {
        return [
            'catatan_verifikasi.required' => 'Catatan penolakan wajib diisi agar OPD dapat memperbaiki data.',
            'catatan_verifikasi.min'      => 'Catatan minimal 10 karakter, jelaskan alasan penolakan.',
        ];
    }
}