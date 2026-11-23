<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SasaranPd extends Model
{
    protected $table = 'sasaran_pd';
    public $timestamps = false;

    protected $fillable = [
        'tujuan_pd_id', 'kode', 'nama', 'tahun_awal', 'tahun_akhir',
    ];

    public function tujuanPd(): BelongsTo
    {
        return $this->belongsTo(TujuanPd::class, 'tujuan_pd_id');
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'sasaran_pd_id');
    }
}