<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GisValue extends Model
{
    protected $table = 'gis_values';
    protected $fillable = ['gis_layer_id', 'area_code', 'year', 'raw_value', 'value_type', 'numeric_value', 'value_min', 'value_max', 'metadata'];
    protected $casts = ['year' => 'integer', 'numeric_value' => 'float', 'value_min' => 'float', 'value_max' => 'float', 'metadata' => 'array'];

    public function layer(): BelongsTo
    {
        return $this->belongsTo(GisLayer::class, 'gis_layer_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(GisArea::class, 'area_code', 'code');
    }
}
