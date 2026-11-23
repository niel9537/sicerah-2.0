<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prosn extends Model
{
    protected $table = 'emonev_prosn';

    protected $guarded = ['id', 'tahun', 'baris', 'jenis', 'tema', 'program', 'nomor', 'kode'];

    protected function casts(): array
    {
        return ['tautan' => 'array', 'tahun' => 'integer', 'versi' => 'integer'];
    }

    public function validasiTerakhir()
    {
        return $this->hasOne(ProsnValidasi::class, 'prosn_id')->latestOfMany();
    }

    public function riwayatValidasi()
    {
        return $this->hasMany(ProsnValidasi::class, 'prosn_id')->orderByDesc('id');
    }

    public function getStatusValidasiAttribute(): string
    {
        $v = $this->validasiTerakhir;

        return $v && (int) $v->versi === $this->versi ? ucfirst($v->keputusan) : 'Menunggu validasi';
    }

    public const STATUS = ['Sudah Sesuai', 'Belum Sesuai', 'Belum Lapor'];

    public const DOKUMEN = ['Ditemukan', 'Belum ditemukan'];
}
