<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndikatorRealisasi extends Model
{
    protected $table = 'indikator_realisasi';
    protected $fillable = [
        'indikator_id', 'tahun', 'triwulan', 'realisasi', 'status',
    ];


    public function indikator(): BelongsTo
    {
        return $this->belongsTo(Indikator::class, 'indikator_id');
    }
}
