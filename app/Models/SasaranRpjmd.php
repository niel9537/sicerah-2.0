<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SasaranRpjmd extends Model
{
    protected $table = 'sasaran_rpjmd';

    protected $fillable = ['kode', 'nama', 'periode_awal', 'periode_akhir'];

    public function tujuanPds(): HasMany
    {
        return $this->hasMany(TujuanPd::class, 'sasaran_rpjmd_id');
    }
}