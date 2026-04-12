<?php

namespace App\Helpers;

class SeoHelper
{
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
