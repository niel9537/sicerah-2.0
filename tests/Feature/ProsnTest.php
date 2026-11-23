<?php

namespace Tests\Feature;

use App\Models\Prosn;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class ProsnTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array', 'session.driver' => 'array']);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('nama');
            $t->string('email');
            $t->string('password');
            $t->string('role');
            $t->timestamps();
        });
        Schema::create('periode', function (Blueprint $t) {
            $t->id();
            $t->integer('tahun');
            $t->integer('triwulan');
            $t->string('status');
        });
        (require database_path('migrations/2026_11_22_000001_create_emonev_prosn_table.php'))->up();
        (require database_path('migrations/2026_11_22_000002_add_prosn_validation.php'))->up();
        $this->withoutVite();
    }

    private function loginAs(string $role): void
    {
        $this->actingAs(User::create(['nama' => 'Tester', 'email' => $role.'@example.test', 'password' => 'test', 'role' => $role]));
    }

    public function test_every_source_cell_and_hierarchy_is_preserved(): void
    {
        $source = json_decode(file_get_contents(database_path('data/prosn_2026.json')), true);
        $this->assertDatabaseCount('emonev_prosn', 82);
        foreach ($source as $row) {
            $stored = Prosn::where('baris', $row['baris'])->firstOrFail();
            foreach ($row as $key => $value) {
                $this->assertEquals($value, $stored->$key, 'Mismatch at row '.$row['baris'].' '.$key);
            }
        }
        $this->assertEquals(67, Prosn::where('jenis', 'indikator')->count());
        $this->assertEquals(56, Prosn::where('status', 'Sudah Sesuai')->count());
        $this->assertEquals(11, Prosn::where('status', 'Belum Lapor')->count());
    }

    public function test_dashboard_filters_and_permissions(): void
    {
        $this->get(route('prosn.index'))->assertRedirect(route('login'));
        $this->loginAs('pimpinan');
        $this->get(route('prosn.index'))->assertOk()->assertSee('eMonev ProSN Tahun 2026')->assertSee('Distribusi status pelaporan')->assertSee('A.1.1')->assertSee('A.1.5')->assertDontSee('A.1.6');
        $this->get(route('prosn.index', ['page' => 14]))->assertOk()->assertSee('E.3.6');
        $this->get(route('prosn.index', ['tema' => 'A', 'status' => 'Belum Lapor']))->assertOk()->assertSee('A.2.4')->assertSee('A.3.5')->assertDontSee('B.1.3');
        $this->get(route('prosn.index', ['q' => 'not-a-real-code']))->assertOk()->assertSee('Tidak ada butir');
        $this->get(route('prosn.index', ['q' => 'A.1.1']))->assertOk()->assertSee('A.1.1')->assertDontSee('A.1.2');
        $row = Prosn::where('kode', 'A.1.1')->first();
        $this->get(route('prosn.edit', $row))->assertForbidden();
        $this->put(route('prosn.update', $row), ['status' => 'Belum Lapor'])->assertForbidden();
    }

    public function test_admin_updates_and_validates_links_and_status(): void
    {
        $this->loginAs('super_admin');
        $row = Prosn::where('kode', 'A.1.1')->first();
        $this->get(route('prosn.edit', $row))->assertOk()->assertSee('Simpan perubahan');
        $data = ['uraian' => $row->uraian, 'indikator' => $row->indikator, 'status' => 'Belum Lapor', 'ketersediaan' => 'Belum ditemukan', 'bukti_1' => 'Dokumen baru', 'tautan' => [1 => 'https://example.com/evidence'], 'catatan' => 'Tindak lanjut'];
        $this->put(route('prosn.update', $row), $data)->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('emonev_prosn', ['id' => $row->id, 'status' => 'Belum Lapor', 'catatan' => 'Tindak lanjut']);
        $this->put(route('prosn.update', $row), array_replace($data, ['tautan' => [1 => 'javascript:alert(1)']]))->assertSessionHasErrors('tautan.1');
        $this->put(route('prosn.update', $row), array_replace($data, ['status' => 'invalid']))->assertSessionHasErrors('status');
        $this->get(route('prosn.index'))->assertOk()->assertSee('https://example.com/evidence');
    }

    public function test_export_preserves_all_columns_and_does_not_execute_formulas(): void
    {
        $this->loginAs('admin_bappeda');
        $row = Prosn::where('kode', 'A.1.1')->first();
        $row->update(['catatan' => '=1+1', 'tautan' => [1 => 'https://example.com/evidence']]);
        $response = $this->get(route('prosn.export'))->assertOk()->assertDownload('eMonev_ProSN_2026.xlsx');
        $file = tempnam(sys_get_temp_dir(), 'prosn');
        try {
            file_put_contents($file, $response->streamedContent());
            $book = IOFactory::load($file);
            $sheet = $book->getActiveSheet();
            $this->assertEquals(83, $sheet->getHighestRow());
            $this->assertEquals('BUKTI DUKUNG', $sheet->getCell('F1')->getValue());
            $this->assertEquals('VALIDASI INSPEKTORAT', $sheet->getCell('I1')->getValue());
            $this->assertEquals('Menunggu validasi', $sheet->getCell('I4')->getValue());
            $this->assertEquals('landscape', $sheet->getPageSetup()->getOrientation());
            $this->assertEquals(1, $sheet->getPageSetup()->getFitToWidth());
            $this->assertNotNull($book->getSheetByName('Riwayat Validasi'));
            $this->assertEquals('KETERSEDIAAN DOKUMEN', $sheet->getCell('G1')->getValue());
            $this->assertEquals('=1+1', $sheet->getCell('H4')->getValue());
            $this->assertEquals('s', $sheet->getCell('H4')->getDataType());
            $this->assertEquals('https://example.com/evidence', $sheet->getCell('F4')->getHyperlink()->getUrl());
            $book->disconnectWorksheets();
        } finally {
            unlink($file);
        }
    }

    public function test_inspektorat_can_only_access_emonev_and_export(): void
    {
        $this->loginAs('inspektorat');
        $this->get(route('dashboard'))->assertRedirect(route('prosn.index'));
        $this->get(route('prosn.index'))->assertOk()->assertSee('Validasi / Riwayat')->assertDontSee('Bukti Dukung 2')->assertDontSee('Bukti Dukung 3')->assertDontSee('Profil Saya')->assertDontSee('Lihat Portal Publik')->assertDontSee('Evaluasi Renja')->assertDontSee('Form 81');
        foreach (['realisasi.index', 'form81.index', 'user.index', 'opd.index', 'evaluasi.index', 'profile.edit', 'webgis.index'] as $route) {
            $this->get(route($route))->assertForbidden();
        }
        $row = Prosn::where('kode', 'A.1.1')->first();
        $this->get(route('prosn.edit', $row))->assertForbidden();
        $this->put(route('prosn.update', $row), ['uraian' => 'Tampered'])->assertForbidden();
        $this->get('/indikator')->assertForbidden();
        $this->get(route('prosn.export'))->assertOk()->assertDownload();
    }

    public function test_inspektorat_login_ignores_intended_other_module(): void
    {
        $user = User::create(['nama' => 'Inspektorat', 'email' => 'inspektorat@example.test', 'password' => 'ValidPassword123', 'role' => 'inspektorat']);
        $this->withSession(['url.intended' => route('user.index')])->post(route('login'), ['email' => $user->email, 'password' => 'ValidPassword123'])->assertRedirect(route('prosn.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_validation_records_identity_time_history_and_requires_revalidation_after_edits(): void
    {
        $this->loginAs('inspektorat');
        $row = Prosn::where('kode', 'A.1.1')->first();
        $this->get(route('prosn.validasi', $row))->assertOk()->assertSee('Validator:')->assertSee('Tester');
        $this->post(route('prosn.validate', $row), ['versi' => 1, 'keputusan' => 'revisi'])->assertSessionHasErrors('catatan');
        $this->post(route('prosn.validate', $row), ['versi' => 1, 'keputusan' => 'invalid'])->assertSessionHasErrors('keputusan');
        $this->travelTo(Carbon::parse('2026-11-22 10:11:12', 'Asia/Jakarta'));
        $this->post(route('prosn.validate', $row), ['versi' => 1, 'keputusan' => 'revisi', 'catatan' => 'Lengkapi dokumen', 'user_id' => 999, 'nama_validator' => 'Forged', 'validated_at' => '2000-01-01'])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('emonev_prosn_validasi', ['prosn_id' => $row->id, 'user_id' => auth()->id(), 'nama_validator' => 'Tester', 'validated_at' => '2026-11-22 10:11:12', 'keputusan' => 'revisi']);
        $this->assertEquals('Revisi', $row->fresh()->status_validasi);
        $this->post(route('prosn.validate', $row), ['versi' => 1, 'keputusan' => 'setuju'])->assertSessionHasNoErrors();
        $this->assertEquals('Setuju', $row->fresh()->status_validasi);
        $this->assertDatabaseCount('emonev_prosn_validasi', 2);
        $this->get(route('prosn.validasi', $row))->assertOk()->assertSee('22-11-2026 10:11:12')->assertSee('Lengkapi dokumen');
        $this->loginAs('admin_bappeda');
        $this->post(route('prosn.validate', $row), ['versi' => 1, 'keputusan' => 'setuju'])->assertForbidden();
        $this->put(route('prosn.update', $row), ['uraian' => $row->uraian, 'indikator' => $row->indikator, 'status' => $row->status, 'ketersediaan' => $row->ketersediaan, 'bukti_1' => 'Updated evidence'])->assertSessionHasNoErrors();
        $this->assertEquals('Menunggu validasi', $row->fresh()->status_validasi);
        $this->assertEquals(2, $row->fresh()->versi);
        $this->loginAs('inspektorat');
        $this->post(route('prosn.validate', $row), ['versi' => 1, 'keputusan' => 'setuju'])->assertSessionHasErrors('versi');
        $this->assertDatabaseCount('emonev_prosn_validasi', 2);
        $theme = Prosn::where('jenis', 'tema')->first();
        $this->get(route('prosn.validasi', $theme))->assertNotFound();
        $this->post(route('prosn.validate', $theme), ['versi' => 1, 'keputusan' => 'setuju'])->assertNotFound();
        $this->travelBack();
    }

    public function test_drive_links_are_imported_without_overwriting_admin_edits(): void
    {
        $rows = Prosn::all();
        $this->assertCount(61, $rows->filter(fn ($r) => ! empty($r->tautan[1])));
        $this->assertEquals('https://drive.google.com/file/d/1BuXOMkoOk9kg-CrFGTH3EZgEo5w8PzJK/view', Prosn::where('kode', 'A.1.1')->first()->tautan[1]);
        $row = Prosn::where('kode', 'A.1.1')->first();
        $row->update(['tautan' => []]);
        $migration = require database_path('migrations/2026_11_22_000003_import_prosn_drive_links.php');
        $migration->up();
        $this->assertStringContainsString('drive.google.com', $row->fresh()->tautan[1]);
        $this->assertEquals(2, $row->fresh()->versi);
        $row->update(['tautan' => [1 => 'https://drive.google.com/drive/folders/admin-updated']]);
        $migration->up();
        $this->assertEquals('https://drive.google.com/drive/folders/admin-updated', $row->fresh()->tautan[1]);
        $this->assertEquals(2, $row->fresh()->versi);
    }

    public function test_pagination_has_five_indicators_and_repeats_group_headings(): void
    {
        $this->loginAs('pimpinan');
        foreach ([1, 2, 3, 5, 6, 7, 14] as $page) {
            $response = $this->get(route('prosn.index', ['page' => $page]))->assertOk();
            $pagination = $response->viewData('pagination');
            $items = $pagination->getCollection();
            $this->assertCount($page === 14 ? 2 : 5, $items);
            $this->assertEquals(67, $pagination->total());
            $rows = $response->viewData('rows');
            $this->assertCount($items->count(), $rows->where('jenis', 'indikator'));
            foreach ($items as $item) {
                $this->assertTrue($rows->contains(fn ($r) => $r->jenis === 'tema' && $r->tema === $item->tema));
                $this->assertTrue($rows->contains(fn ($r) => $r->jenis === 'program' && $r->tema === $item->tema && $r->program === $item->program));
            }
        }
        $response = $this->get(route('prosn.index', ['tema' => 'C', 'status' => 'Sudah Sesuai', 'page' => 2]))->assertOk();
        $this->assertCount(5, $response->viewData('pagination')->getCollection());
        $response->assertSee('tema=C')->assertSee('status=Sudah%20Sesuai');
        $this->get(route('prosn.index', ['page' => 999]))->assertOk()->assertSee('E.3.6');
        $this->get(route('prosn.index', ['page' => 0]))->assertSessionHasErrors('page');
    }
}
