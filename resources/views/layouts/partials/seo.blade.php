@php
    $base = rtrim(config('seo.url'), '/');
    $path = request()->getPathInfo();
    $canonical = $base.($path === '/' ? '/' : $path);
    // Preserve pagination and indicator types, while excluding tracking and search parameters.
    $parameters = array_filter(request()->only(['jenis', 'page']), fn ($value) => is_scalar($value) && (string) $value !== '' && (string) $value !== '1');
    if ($parameters) $canonical .= '?'.http_build_query($parameters);
    $title = trim(strip_tags(html_entity_decode($__env->yieldContent('title', 'SICERAH Nias Barat'), ENT_QUOTES, 'UTF-8'))).' | Bappelitbangda Nias Barat';
    $description = trim(strip_tags(html_entity_decode($__env->yieldContent('meta_description', config('seo.description')), ENT_QUOTES, 'UTF-8')));
    $image = html_entity_decode($__env->yieldContent('meta_image', $base.'/images/logo-sicerah.png'), ENT_QUOTES, 'UTF-8');
    $indexable = !request()->routeIs('publik.struktur', 'publik.iku-opd') && !request()->filled('q');
    $graph = [
        ['@type' => 'GovernmentOrganization', '@id' => $base.'/#organization', 'name' => 'Bappelitbangda Kabupaten Nias Barat', 'alternateName' => ['Bappelitbangda Nias Barat', 'Bappelitbang Nias Barat'], 'description' => 'Badan Perencanaan Pembangunan, Penelitian dan Pengembangan Daerah Kabupaten Nias Barat, Sumatera Utara.', 'url' => $base.'/', 'logo' => $base.'/images/logo.png', 'areaServed' => ['@type' => 'AdministrativeArea', 'name' => 'Kabupaten Nias Barat']],
        ['@type' => 'WebSite', '@id' => $base.'/#website', 'name' => config('seo.name'), 'alternateName' => ['SICERAH', 'SICERAH Bappelitbangda Nias Barat'], 'url' => $base.'/', 'inLanguage' => 'id-ID', 'publisher' => ['@id' => $base.'/#organization']],
        ['@type' => 'WebPage', '@id' => $canonical.'#webpage', 'url' => $canonical, 'name' => $title, 'description' => $description, 'inLanguage' => 'id-ID', 'isPartOf' => ['@id' => $base.'/#website'], 'about' => ['@id' => $base.'/#organization']],
    ];
    $structuredData = [chr(64).'context' => 'https://schema.org', '@graph' => $graph];
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="robots" content="{{ $indexable ? 'index, follow, max-image-preview:large' : 'noindex, follow' }}">
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:locale" content="id_ID">
<meta property="og:type" content="{{ request()->routeIs('publik.publikasi.show') ? 'article' : 'website' }}">
<meta property="og:site_name" content="{{ config('seo.name') }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:image:alt" content="SICERAH Bappelitbangda Kabupaten Nias Barat">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">
<script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
