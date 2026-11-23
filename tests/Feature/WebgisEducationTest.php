<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
class WebgisEducationTest extends TestCase {
    protected function setUp(): void {
        parent::setUp();
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:','cache.default'=>'array','session.driver'=>'array']); DB::purge('sqlite');
        Schema::create('users',function(Blueprint $t){$t->id();$t->string('nama');$t->string('email');$t->string('password');$t->string('role');$t->timestamps();});
        Schema::create('periode',function(Blueprint $t){$t->id();$t->integer('tahun');$t->integer('triwulan');$t->string('status');});
        (require database_path('migrations/webgis/2026_09_09_000001_create_webgis_tables.php'))->up();
        (require database_path('migrations/2026_11_07_000001_create_webgis_education_table.php'))->up();
        $this->seed(\Database\Seeders\WebgisSeeder::class); $this->withoutVite();
    }
    private function admin(): void { $this->actingAs(User::create(['nama'=>'Admin','email'=>'admin@example.test','password'=>'test','role'=>'super_admin'])); }
    public function test_crud_and_public_district_data(): void {
        $this->get(route('webgis.education.index'))->assertRedirect(route('login')); $this->admin();
        $a=['kode_kecamatan'=>'12.25.01','schools'=>10,'students'=>100,'teachers'=>20,'synced'=>9,'unsynced'=>1,'source'=>'Test'];
        $this->post(route('webgis.education.store'),$a)->assertSessionHasNoErrors();
        $id=DB::table('webgis_education')->value('id');
        $this->get(route('webgis.education.index',['edit'=>$id]))->assertOk()->assertSee('Ubah data kecamatan');
        $this->put(route('webgis.education.update',$id),array_replace($a,['students'=>200]))->assertSessionHasNoErrors();
        $json=$this->getJson(route('webgis.education.data'))->assertOk()->assertJsonCount(8,'geojson.features')->json();
        $this->assertEquals(200,$json['education']['12.25.01']['students']);
        $this->post(route('webgis.education.store'),array_replace($a,['synced'=>11]))->assertSessionHasErrors('schools');
        $this->delete(route('webgis.education.destroy',$id))->assertSessionHasNoErrors(); $this->assertDatabaseCount('webgis_education',0);
    }
    public function test_csv_normalization_and_atomic_validation(): void {
        $this->admin();
        $header="No.,Kecamatan,Jumlah Sekolah,Jumlah PD,Jumlah GTK,Sudah Sinkronisasi,Belum Sinkronisasi,Persentase (%)\n";
        $this->post(route('webgis.education.csv'),['csv'=>UploadedFile::fake()->createWithContent('data.csv',$header."1,Kec. Moro o,56,2951,760,52,4,92.9\n2,Kec. Ulu Moro o,21,1660,405,21,0,100\n")])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('webgis_education',2);
        $this->post(route('webgis.education.csv'),['csv'=>UploadedFile::fake()->createWithContent('data.csv',$header."1,Kec. Lahomi,10,100,20,9,1,90\n2,Unknown,10,100,20,9,1,90\n")])->assertSessionHasErrors('csv');
        $this->assertDatabaseCount('webgis_education',2);
    }
    public function test_initial_csv_and_seeder_preserve_edits(): void {
        $this->seed(\Database\Seeders\WebgisEducationSeeder::class);
        $this->assertDatabaseCount('webgis_education',8);
        $this->assertEquals(344,DB::table('webgis_education')->sum('schools'));
        DB::table('webgis_education')->where('kode_kecamatan','12.25.01')->update(['students'=>123]);
        $this->seed(\Database\Seeders\WebgisEducationSeeder::class);
        $this->assertDatabaseHas('webgis_education',['kode_kecamatan'=>'12.25.01','students'=>123]);
    }
    public function test_operator_cannot_manage_education(): void {
        $this->actingAs(User::create(['nama'=>'Operator','email'=>'op@example.test','password'=>'test','role'=>'operator_opd']));
        $this->get(route('webgis.education.index'))->assertForbidden();
        $this->post(route('webgis.education.store'),[])->assertForbidden();
    }
}
