<?php

namespace App\Http\Requests\Evaluasi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEvaluasiRenjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['operator_opd', 'admin_bappeda', 'super_admin'], true);
    }

    public function rules(): array
    {
        $periodeId = $this->input('periode_id'); // ← di-capture dulu, bukan $this->periode_id

        return [
            'subkegiatan_id' => [
                'required', 'integer', 'exists:subkegiatan,id',
                Rule::unique('evaluasi_renja')->where(
                    fn ($q) => $q->where('periode_id', $periodeId)
                ),
            ],
            'periode_id' => 'required|integer|exists:periode,id',
        ];
    }

    public function messages(): array
    {
        return [
            'subkegiatan_id.required' => 'Sub kegiatan wajib dipilih.',
            'subkegiatan_id.unique'   => 'Evaluasi untuk sub kegiatan dan periode ini sudah ada.',
            'periode_id.required'     => 'Periode wajib dipilih.',
        ];
    }
}