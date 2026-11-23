<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealisasiTriwulanan extends Model
{
    protected $table = 'realisasi_triwulanan';

    protected $fillable = [
        'subkegiatan_id', 'tahun', 'triwulan',
        'realisasi_kinerja', 'realisasi_anggaran',
        'status', 'catatan_verifikasi',
        'input_by', 'verified_by', 'verified_at',
    ];

    protected $casts = [
        'realisasi_kinerja'  => 'decimal:2',
        'realisasi_anggaran' => 'decimal:2',
        'verified_at'        => 'datetime',
    ];

    public function subKegiatan(): BelongsTo
    {
        return $this->belongsTo(SubKegiatan::class, 'subkegiatan_id');
    }

    public function inputBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeForPeriode($q, int $tahun, ?int $triwulan = null)
    {
        $q->where('tahun', $tahun);
        if ($triwulan) $q->where('triwulan', $triwulan);
        return $q;
    }
}