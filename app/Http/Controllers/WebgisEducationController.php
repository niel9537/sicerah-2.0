<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
class WebgisEducationController extends Controller {
    public static function districts() {
        return DB::table('webgis_desa')->select('kode_kecamatan','nama_kecamatan')->whereNotNull('kode_kecamatan')->distinct()->orderBy('nama_kecamatan')->get();
    }
    public function data() {
        $seed=json_decode(file_get_contents(database_path('seeders/data/webgis-seed.json')),true,512,JSON_THROW_ON_ERROR);
        $master=collect($seed['desa'])->keyBy('kode_desa'); $groups=[];
        foreach($seed['geojson']['features'] as $f) {
            $code=$master[$f['properties']['kode_desa']]['kode_kecamatan']??null;
            if(!$code) continue;
            $geometry=$f['geometry'];
            foreach($geometry['type']==='Polygon'?[$geometry['coordinates']]:$geometry['coordinates'] as $polygon) $groups[$code][]=$polygon;
        }
        $features=[]; foreach($groups as $code=>$polygons) $features[]=['type'=>'Feature','properties'=>['kode_desa'=>$code],'geometry'=>['type'=>'MultiPolygon','coordinates'=>$polygons]];
        $rows=DB::table('webgis_education')->get()->keyBy('kode_kecamatan');
        $districts=self::districts()->map(fn($d)=>['kode_desa'=>$d->kode_kecamatan,'nama_desa'=>$d->nama_kecamatan,'kode_kecamatan'=>$d->kode_kecamatan,'nama_kecamatan'=>$d->nama_kecamatan]);
        return response()->json(['education_mode'=>true,'theme'=>['name'=>'Pendidikan per Kecamatan','unit'=>'sekolah','breaks'=>[25,40,55],'colors'=>['#d1fae5','#6ee7b7','#10b981','#047857'],'source'=>'CSV progres Dapodik Kab. Nias Barat','description'=>'Agregat kecamatan. Pembaruan melalui CRUD atau impor CSV.'], 'geojson'=>['type'=>'FeatureCollection','features'=>$features],'desa'=>$districts,'values'=>$rows->pluck('schools','kode_kecamatan'),'years'=>[],'year'=>'Snapshot terakhir','education'=>$rows])->header('Cache-Control','private, no-store');
    }
    public function index(Request $r) {
        return view('webgis.education', ['districts'=>self::districts(), 'rows'=>DB::table('webgis_education')->get()->keyBy('kode_kecamatan'), 'editing'=>DB::table('webgis_education')->where('id',$r->query('edit'))->first()]);
    }
    private function validateRow(array $row): array {
        $rules=['kode_kecamatan'=>['required',Rule::in(self::districts()->pluck('kode_kecamatan')->all())],'source'=>'required|string|max:255'];
        foreach (['schools','students','teachers','synced','unsynced'] as $key) $rules[$key]='required|integer|min:0|max:100000000';
        $a=Validator::make($row,$rules)->validate();
        if ((int)$a['synced']+(int)$a['unsynced'] !== (int)$a['schools']) throw ValidationException::withMessages(['schools'=>'Sudah dan belum sinkronisasi harus berjumlah sama dengan jumlah sekolah.']);
        return $a;
    }
    public function store(Request $r) {
        $a=$this->validateRow($r->all());
        Validator::make($a,['kode_kecamatan'=>'unique:webgis_education,kode_kecamatan'])->validate();
        DB::table('webgis_education')->insert($a+['created_at'=>now(),'updated_at'=>now()]);
        return redirect()->route('webgis.education.index')->with('success','Data pendidikan ditambahkan.');
    }
    public function update(Request $r, int $education) {
        abort_unless(DB::table('webgis_education')->where('id',$education)->exists(),404);
        $a=$this->validateRow($r->all());
        Validator::make($a,['kode_kecamatan'=>Rule::unique('webgis_education')->ignore($education)])->validate();
        DB::table('webgis_education')->where('id',$education)->update($a+['updated_at'=>now()]);
        return redirect()->route('webgis.education.index')->with('success','Data pendidikan diperbarui.');
    }
    public function destroy(int $education) {
        abort_unless(DB::table('webgis_education')->where('id',$education)->delete(),404);
        return back()->with('success','Data pendidikan dihapus.');
    }
    public function csv(Request $r) {
        $r->validate(['csv'=>'required|file|max:2048']);
        $h=fopen($r->file('csv')->getRealPath(),'r'); $rows=[]; $seen=[];
        $normalize=fn($v)=>preg_replace('/[^a-z0-9]/','',strtolower(preg_replace('/^Kec\.\s*/i','',trim($v))));
        $districts=[]; foreach(self::districts() as $d) $districts[$normalize($d->nama_kecamatan)]=$d->kode_kecamatan;
        try {
            $header=fgetcsv($h,0,',','"','');
            if ($header) $header[0]=preg_replace('/^\xEF\xBB\xBF/','',$header[0]);
            if ($header!==['No.','Kecamatan','Jumlah Sekolah','Jumlah PD','Jumlah GTK','Sudah Sinkronisasi','Belum Sinkronisasi','Persentase (%)']) throw ValidationException::withMessages(['csv'=>'Header CSV harus sesuai ekspor progres Dapodik.']);
            $n=1;
            while (($row=fgetcsv($h,0,',','"',''))!==false) {
                $n++; if ($row===[null]) continue;
                try {
                    if (count($row)!==8 || count($rows)>=100) throw new \RuntimeException('Format atau jumlah baris tidak valid.');
                    $code=$districts[$normalize($row[1])]??null;
                    $a=$this->validateRow(['kode_kecamatan'=>$code,'schools'=>$row[2],'students'=>$row[3],'teachers'=>$row[4],'synced'=>$row[5],'unsynced'=>$row[6],'source'=>'CSV progres Dapodik Kab. Nias Barat']);
                    if(isset($seen[$code])) throw new \RuntimeException('Kecamatan duplikat.');
                    if(!is_numeric($row[7]) || abs((float)$row[7]-($a['schools'] ? 100*$a['synced']/$a['schools'] : 0))>0.11) throw new \RuntimeException('Persentase tidak sesuai jumlah sekolah.');
                    $seen[$code]=true; $rows[]=$a+['created_at'=>now(),'updated_at'=>now()];
                } catch (\Throwable $e) { throw ValidationException::withMessages(['csv'=>"Baris $n: ".$e->getMessage()]); }
            }
        } finally { fclose($h); }
        if(!$rows) throw ValidationException::withMessages(['csv'=>'CSV tidak berisi data.']);
        DB::transaction(fn()=>DB::table('webgis_education')->upsert($rows,['kode_kecamatan'],['schools','students','teachers','synced','unsynced','source','updated_at']));
        return back()->with('success',count($rows).' kecamatan diimpor. Data kecamatan yang sama diperbarui.');
    }
}
