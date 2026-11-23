<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opd extends Model
{
    protected $table = 'opd';

    protected $fillable = ['kode', 'nama', 'alamat', 'telepon', 'email'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'opd_id');
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'opd_id');
    }

    public function tujuanPd(): HasMany
    {
        return $this->hasMany(TujuanPd::class, 'opd_id');
    }

    public function indikators(): HasMany
    {
        return $this->hasMany(Indikator::class, 'opd_id');
    }

    public function lkpjs(): HasMany
    {
        return $this->hasMany(Lkpj::class, 'opd_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->kode ? $this->kode . ' - ' : '') . $this->nama);
    }
}