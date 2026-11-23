<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class WebgisEducationSeeder extends Seeder {
    public function run(): void {
        $normalize=fn($v)=>preg_replace('/[^a-z0-9]/','',strtolower(preg_replace('/^Kec\.\s*/i','',trim($v))));
        $districts=[]; foreach(\App\Http\Controllers\WebgisEducationController::districts() as $d) $districts[$normalize($d->nama_kecamatan)]=$d->kode_kecamatan;
        $h=fopen(database_path('seeders/data/education-nias-barat.csv'),'r'); $rows=[];
        try {
            fgetcsv($h,0,',','"','');
            while(($r=fgetcsv($h,0,',','"',''))!==false) {
                if($r===[null]) continue;
                $code=$districts[$normalize($r[1])]??null;
                if(!$code) throw new \RuntimeException('Kecamatan tidak ditemukan: '.$r[1]);
                $rows[]=['kode_kecamatan'=>$code,'schools'=>(int)$r[2],'students'=>(int)$r[3],'teachers'=>(int)$r[4],'synced'=>(int)$r[5],'unsynced'=>(int)$r[6],'source'=>'CSV progres Dapodik Kab. Nias Barat (tanggal sumber tidak tercantum)','created_at'=>now(),'updated_at'=>now()];
            }
        } finally { fclose($h); }
        DB::transaction(fn()=>DB::table('webgis_education')->insertOrIgnore($rows));
    }
}
