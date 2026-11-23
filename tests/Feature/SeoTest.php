<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class SeoTest extends TestCase
{
    public function test_sitemap_template_avoids_php_short_open_tag_conflict(): void
    {
        $html = view('seo.sitemap', ['entries' => [['url' => 'https://bappelitbangda.niasbaratkab.go.id/']]])->render();
        $this->assertStringStartsWith('<urlset', $html);
        $this->assertNotFalse(simplexml_load_string($html));
    }

    public function test_metadata_has_canonical_and_valid_organization_schema(): void
    {
        $this->app['request'] = \Illuminate\Http\Request::create('/indikator-publik?jenis=IKU&utm_source=test');
        $html = view('layouts.partials.seo')->render();
        $this->assertStringContainsString('href="https://bappelitbangda.niasbaratkab.go.id/indikator-publik?jenis=IKU"', $html);
        $this->assertStringNotContainsString('utm_source', $html);
        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches);
        $schema = json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('https://schema.org', $schema['@context']);
        $this->assertSame('GovernmentOrganization', $schema['@graph'][0]['@type']);
        $this->assertSame('SICERAH Nias Barat', $schema['@graph'][1]['name']);
    }

    public function test_metadata_escapes_titles_and_descriptions(): void
    {
        $html = Blade::render('@section("title", $title) @section("meta_description", $description) @include("layouts.partials.seo")', [
            'title' => '"><script>alert(1)</script>',
            'description' => '" onload="alert(1)',
        ]);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('&quot; onload=&quot;alert(1)', $html);
    }

    public function test_sitemap_contains_only_public_and_published_pages(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_published');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
        DB::table('publications')->insert([
            ['id' => 1, 'is_published' => true, 'published_at' => now()->subDay(), 'updated_at' => now()],
            ['id' => 2, 'is_published' => false, 'published_at' => now()->subDay(), 'updated_at' => now()],
            ['id' => 3, 'is_published' => true, 'published_at' => now()->addDay(), 'updated_at' => now()],
        ]);
        $response = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);
        $urls = array_map(fn ($node) => (string) $node->loc, iterator_to_array($xml->url, false));
        $base = 'https://bappelitbangda.niasbaratkab.go.id';
        $this->assertContains($base.'/publikasi/1', $urls);
        $this->assertNotContains($base.'/publikasi/2', $urls);
        $this->assertNotContains($base.'/publikasi/3', $urls);
        $this->assertNotContains($base.'/struktur-hierarki', $urls);
        $this->assertNotContains($base.'/dashboard', $urls);
    }
}
