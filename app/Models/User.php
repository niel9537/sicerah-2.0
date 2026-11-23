<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN    = 'super_admin';
    public const ROLE_ADMIN_BAPPEDA  = 'admin_bappeda';
    public const ROLE_OPERATOR_OPD   = 'operator_opd';
    public const ROLE_VERIFIKATOR    = 'verifikator';
    public const ROLE_INSPEKTORAT = 'inspektorat';
    public const ROLE_PIMPINAN       = 'pimpinan';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'opd_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /* ---------------- Relasi ---------------- */

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'opd_id');
    }

    /* ---------------- Accessor / Helper ---------------- */

    public function getNameAttribute(): ?string
    {
        // agar Breeze / Auth::user()->name tetap bekerja
        return $this->nama;
    }

    public function isSuperAdmin(): bool     { return $this->role === self::ROLE_SUPER_ADMIN; }
    public function isAdminBappeda(): bool   { return $this->role === self::ROLE_ADMIN_BAPPEDA; }
    public function isOperator(): bool       { return $this->role === self::ROLE_OPERATOR_OPD; }
    public function isVerifikator(): bool    { return $this->role === self::ROLE_VERIFIKATOR; }
    public function isPimpinan(): bool       { return $this->role === self::ROLE_PIMPINAN; }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN   => 'Super Administrator',
            self::ROLE_ADMIN_BAPPEDA => 'Admin Bappeda',
            self::ROLE_OPERATOR_OPD  => 'Operator OPD',
            self::ROLE_VERIFIKATOR   => 'Verifikator',
            self::ROLE_INSPEKTORAT => 'Inspektorat',
            self::ROLE_PIMPINAN      => 'Pimpinan',
            default                  => '-',
        };
    }
}