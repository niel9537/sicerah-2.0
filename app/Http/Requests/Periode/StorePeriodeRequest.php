<?php

namespace App\Http\Requests\Periode;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePeriodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin_bappeda'], true);
    }

    public function rules(): array
    {
        return [
            'tahun'    => [
                'required', 'integer', 'digits:4', 'min:2000', 'max:2100',
                Rule::unique('periode')->where(fn ($q) => $q->where('triwulan', $this->triwulan)),
            ],
            'triwulan' => 'required|integer|between:1,4',
            'nama'     => 'nullable|string|max:30',
            'status'   => 'required|in:aktif,tutup',
        ];
    }

    public function messages(): array
    {
        return [
            'tahun.required'    => 'Tahun wajib diisi.',
            'tahun.unique'      => 'Kombinasi tahun dan triwulan ini sudah ada.',
            'triwulan.required' => 'Triwulan wajib dipilih.',
            'triwulan.between'  => 'Triwulan harus antara 1 sampai 4.',
        ];
    }
}