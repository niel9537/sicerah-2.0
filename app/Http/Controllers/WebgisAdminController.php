<?php
namespace App\Http\Controllers;
use App\Models\WebgisTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
class WebgisAdminController extends Controller {
    public function index() { return view('webgis.admin.index',['themes'=>WebgisTheme::orderBy('name')->get(),'theme'=>new WebgisTheme]); }
    public function edit(WebgisTheme $theme) {
        return view('webgis.admin.edit',['theme'=>$theme,'desa'=>DB::table('webgis_desa')->orderBy('nama_desa')->get(),'values'=>DB::table('webgis_values')->where('theme_id',$theme->id)->orderByDesc('year')->orderBy('kode_desa')->paginate(30)]);
    }
    public function store(Request $r) { $t=WebgisTheme::create($this->validated($r)); return redirect()->route('webgis.admin.edit',$t)->with('success','Tema dibuat sebagai draf atau publik sesuai pilihan Anda.'); }
    public function update(Request $r, WebgisTheme $theme) { $theme->update($this->validated($r,$theme)); return back()->with('success','Tema disimpan.'); }
    public function destroy(WebgisTheme $theme) { $theme->delete(); return redirect()->route('webgis.admin.index')->with('success','Tema dan nilai terkait dihapus.'); }
    private function fail(string $key,string $message): never { throw ValidationException::withMessages([$key=>$message]); }
    private function validated(Request $r, ?WebgisTheme $theme=null): array {
        $a=$r->validate(['name'=>'required|string|max:150','slug'=>['required','alpha_dash','max:100',Rule::unique('webgis_themes','slug')->ignore($theme?->id)],'unit'=>'required|string|max:40','description'=>'nullable|string|max:3000','source'=>'nullable|string|max:255','is_public'=>'required|boolean','breaks_text'=>'required|string|max:300','colors_text'=>'required|string|max:300','geojson'=>'nullable|file|max:10240','code_property'=>'required|string|max:80']);
        $breaks=array_map('trim',explode(',',$a['breaks_text']));
        if(count($breaks)>8) $this->fail('breaks_text','Maksimal 8 batas.');
        foreach($breaks as $i=>$b) if(!is_numeric($b)||!is_finite((float)$b)||($i && (float)$b<=(float)$breaks[$i-1])) $this->fail('breaks_text','Batas harus angka berurutan naik, dipisah koma.');
        $colors=array_map('trim',explode(',',$a['colors_text']));
        if(count($colors)!==count($breaks)+1) $this->fail('colors_text','Jumlah warna harus jumlah batas + 1.');
        foreach($colors as $c) if(!preg_match('/^#[0-9a-fA-F]{6}$/',$c)) $this->fail('colors_text','Warna harus #RRGGBB.');
        $g=$theme?->geojson;
        if($r->hasFile('geojson')) {
            try { $g=json_decode(file_get_contents($r->file('geojson')->getRealPath()),true,64,JSON_THROW_ON_ERROR); }
            catch(\Throwable $e) { $this->fail('geojson','JSON tidak valid.'); }
            if(($g['type']??null)!=='FeatureCollection'||empty($g['features'])||!is_array($g['features'])||count($g['features'])>2000) $this->fail('geojson','Gunakan FeatureCollection Polygon/MultiPolygon (maksimal 2000 fitur).');
            if(isset($g['crs'])&&!preg_match('/4326|CRS84/',json_encode($g['crs']))) $this->fail('geojson','Ekspor ulang ke EPSG:4326.');
            $known=DB::table('webgis_desa')->pluck('kode_desa')->all(); $seen=[]; $clean=[];
            foreach($g['features'] as $f) {
                $code=$f['properties'][$a['code_property']]??null; $geometry=$f['geometry']??[];
                if(!is_string($code)||!in_array($code,$known,true)||isset($seen[$code])) $this->fail('geojson','Kode desa tidak dikenal/duplikat atau atribut kode tidak ditemukan.');
                $seen[$code]=true;
                if(!in_array($geometry['type']??null,['Polygon','MultiPolygon'],true)) $this->fail('geojson','Geometri harus Polygon atau MultiPolygon.');
                $polygons=$geometry['type']==='Polygon'?[$geometry['coordinates']??[]]:($geometry['coordinates']??[]);
                if(!is_array($polygons)||!count($polygons)) $this->fail('geojson','Koordinat kosong.');
                foreach($polygons as $polygon) {
                    if(!is_array($polygon)||!count($polygon)) $this->fail('geojson','Polygon kosong.');
                    foreach($polygon as $ring) {
                        if(!is_array($ring)||count($ring)<4||$ring[0]!==$ring[count($ring)-1]) $this->fail('geojson','Ring polygon harus tertutup dengan minimal 4 koordinat.');
                        foreach($ring as $pos) if(!is_array($pos)||count($pos)<2||!is_numeric($pos[0])||!is_numeric($pos[1])||abs((float)$pos[0])>180||abs((float)$pos[1])>90) $this->fail('geojson','Koordinat longitude/latitude tidak valid (EPSG:4326).');
                    }
                }
                $clean[]=['type'=>'Feature','properties'=>['kode_desa'=>$code],'geometry'=>$geometry];
            }
            $existing=$theme?DB::table('webgis_values')->where('theme_id',$theme->id)->distinct()->pluck('kode_desa')->all():[];
            if(array_diff($existing,array_keys($seen))) $this->fail('geojson','Peta baru tidak mencakup desa yang sudah memiliki nilai. Hapus nilai terkait terlebih dahulu.');
            $g=['type'=>'FeatureCollection','features'=>$clean];
        }
        if(!$g) $g=json_decode(file_get_contents(database_path('seeders/data/webgis-seed.json')),true)['geojson'];
        return ['name'=>$a['name'],'slug'=>$a['slug'],'unit'=>$a['unit'],'description'=>$a['description']??null,'source'=>$a['source']??null,'is_public'=>(bool)$a['is_public'],'breaks'=>array_map('floatval',$breaks),'colors'=>$colors,'geojson'=>$g];
    }
    private function validateValue(array $row,WebgisTheme $theme): array {
        $a=Validator::make($row,['kode_desa'=>['required','string',Rule::in(array_column(array_column($theme->geojson['features'],'properties'),'kode_desa'))],'year'=>'required|integer|between:1900,2200','value'=>'required|numeric|between:-9999999999999,9999999999999','note'=>'nullable|string|max:500'])->validate();
        return $a;
    }
    private function saveValue(array $a,WebgisTheme $theme): void {
        DB::table('webgis_values')->upsert([array_merge($a,['theme_id'=>$theme->id,'created_at'=>now(),'updated_at'=>now()])],['theme_id','kode_desa','year'],['value','note','updated_at']);
    }
    public function value(Request $r,WebgisTheme $theme) { $this->saveValue($this->validateValue($r->only(['kode_desa','year','value','note']),$theme),$theme); return back()->with('success','Nilai disimpan (kode desa + tahun yang sama diperbarui).'); }
    public function deleteValue(WebgisTheme $theme,int $value) { abort_unless(DB::table('webgis_values')->where('theme_id',$theme->id)->where('id',$value)->delete(),404); return back()->with('success','Nilai dihapus; desa kembali berstatus belum ada data.'); }
    public function csv(Request $r,WebgisTheme $theme) {
        $r->validate(['csv'=>'required|file|max:2048']);
        $h=fopen($r->file('csv')->getRealPath(),'r'); $rows=[]; $keys=[];
        try {
            $line=fgets($h); if($line===false) $this->fail('csv','CSV kosong.');
            $delimiter=substr_count($line,';')>substr_count($line,',')?';':',';
            rewind($h); $header=fgetcsv($h,0,$delimiter,'"','');
            $header[0]=preg_replace('/^\xEF\xBB\xBF/','',$header[0]);
            if($header!==['kode_desa','year','value','note']) $this->fail('csv','Header wajib: kode_desa,year,value,note');
            $n=1;
            while(($row=fgetcsv($h,0,$delimiter,'"',''))!==false) {
                $n++; if($row===[null]) continue;
                if(count($row)!==4||count($rows)>=10000) $this->fail('csv',"Baris $n tidak valid atau melebihi 10000 baris.");
                try { $a=$this->validateValue(array_combine($header,$row),$theme); }
                catch(ValidationException $e) { $this->fail('csv',"Baris $n: ".implode(' ', $e->validator->errors()->all())); }
                $key=$a['kode_desa'].'/'.$a['year']; if(isset($keys[$key])) $this->fail('csv',"Baris $n: kode desa/tahun duplikat dalam CSV.");
                $keys[$key]=true; $rows[]=$a;
            }
        } finally { fclose($h); }
        if(!$rows) $this->fail('csv','Tidak ada baris data.');
        DB::transaction(function() use($rows,$theme) { foreach($rows as $a) $this->saveValue($a,$theme); });
        return back()->with('success',count($rows).' baris diimpor. Nilai lama dengan desa/tahun sama diperbarui.');
    }
}
