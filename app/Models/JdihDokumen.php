<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JdihDokumen extends Model
{
    use HasFactory;

    protected $table = 'jdih_dokumen';

    protected $fillable = [
        'kategori',
        'judul',
        'nomor',
        'tahun',
        'ringkasan',
        'file_url',
        'tanggal_terbit',
        'is_published',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'tanggal_terbit' => 'date',
        'is_published' => 'boolean',
    ];

    /** Label kategori yang enak dibaca untuk ditampilkan di UI. */
    public const KATEGORI_LABEL = [
        'RPJPD' => 'RPJPD',
        'RPJMD' => 'RPJMD',
        'RENSTRA' => 'Renstra',
        'RKPD' => 'RKPD',
        'RENJA' => 'Renja',
        'PERDA' => 'Peraturan Daerah',
        'PERBUP' => 'Peraturan Bupati',
        'LAINNYA' => 'Lainnya',
    ];

    public function kategoriLabel(): string
    {
        return self::KATEGORI_LABEL[$this->kategori] ?? $this->kategori;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeKategori($query, ?string $kategori)
    {
        return $kategori ? $query->where('kategori', $kategori) : $query;
    }

    public function isLengkap(): bool
    {
        return filled($this->nomor) && filled($this->file_url);
    }
}
