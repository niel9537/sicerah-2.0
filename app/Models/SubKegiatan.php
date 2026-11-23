<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubKegiatan extends Model
{
    protected $table = 'subkegiatan';
    public $timestamps = false;

    protected $fillable = [
        'kegiatan_id', 'kode', 'nama', 'indikator', 'satuan',
    ];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function targetTahunans(): HasMany
    {
        return $this->hasMany(TargetTahunan::class, 'subkegiatan_id');
    }

    public function realisasiTriwulanans(): HasMany
    {
        return $this->hasMany(RealisasiTriwulanan::class, 'subkegiatan_id');
    }

    public function evaluasiRenjas(): HasMany
    {
        return $this->hasMany(EvaluasiRenja::class, 'subkegiatan_id');
    }

    public function targetForYear(int $tahun): ?TargetTahunan
    {
        return $this->targetTahunans()->where('tahun', $tahun)->first();
    }

    public function realisasiForPeriod(int $tahun, int $triwulan): ?RealisasiTriwulanan
    {
        return $this->realisasiTriwulanans()
            ->where('tahun', $tahun)
            ->where('triwulan', $triwulan)
            ->first();
    }

    /** Batasi query hanya sub kegiatan milik OPD tertentu (lewat kegiatan → program → opd_id) */
    public function scopeForOpd($query, ?int $opdId)
    {
        if (! $opdId) {
            return $query; // admin/super admin tanpa filter opd → tampilkan semua
        }

        return $query->whereHas(
            'kegiatan.program',
            fn ($q) => $q->where('opd_id', $opdId)
        );
    }
}