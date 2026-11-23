<?php

namespace App\Http\Controllers;

use App\Models\Publication;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $base = rtrim(config('seo.url'), '/');
        $paths = ['/', '/indikator-publik', '/indikator-publik?jenis=IKU', '/indikator-publik?jenis=IKK', '/indikator-publik?jenis=IKD', '/webgis', '/bps', '/jdih', '/publikasi'];
        $entries = array_map(fn ($path) => ['url' => $base.$path], $paths);
        foreach (Publication::published()->select(['id', 'updated_at'])->orderBy('id')->cursor() as $publication) {
            $entries[] = ['url' => $base.'/publikasi/'.$publication->id, 'lastmod' => $publication->updated_at?->toAtomString()];
        }

        return response()->view('seo.sitemap', compact('entries'))->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
