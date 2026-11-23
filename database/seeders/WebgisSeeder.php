<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\WebgisTheme;
class WebgisSeeder extends Seeder {
    public function run(): void {
        $data=json_decode(file_get_contents(database_path('seeders/data/webgis-seed.json')),true,512,JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($data) {
            foreach ($data['desa'] as $row) DB::table('webgis_desa')->insertOrIgnore($row);
            foreach ([['penduduk','Penduduk','jiwa',3],['stunting','Stunting','kasus',4],['rtlh','RTLH','unit rumah',5],['kemiskinan','Kemiskinan','jiwa',null]] as [$slug,$name,$unit,$col]) {
                // Existing themes and their values are never reset on rerun.
                if(WebgisTheme::where('slug',$slug)->exists()) continue;
                $t=WebgisTheme::create(['slug'=>$slug,'name'=>$name,'unit'=>$unit,'description'=>'Data awal dari berkas WebGIS; verifikasi sebelum publikasi.','source'=>'webgis_niasbarat.sql','is_public'=>false,'breaks'=>[10,20],'colors'=>['#22c55e','#f59e0b','#ef4444'],'geojson'=>$data['geojson']]);
                if ($col===null) continue;
                foreach ($data['values'] as $r) DB::table('webgis_values')->insert(['theme_id'=>$t->id,'kode_desa'=>$r[1],'year'=>$r[2],'value'=>$r[$col],'created_at'=>now(),'updated_at'=>now()]);
            }
        });
    }
}
