<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GisArea extends Model
{
    protected $table = 'gis_areas';
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['code', 'name', 'district_code', 'district_name'];

    public function values(): HasMany
    {
        return $this->hasMany(GisValue::class, 'area_code', 'code');
    }
}
