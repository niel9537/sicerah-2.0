<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WebgisTheme extends Model {
    protected $table = 'webgis_themes';
    protected $fillable = ['slug','name','unit','description','source','is_public','breaks','colors','geojson'];
    protected function casts(): array { return ['is_public'=>'boolean','breaks'=>'array','colors'=>'array','geojson'=>'array']; }
}
