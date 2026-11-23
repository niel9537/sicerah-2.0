<?php

namespace Tests\Feature;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array', 'session.driver' => 'array']);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $table) {
            $table->id(); $table->string('nama'); $table->string('email');
            $table->string('password'); $table->string('role'); $table->timestamps();
        });
        Schema::create('periode', function (Blueprint $table) {
            $table->id(); $table->integer('tahun'); $table->integer('triwulan'); $table->string('status');
        });
        (require database_path('migrations/2026_11_22_000004_create_publications_table.php'))->up();
        $this->withoutVite();
        $this->travelTo(now()->setDate(2026, 11, 22)->setTime(12, 0));
    }

    protected function tearDown(): void
    {
        $this->travelBack();
        parent::tearDown();
    }

    private function loginAs(string $role): void
    {
        $this->actingAs(User::create(['nama' => 'Tester', 'email' => $role.'@example.test', 'password' => 'test', 'role' => $role]));
    }

    private function data(array $override = []): array
    {
        return array_merge(['judul' => 'Berita pembangunan', 'kategori' => 'Kegiatan', 'ringkasan' => 'Ringkasan berita', 'konten' => "Paragraf pertama.\n\nParagraf kedua.", 'is_published' => 1, 'published_at' => now()->format('Y-m-d H:i:s')], $override);
    }

    public function test_cms_is_exclusively_for_superadmin(): void
    {
        $publication = Publication::create($this->data());
        $this->get(route('cms.publications.index'))->assertRedirect(route('login'));
        foreach (['admin_bappeda', 'operator_opd', 'pimpinan', 'verifikator', 'inspektorat'] as $role) {
            $this->loginAs($role);
            $this->get(route('cms.publications.index'))->assertForbidden();
            $this->get(route('cms.publications.create'))->assertForbidden();
            $this->get(route('cms.publications.edit', $publication))->assertForbidden();
            $this->post(route('cms.publications.store'), $this->data())->assertForbidden();
            $this->put(route('cms.publications.update', $publication), $this->data())->assertForbidden();
            $this->delete(route('cms.publications.destroy', $publication))->assertForbidden();
        }
        $this->loginAs('super_admin');
        $this->get(route('cms.publications.index'))->assertOk()->assertSee('Kelola Publikasi');
        $this->get(route('cms.publications.create'))->assertOk();
        $this->get(route('cms.publications.edit', $publication))->assertOk();
    }

    public function test_carousel_only_contains_three_latest_publications_from_last_seven_days(): void
    {
        Publication::create($this->data(['judul' => 'Lama', 'published_at' => now()->subDays(7)->subSecond()]));
        Publication::create($this->data(['judul' => 'Draf', 'is_published' => 0]));
        Publication::create($this->data(['judul' => 'Besok', 'published_at' => now()->addDay()]));
        foreach ([6, 4, 2, 0] as $days) Publication::create($this->data(['judul' => 'Hari '.$days, 'published_at' => now()->subDays($days)]));
        $slides = Publication::forCarousel()->get();
        $this->assertSame(['Hari 0', 'Hari 2', 'Hari 4'], $slides->pluck('judul')->all());
        $html = view('landing._portal-hero', ['carouselPublications' => $slides])->render();
        $this->assertSame(3, substr_count($html, 'class="portal-slide"'));
        $this->assertStringNotContainsString('Hari 6', $html);
        $this->assertStringContainsString(route('publik.publikasi.show', $slides->first()), $html);
        Publication::query()->delete();
        Publication::create($this->data(['judul' => 'Batas tujuh hari', 'published_at' => now()->subDays(7)]));
        $this->assertCount(1, Publication::forCarousel()->get());
        $this->travel(1)->seconds();
        $this->assertCount(0, Publication::forCarousel()->get());
        $html = view('landing._portal-hero', ['carouselPublications' => Publication::forCarousel()->get()])->render();
        $this->assertStringContainsString('Nias Barat Semakin Cerah', $html);
        $this->assertStringNotContainsString('Sorotan berikutnya', $html);
    }

    public function test_public_pages_hide_drafts_and_future_posts_but_keep_archived_news(): void
    {
        $old = Publication::create($this->data(['judul' => 'Arsip lama', 'published_at' => now()->subDays(30)]));
        $draft = Publication::create($this->data(['judul' => 'Rahasia draf', 'is_published' => 0]));
        $future = Publication::create($this->data(['judul' => 'Rahasia jadwal', 'published_at' => now()->addDay()]));
        $this->get(route('publik.publikasi'))->assertOk()->assertSee('Arsip lama')->assertDontSee('Rahasia draf')->assertDontSee('Rahasia jadwal');
        $this->get(route('publik.publikasi.show', $old))->assertOk()->assertSee('Paragraf pertama.');
        $this->get(route('publik.publikasi.show', $draft))->assertNotFound();
        $this->get(route('publik.publikasi.show', $future))->assertNotFound();
        $this->travel(1)->days();
        $this->get(route('publik.publikasi.show', $future))->assertOk();
    }

    public function test_cms_saves_uploads_replaces_covers_unpublishes_and_deletes(): void
    {
        Storage::fake('public');
        $this->loginAs('super_admin');
        $this->post(route('cms.publications.store'), $this->data(['cover' => UploadedFile::fake()->image('news.jpg')]))->assertSessionHasNoErrors()->assertRedirect(route('cms.publications.index'));
        $publication = Publication::firstOrFail();
        $firstCover = $publication->cover_path;
        Storage::disk('public')->assertExists($firstCover);
        $this->put(route('cms.publications.update', $publication), $this->data(['judul' => 'Direvisi', 'is_published' => 0, 'cover' => UploadedFile::fake()->image('new.png')]))->assertSessionHasNoErrors()->assertRedirect();
        $publication->refresh();
        $this->assertSame('Direvisi', $publication->judul);
        Storage::disk('public')->assertMissing($firstCover);
        Storage::disk('public')->assertExists($publication->cover_path);
        $this->get(route('publik.publikasi.show', $publication))->assertNotFound();
        $this->delete(route('cms.publications.destroy', $publication))->assertRedirect();
        $this->assertDatabaseCount('publications', 0);
        Storage::disk('public')->assertMissing($publication->cover_path);
    }

    public function test_cms_rejects_invalid_cover_missing_fields_and_missing_publish_date(): void
    {
        Storage::fake('public');
        $this->loginAs('super_admin');
        $this->post(route('cms.publications.store'), $this->data(['judul' => '', 'published_at' => '', 'cover' => UploadedFile::fake()->create('malicious.svg', 1, 'image/svg+xml')]))
            ->assertSessionHasErrors(['judul', 'published_at', 'cover']);
        $this->assertDatabaseCount('publications', 0);
    }

    public function test_article_text_is_escaped(): void
    {
        $publication = Publication::create($this->data(['konten' => '<script>alert(1)</script>']));
        $this->get(route('publik.publikasi.show', $publication))->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    }
}
