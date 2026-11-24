<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class RpjmdContent
{
    public function get(): array
    {
        $path = storage_path('app/private/rpjmd-content.json');
        $content = json_decode(File::get(File::exists($path) ? $path : resource_path('data/rpjmd.json')), true, 512, JSON_THROW_ON_ERROR);
        // Older CMS files used governance as Misi I. Preserve edits and stable
        // indicator links while bringing those files into the PERDA sequence.
        if (count($content['pairs'] ?? []) === 5 && str_starts_with(mb_strtolower($content['pairs'][0]['misi']), 'menyelenggarakan')) {
            $pairs = $content['pairs'];
            $content['pairs'] = array_map(fn ($index) => $pairs[$index], [1, 2, 3, 4, 0]);
            $content['pairs'][3]['misi'] = str_replace('Dalang Rangka', 'Dalam Rangka', $content['pairs'][3]['misi']);
        }
        return $content;
    }

    public function save(array $content): void
    {
        $path = storage_path('app/private/rpjmd-content.json');
        File::ensureDirectoryExists(dirname($path));
        File::replace($path, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }
}
