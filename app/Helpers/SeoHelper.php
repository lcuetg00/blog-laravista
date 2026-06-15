<?php

namespace App\Helpers;

use App\Models\Pagina;

class SeoHelper
{
    /**
     * Construye el array de SEO de una página de bloques a partir de su título traducido y su descripción para el SEO.
     */
    public static function desdePagina(Pagina $pagina): array
    {
        return [
            'title' => $pagina->getTranslation('titulo', app()->getLocale()),
            'description' => $pagina->getTranslation('descripcion', app()->getLocale()),
        ];
    }

    /**
     * Genera un array con los metadatos SEO (título, descripción, imagen, URL, etc.) para una página.
     */
    public static function generateMeta(
        ?string $title = null,
        ?string $description = null,
        ?string $image = null,
        ?string $url = null,
        string $type = 'website',
        ?array $keywords = null
    ): array {
        $appName = config('app.name');
        $appUrl = config('app.url');

        return [
            'title' => $title ? "{$title} - {$appName}" : $appName,
            'description' => $description ?? trans('public.meta.default_description'),
            'image' => $image ? asset($image) : asset('images/laravista-smaller.png'),
            'url' => $url ?? request()->url(),
            'type' => $type,
            'keywords' => $keywords ?? trans('public.meta.default_keywords'),
            'locale' => app()->getLocale(),
            'canonical' => $url ?? request()->url(),
        ];
    }
}
