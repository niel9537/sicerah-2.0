<?php

namespace App\Http\Requests\TargetTahunan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTargetTahunanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin_bappeda'], true);
    }

    public function rules(): array
    {
        return [
            'subkegiatan_id' => [
                'required', 'integer', 'exists:subkegiatan,id',
                Rule::unique('target_tahunan')->where(fn ($q) => $q->where('tahun', $this->tahun)),
            ],
            'tahun'          => 'required|integer|digits:4|min:2000|max:2100',
            'target_kinerja' => 'required|numeric|min:0',
            'pagu_renstra'   => 'nullable|numeric|min:0',
            'pagu_renja'     => 'required|numeric|min:0',
            'pagu_perubahan' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'subkegiatan_id.required' => 'Sub kegiatan wajib dipilih.',
            'subkegiatan_id.unique'   => 'Target untuk sub kegiatan dan tahun ini sudah ada.',
            'tahun.required'          => 'Tahun wajib diisi.',
            'target_kinerja.required' => 'Target kinerja wajib diisi.',
            'pagu_renja.required'     => 'Pagu RENJA (APBD Murni) wajib diisi.',
        ];
    }
}