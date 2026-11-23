<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Urusan extends Model
{
    protected $table = 'urusan';

    protected $fillable = ['kode', 'nama'];

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'urusan_id');
    }

    public function indikators(): HasMany
    {
        return $this->hasMany(Indikator::class, 'urusan_id');
    }
}