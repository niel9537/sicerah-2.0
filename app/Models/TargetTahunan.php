<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TargetTahunan extends Model
{
    protected $table = 'target_tahunan';
    public $timestamps = false;

    protected $fillable = [
        'subkegiatan_id', 'tahun', 'target_kinerja',
        'pagu_renstra', 'pagu_renja', 'pagu_perubahan',
    ];

    protected $casts = [
        'target_kinerja' => 'decimal:2',
        'pagu_renstra'   => 'decimal:2',
        'pagu_renja'     => 'decimal:2',
        'pagu_perubahan' => 'decimal:2',
    ];

    public function subKegiatan(): BelongsTo
    {
        return $this->belongsTo(SubKegiatan::class, 'subkegiatan_id');
    }

    /**
     * Pagu efektif = pakai pagu_perubahan (APBD-P) jika sudah diisi,
     * jika belum maka pakai pagu_renja (APBD Murni).
     */
    public function paguEfektif(): float
    {
        if ($this->pagu_perubahan !== null && (float) $this->pagu_perubahan > 0) {
            return (float) $this->pagu_perubahan;
        }

        return (float) ($this->pagu_renja ?? 0);
    }

    public function sumberPaguLabel(): string
    {
        return ($this->pagu_perubahan !== null && (float) $this->pagu_perubahan > 0)
            ? 'APBD Perubahan'
            : 'APBD Murni';
    }
}