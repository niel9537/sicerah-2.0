<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indikator extends Model
{
    protected $table = 'indikator';
    public $timestamps = true;
    public const CREATED_AT = null;
    public const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'jenis', 'opd_id', 'urusan_id', 'sasaran_rpjmd_id',
        'kode', 'nama', 'satuan', 'sumber_data', 'keterangan', 'aspek',
        'is_iku', 'is_makro', 'bank_data_row', 'urutan',
    ];

    protected $casts = [
        'is_iku' => 'boolean',
        'is_makro' => 'boolean',
        'bank_data_row' => 'integer',
        'urutan' => 'integer',
    ];

    public function opd(): BelongsTo         { return $this->belongsTo(Opd::class, 'opd_id'); }
    public function urusan(): BelongsTo      { return $this->belongsTo(Urusan::class, 'urusan_id'); }
    public function sasaranRpjmd(): BelongsTo{ return $this->belongsTo(SasaranRpjmd::class, 'sasaran_rpjmd_id'); }

    public function targets(): HasMany       { return $this->hasMany(IndikatorTarget::class, 'indikator_id'); }
    public function realisasis(): HasMany    { return $this->hasMany(IndikatorRealisasi::class, 'indikator_id'); }

    public function scopeIku($q)
    {
        return $q->where(function ($query) {
            $query->where('jenis', 'IKU')->orWhere(function ($legacy) {
                $legacy->where('is_iku', true)->whereNotExists(function ($source) {
                    $source->selectRaw('1')->from('rpjmd_indicator_sources')->where('jenis', 'IKU');
                });
            });
        });
    }
    public function scopeIkk($q)  { return $q->where('jenis', 'IKK'); }
    public function scopeIkd($q)  { return $q->where('jenis', 'IKD'); }
    public function scopeMakro($q){ return $q->where('is_makro', true); }
    public function scopeBankData($q){ return $q->whereNotNull('bank_data_row'); }
}
