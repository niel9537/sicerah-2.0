<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $table = 'program';
    public $timestamps = false;

    protected $fillable = [
        'urusan_id', 'opd_id', 'sasaran_pd_id', 'kode', 'nama',
    ];

    public function urusan(): BelongsTo
    {
        return $this->belongsTo(Urusan::class, 'urusan_id');
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'opd_id');
    }

    public function sasaranPd(): BelongsTo
    {
        return $this->belongsTo(SasaranPd::class, 'sasaran_pd_id');
    }

    public function kegiatans(): HasMany
    {
        return $this->hasMany(Kegiatan::class, 'program_id');
    }
}