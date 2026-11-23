<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluasiRenja extends Model
{
    protected $table = 'evaluasi_renja';

    public const STATUS_DRAFT        = 'draft';
    public const STATUS_DIAJUKAN     = 'diajukan';
    public const STATUS_DIVERIFIKASI = 'diverifikasi';
    public const STATUS_DITOLAK      = 'ditolak';

    protected $fillable = [
        'subkegiatan_id', 'periode_id',
        'target_fisik', 'target_keuangan',
        'realisasi_fisik', 'realisasi_keuangan',
        'status', 'keterangan',
        'analisis', 'kendala', 'solusi', 'tindak_lanjut',
        'catatan_verifikasi',
        'created_by', 'verified_by', 'verified_at',
    ];

    protected $casts = [
        'target_fisik'       => 'decimal:2',
        'target_keuangan'    => 'decimal:2',
        'realisasi_fisik'    => 'decimal:2',
        'realisasi_keuangan' => 'decimal:2',
        'verified_at'        => 'datetime',
    ];

    public function subKegiatan(): BelongsTo
    {
        return $this->belongsTo(SubKegiatan::class, 'subkegiatan_id');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class, 'periode_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /* -------- Perhitungan capaian -------- */

    public function persentaseFisik(): float
    {
        return $this->target_fisik > 0
            ? round((float) $this->realisasi_fisik / (float) $this->target_fisik * 100, 2)
            : 0;
    }

    public function persentaseKeuangan(): float
    {
        return $this->target_keuangan > 0
            ? round((float) $this->realisasi_keuangan / (float) $this->target_keuangan * 100, 2)
            : 0;
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT        => 'badge-default',
            self::STATUS_DIAJUKAN     => 'badge-warning',
            self::STATUS_DIVERIFIKASI => 'badge-success',
            self::STATUS_DITOLAK      => 'badge-danger',
            default                   => 'badge-default',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT        => 'Draft',
            self::STATUS_DIAJUKAN     => 'Diajukan',
            self::STATUS_DIVERIFIKASI => 'Diverifikasi',
            self::STATUS_DITOLAK      => 'Ditolak',
            default                   => '-',
        };
    }
}