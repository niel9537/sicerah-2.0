<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $source = json_decode(file_get_contents(database_path('data/prosn_2026.json')), true, 512, JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($source) {
            foreach ($source as $item) {
                $url = $item['tautan'][1] ?? null;
                if (! $url) {
                    continue;
                }
                $row = DB::table('emonev_prosn')->where('tahun', 2026)->where('baris', $item['baris'])->lockForUpdate()->first();
                if (! $row) {
                    continue;
                }
                $links = json_decode($row->tautan ?? '{}', true) ?: [];
                if (! empty($links[1])) {
                    continue;
                }
                $links[1] = $url;
                DB::table('emonev_prosn')->where('id', $row->id)->update(['tautan' => json_encode($links), 'versi' => $row->versi + 1, 'updated_at' => now()]);
            }
        });
    }

    public function down(): void
    {
        // Imported evidence links remain user data and are not removed on rollback.
    }
};
