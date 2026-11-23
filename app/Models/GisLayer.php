<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GisLayer extends Model
{
    protected $table = 'gis_layers';
    protected $fillable = ['name', 'slug', 'description', 'unit', 'geojson_path', 'color_from', 'color_to', 'breakpoints', 'is_active', 'is_public', 'sort_order'];
    protected $casts = ['breakpoints' => 'array', 'is_active' => 'boolean', 'is_public' => 'boolean', 'sort_order' => 'integer'];

    public function values(): HasMany
    {
        return $this->hasMany(GisValue::class);
    }
}
