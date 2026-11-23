<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class BpsService
{
    public const SECTIONS = ['statictable' => 'Tabel Statistik', 'publication' => 'Publikasi BPS', 'indicators' => 'Indikator Strategis'];

    public function listing(string $model, int $page = 1, string $keyword = '', ?int $year = null): array
    {
        if (!isset(self::SECTIONS[$model])) throw new RuntimeException('Kategori BPS tidak tersedia.');
        $query = ['lang' => 'ind', 'page' => $page];
        if ($model !== 'indicators') {
            if ($keyword !== '') $query['keyword'] = $keyword;
            if ($year) $query['year'] = $year;
        }
        $payload = $this->fetch('list/model/'.$model.'/domain/'.$this->domain(), $query);
        $data = $payload['data'] ?? [];
        $meta = is_array($data) && is_array($data[0] ?? null) ? $data[0] : [];
        $records = is_array($data) && is_array($data[1] ?? null) ? $data[1] : [];
        return [
            'items' => collect($records)->filter(fn ($row) => is_array($row))->map(fn ($row) => $this->present($row, $model))->values()->all(),
            'page' => max(1, (int) ($meta['page'] ?? $page)),
            'pages' => max(1, (int) ($meta['pages'] ?? 1)),
            'total' => max(0, (int) ($meta['total'] ?? count($records))),
            'retrieved_at' => $payload['_retrieved_at'],
        ];
    }

    public function table(int $id): ?array
    {
        $payload = $this->fetch('view/domain/'.$this->domain().'/model/statictable/lang/ind/id/'.$id);
        $data = $payload['data'] ?? null;
        if (!is_array($data) || empty($data['table_id'])) return null;
        return $this->present($data, 'statictable') + ['rows' => $this->tableRows((string) ($data['table'] ?? '')), 'retrieved_at' => $payload['_retrieved_at']];
    }

    private function domain(): string
    {
        $domain = (string) config('services.bps.domain', '1225');
        if (!preg_match('/^\d{4}$/', $domain)) throw new RuntimeException('Konfigurasi wilayah BPS belum sesuai.');
        return $domain;
    }

    private function fetch(string $path, array $query = []): array
    {
        $key = trim((string) config('services.bps.api_key'));
        if ($key === '') throw new RuntimeException('Koneksi BPS belum dikonfigurasi.');
        $cacheKey = 'bps:v1:'.hash('sha256', $path.json_encode($query).$key);
        return Cache::store(config('services.bps.cache_store', 'file'))->remember($cacheKey, now()->addHour(), function () use ($path, $query, $key) {
            try {
                $response = Http::acceptJson()->connectTimeout(5)->timeout(18)
                    ->withOptions(['allow_redirects' => false, 'verify' => config('services.bps.ca_bundle') ?: true])
                    ->get(rtrim(config('services.bps.base_url'), '/').'/'.$path.'/key/'.rawurlencode($key), $query);
                $payload = $response->json();
                if (!$response->successful() || !is_array($payload) || ($payload['status'] ?? '') !== 'OK') {
                    throw new RuntimeException();
                }
                if (!in_array($payload['data-availability'] ?? '', ['available', 'list-not-available', 'not-available'], true)) {
                    throw new RuntimeException();
                }
                $payload['_retrieved_at'] = now()->toIso8601String();
                return $payload;
            } catch (Throwable $error) {
                // The upstream URL contains a credential: never log or rethrow it.
                throw new RuntimeException('Layanan BPS sedang tidak dapat diakses. Silakan coba beberapa saat lagi.');
            }
        });
    }

    private function present(array $row, string $model): array
    {
        return [
            'id' => $row['table_id'] ?? $row['pub_id'] ?? null,
            'title' => $this->text($row['title'] ?? 'Data BPS'),
            'subject' => $this->text($row['subj'] ?? ''),
            'description' => $this->text($row['abstract'] ?? $row['desc'] ?? ''),
            'date' => $this->text($row['updt_date'] ?? $row['rl_date'] ?? $row['sch_date'] ?? ''),
            'size' => $this->text($row['size'] ?? ''),
            'download' => $this->safeUrl($model === 'publication' ? ($row['pdf'] ?? '') : ($row['excel'] ?? '')),
            'cover' => $this->safeUrl($row['cover'] ?? ''),
            'value' => $this->text($row['value'] ?? ''),
            'unit' => $this->text($row['unit'] ?? ''),
            'source' => $this->text($row['data_source'] ?? ''),
        ];
    }

    private function text(mixed $value): string
    {
        if (!is_scalar($value)) return '';
        $text = trim(strip_tags(html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $key = (string) config('services.bps.api_key');
        return $key !== '' ? str_replace($key, '[disembunyikan]', $text) : $text;
    }

    private function safeUrl(mixed $url): ?string
    {
        if (!is_string($url) || !filter_var($url, FILTER_VALIDATE_URL)) return null;
        $key = (string) config('services.bps.api_key');
        if ($key !== '' && str_contains(rawurldecode($url), $key)) return null;
        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');
        return ($parts['scheme'] ?? '') === 'https' && !isset($parts['user']) && !isset($parts['pass'])
            && ($host === 'bps.go.id' || str_ends_with($host, '.bps.go.id')) ? $url : null;
    }

    private function tableRows(string $html): array
    {
        if ($html === '') return [];
        $dom = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        try {
            $dom->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
            $xpath = new DOMXPath($dom);
            foreach ($xpath->query('//script|//style|//iframe|//object|//noscript') as $node) $node->parentNode->removeChild($node);
            $rows = [];
            foreach ($xpath->query('//table//tr') as $row) {
                $cells = [];
                foreach ($xpath->query('./th|./td', $row) as $cell) {
                    $cells[] = ['text' => $this->text($cell->textContent), 'header' => $cell->tagName === 'th',
                        'colspan' => max(1, min(100, (int) $cell->getAttribute('colspan'))),
                        'rowspan' => max(1, min(100, (int) $cell->getAttribute('rowspan')))];
                }
                if ($cells) $rows[] = $cells;
            }
            return $rows;
        } finally {
            libxml_clear_errors(); libxml_use_internal_errors($previous);
        }
    }
}
