<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Genera sitemap.xml para todos los idiomas disponibles';

    /**
     * Genera el archivo sitemap.xml y lo guarda en la carpeta public.
     */
    public function handle(): int
    {
        $this->info('Generando sitemap.xml');

        $sitemap = $this->generateSitemapXml();

        File::put(public_path('sitemap.xml'), $sitemap);

        $this->info('Generando correctamente public/sitemap.xml');

        return Command::SUCCESS;
    }

    /**
     * Construye el contenido XML del sitemap con todas las rutas e idiomas disponibles.
     */
    protected function generateSitemapXml(): string
    {
        $baseUrl = config('app.url');
        $locales = LaravelLocalization::getSupportedLocales();

        $routes = [
            ['path' => '', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['path' => 'tecnologias', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['path' => 'proyectos', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['path' => 'contacto', 'priority' => '0.7', 'changefreq' => 'monthly'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $xml .= ' xmlns:xhtml="http://www.w3.org/1999/xhtml">'.PHP_EOL;

        foreach ($routes as $route) {
            foreach ($locales as $localeCode => $properties) {
                $url = $route['path'] === ''
                    ? "{$baseUrl}/{$localeCode}"
                    : "{$baseUrl}/{$localeCode}/{$route['path']}";

                $xml .= '  <url>'.PHP_EOL;
                $xml .= "    <loc>{$url}</loc>".PHP_EOL;
                $xml .= "    <changefreq>{$route['changefreq']}</changefreq>".PHP_EOL;
                $xml .= "    <priority>{$route['priority']}</priority>".PHP_EOL;

                foreach ($locales as $altLocaleCode => $altProperties) {
                    $altUrl = $route['path'] === ''
                        ? "{$baseUrl}/{$altLocaleCode}"
                        : "{$baseUrl}/{$altLocaleCode}/{$route['path']}";

                    $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"{$altLocaleCode}\" href=\"{$altUrl}\" />".PHP_EOL;
                }

                $xml .= '  </url>'.PHP_EOL;
            }
        }

        $xml .= '</urlset>'.PHP_EOL;

        return $xml;
    }
}
