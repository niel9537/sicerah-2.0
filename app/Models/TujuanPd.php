<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TujuanPd extends Model
{
    protected $table = 'tujuan_pd';
    public $timestamps = false;

    protected $fillable = [
        'opd_id', 'sasaran_rpjmd_id', 'kode', 'nama',
        'tahun_awal', 'tahun_akhir',
    ];

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'opd_id');
    }

    public function sasaranRpjmd(): BelongsTo
    {
        return $this->belongsTo(SasaranRpjmd::class, 'sasaran_rpjmd_id');
    }

    public function sasaranPds(): HasMany
    {
        return $this->hasMany(SasaranPd::class, 'tujuan_pd_id');
    }
}