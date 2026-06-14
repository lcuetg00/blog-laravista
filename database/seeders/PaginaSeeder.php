<?php

namespace Database\Seeders;

use App\Models\Pagina;
use Illuminate\Database\Seeder;

/**
 * Seeder que crea la fila en BD de cada página pública (home, créditos, tecnologías, proyectos, contacto, política de privacidad, términos y condiciones), una por una, con las traducciones de su título (es/en/ja).
 *
 * Las páginas solo se crean desde aquí: el CRUD del panel no permite crear ni borrar páginas, solo editar.
 * La descripción se deja en null porque todavía no hay copy definitivo — el panel la rellenará caso a caso.
 */
class PaginaSeeder extends Seeder
{
    /**
     * Crea las páginas públicas con su clave estable y las traducciones del titulo en los tres idiomas soportados.
     */
    public function run(): void
    {
        // Una entrada por cada vista pública existente en resources/views/public/home y por cada ruta del HomeController
        $paginas = [
            [
                'clave' => 'home',
                'titulo' => ['es' => 'Inicio', 'en' => 'Home', 'ja' => 'ホーム'],
                'descripcion' => null,
            ],
            [
                'clave' => 'creditos',
                'titulo' => ['es' => 'Créditos', 'en' => 'Credits', 'ja' => 'クレジット'],
                'descripcion' => null,
            ],
            [
                'clave' => 'tecnologias',
                'titulo' => ['es' => 'Tecnologías', 'en' => 'Technologies', 'ja' => '技術'],
                'descripcion' => null,
            ],
            [
                'clave' => 'proyectos',
                'titulo' => ['es' => 'Proyectos', 'en' => 'Projects', 'ja' => 'プロジェクト'],
                'descripcion' => null,
            ],
            [
                'clave' => 'contacto',
                'titulo' => ['es' => 'Contacto', 'en' => 'Contact', 'ja' => 'お問い合わせ'],
                'descripcion' => null,
            ],
            [
                'clave' => 'politica-privacidad',
                'titulo' => ['es' => 'Política de privacidad', 'en' => 'Privacy Policy', 'ja' => 'プライバシーポリシー'],
                'descripcion' => null,
            ],
            [
                'clave' => 'terminos-condiciones',
                'titulo' => ['es' => 'Términos y condiciones', 'en' => 'Terms and Conditions', 'ja' => '利用規約'],
                'descripcion' => null,
            ],
        ];

        // Usamos firstOrCreate para que el seeder sea idempotente entre ejecuciones (no duplica si ya existe esa clave)
        foreach ($paginas as $datos) {
            Pagina::firstOrCreate(
                ['clave' => $datos['clave']],
                [
                    'titulo' => $datos['titulo'],
                    'descripcion' => $datos['descripcion'],
                    'activo' => true,
                ],
            );
        }
    }
}
