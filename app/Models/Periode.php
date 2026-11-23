<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periode extends Model
{
    protected $table = 'periode';
    public $timestamps = false;

    protected $fillable = ['tahun', 'triwulan', 'nama', 'status'];

    public function evaluasiRenjas(): HasMany
    {
        return $this->hasMany(EvaluasiRenja::class, 'periode_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function getLabelAttribute(): string
    {
        return "Triwulan {$this->triwulanRomawi()} Tahun {$this->tahun}";
    }

    public function triwulanRomawi(): string
    {
        return ['I', 'II', 'III', 'IV'][$this->triwulan - 1] ?? '-';
    }
}