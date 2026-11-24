<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\User;
use App\Models\WebgisTheme;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
class WebgisIntegrationTest extends TestCase {
    protected function setUp(): void {
        parent::setUp();
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:','cache.default'=>'array','session.driver'=>'array']);
        DB::purge('sqlite');
        Schema::create('users',function(Blueprint $t){$t->id();$t->string('nama');$t->string('email');$t->string('password');$t->string('role');$t->timestamps();});
        Schema::create('periode',function(Blueprint $t){$t->id();$t->integer('tahun');$t->integer('triwulan');$t->string('status');});
        (require database_path('migrations/webgis/2026_09_09_000001_create_webgis_tables.php'))->up();
        (require database_path('migrations/2026_11_07_000001_create_webgis_education_table.php'))->up();
        $this->seed(\Database\Seeders\WebgisSeeder::class);
        $this->withoutVite();
    }
    public function test_public_map_offers_dsm_and_optional_thematic_overlay(): void {
        $this->get(route('webgis.index'))->assertOk()
            ->assertSee('id="wg-basemap"', false)
            ->assertSee('DSM ? Warna Ketinggian')
            ->assertSee('id="wg-thematic"', false)
            ->assertSee('aster-gdem-legend.svg');
    }
    private function user(string $role): User { return User::create(['nama'=>'Test','email'=>$role.'@example.test','password'=>'test-only-password','role'=>$role]); }
    public function test_seed_and_rerun_preserve_values(): void {
        $this->assertDatabaseCount('webgis_desa',105); $this->assertDatabaseCount('webgis_values',18);
        $t=WebgisTheme::where('slug','stunting')->firstOrFail();
        DB::table('webgis_values')->where('theme_id',$t->id)->update(['value'=>99]);
        $this->seed(\Database\Seeders\WebgisSeeder::class);
        $this->assertDatabaseCount('webgis_values',18);
        $this->assertDatabaseHas('webgis_values',['theme_id'=>$t->id,'value'=>99]);
    }
    public function test_access_and_draft_api(): void {
        $t=WebgisTheme::first();
        $this->get(route('webgis.admin.index'))->assertRedirect(route('login'));
        $this->getJson(route('webgis.data',$t))->assertNotFound();
        $this->actingAs($this->user('operator_opd'))->get(route('webgis.admin.index'))->assertForbidden();
        $this->getJson(route('webgis.data',$t))->assertNotFound();
        $this->actingAs($this->user('admin_bappeda'))->getJson(route('webgis.data',$t))->assertOk();
        $this->get(route('webgis.admin.edit',$t))->assertOk();
    }
    public function test_public_api_does_not_invent_zero_or_expose_notes(): void {
        $t=WebgisTheme::where('slug','kemiskinan')->firstOrFail();$t->update(['is_public'=>true]);
        $json=$this->getJson(route('webgis.data',$t))->assertOk()->json();
        $this->assertEmpty($json['values']);$this->assertArrayNotHasKey('note',$json);
        $this->getJson(route('webgis.data',$t).'?year=invalid')->assertUnprocessable();
    }
    public function test_admin_crud_and_csv_atomicity(): void {
        $this->actingAs($this->user('super_admin'));
        $this->post(route('webgis.admin.store'),['name'=>'Air minum','slug'=>'air-minum','unit'=>'%','is_public'=>0,'breaks_text'=>'40,80','colors_text'=>'#ef4444,#f59e0b,#22c55e','code_property'=>'KDEPUM'])->assertSessionHasNoErrors();
        $t=WebgisTheme::where('slug','air-minum')->firstOrFail();
        $this->post(route('webgis.admin.value',$t),['kode_desa'=>'12.25.06.2002','year'=>2026,'value'=>0,'note'=>'internal'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('webgis_values',['theme_id'=>$t->id,'value'=>0]);
        $csv=UploadedFile::fake()->createWithContent('bad.csv',"kode_desa,year,value,note\n12.25.06.2002,2026,44,valid\nBAD,2026,99,invalid\n");
        $this->post(route('webgis.admin.csv',$t),['csv'=>$csv])->assertSessionHasErrors('csv');
        $this->assertDatabaseHas('webgis_values',['theme_id'=>$t->id,'value'=>0]);
        $this->delete(route('webgis.admin.destroy',$t))->assertRedirect(route('webgis.admin.index'));
        $this->assertDatabaseMissing('webgis_values',['theme_id'=>$t->id]);
    }
}
