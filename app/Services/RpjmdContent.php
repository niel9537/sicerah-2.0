<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class RpjmdContent
{
    public function get(): array
    {
        $path = storage_path('app/private/rpjmd-content.json');
        return json_decode(File::get(File::exists($path) ? $path : resource_path('data/rpjmd.json')), true, 512, JSON_THROW_ON_ERROR);
    }

    public function save(array $content): void
    {
        $path = storage_path('app/private/rpjmd-content.json');
        File::ensureDirectoryExists(dirname($path));
        File::replace($path, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }
}
