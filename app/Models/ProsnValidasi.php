<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProsnValidasi extends Model
{
    protected $table = 'emonev_prosn_validasi';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['validated_at' => 'datetime'];
    }
}
