<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RpjmdIndicatorSource extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['targets' => 'array'];

    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }

    public function scopeIku($query)
    {
        return $query->where('jenis', 'IKU');
    }

    public function scopeIkk($query)
    {
        return $query->where('jenis', 'IKK');
    }

    public function scopeIkd($query)
    {
        return $query->where('jenis', 'IKD');
    }

    public function scopeDaerah($query)
    {
        return $query->whereNull('perangkat_daerah');
    }

    public function scopePerangkatDaerah($query)
    {
        return $query->whereNotNull('perangkat_daerah');
    }
}
