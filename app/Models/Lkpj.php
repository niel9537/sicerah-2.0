<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lkpj extends Model
{
    protected $table = 'lkpj';
    public $timestamps = false;

    protected $fillable = [
        'urusan_id', 'opd_id', 'tahun',
        'kebijakan', 'uraian_program', 'target', 'realisasi',
        'permasalahan', 'upaya', 'rekomendasi', 'status',
    ];

    public function urusan(): BelongsTo { return $this->belongsTo(Urusan::class, 'urusan_id'); }
    public function opd(): BelongsTo    { return $this->belongsTo(Opd::class, 'opd_id'); }
}