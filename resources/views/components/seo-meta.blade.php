@props(['meta' => []])

@php
    $seo = \App\Helpers\SeoHelper::generateMeta(
        $meta['title'] ?? null,
        $meta['description'] ?? null,
        $meta['image'] ?? null,
        $meta['url'] ?? null,
        $meta['type'] ?? 'website',
        $meta['keywords'] ?? null,
    );

    $keywordsString = is_array($seo['keywords']) ? implode(', ', $seo['keywords']) : $seo['keywords'];
    $localeMap = [
        'es' => 'es_ES',
        'en' => 'en_US',
        'ja' => 'ja_JP',
    ];
    $ogLocale = $localeMap[$seo['locale']] ?? 'en_US';
@endphp

<meta name="description" content="{{ $seo['description'] }}">
<meta name="keywords" content="{{ $keywordsString }}">

{{-- Añadir author aquí, parámetro configurable  --}}
{{-- <meta name="author" content=""> --}}

<link rel="canonical" href="{{ $seo['canonical'] }}">

<meta property="og:title" content="{{ $seo['title'] }}">
<meta property="og:description" content="{{ $seo['description'] }}">
<meta property="og:image" content="{{ $seo['image'] }}">
<meta property="og:url" content="{{ $seo['url'] }}">
<meta property="og:type" content="{{ $seo['type'] }}">
<meta property="og:locale" content="{{ $ogLocale }}">
<meta property="og:site_name" content="{{ config('app.name') }}">

<title>{{ $seo['title'] }}</title>
