<?php
namespace App\Http\Controllers;
use App\Models\WebgisTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class WebgisController extends Controller {
    public function index() { return $this->page(false); }
    public function dashboard() { return $this->page(true); }
    private function page(bool $dashboard) {
        $q=WebgisTheme::query();
        if (!$dashboard || !in_array(auth()->user()?->role,['super_admin','admin_bappeda'],true)) $q->where('is_public',true);
        $themes=$q->orderBy('name')->get(['id','name']);
        return view('webgis.map',compact('themes','dashboard'));
    }
    public function data(Request $r, WebgisTheme $theme) {
        abort_unless($theme->is_public || in_array($r->user()?->role,['super_admin','admin_bappeda'],true),404);
        $input=$r->validate(['year'=>'nullable|integer|between:1900,2200']);
        $years=DB::table('webgis_values')->where('theme_id',$theme->id)->distinct()->orderByDesc('year')->pluck('year');
        $year=(int)($input['year']??$years->first()??date('Y'));
        $values=DB::table('webgis_values')->where('theme_id',$theme->id)->where('year',$year)->pluck('value','kode_desa');
        // Only whitelisted public fields; notes are deliberately admin-only.
        return response()->json(['theme'=>$theme->only(['id','name','unit','description','source','breaks','colors']), 'geojson'=>$theme->geojson,'desa'=>DB::table('webgis_desa')->get(), 'values'=>$values,'years'=>$years,'year'=>$year,'education'=>DB::table('webgis_education')->get()->keyBy('kode_kecamatan')])->header('Cache-Control','private, no-store');
    }
}
