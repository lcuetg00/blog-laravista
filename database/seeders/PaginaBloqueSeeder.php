<?php

namespace Database\Seeders;

use App\Enums\BloqueTipoEnum;
use App\Models\Bloque;
use App\Models\Pagina;
use Illuminate\Database\Seeder;

/**
 * Seeder que crea los bloques de ejemplo que componen cada página pública (home, créditos, tecnologías, proyectos, contacto) con contenido placeholder.
 *
 * Los campos se siembran ya en el formato nativo de Translatable (un bloque de campos por idioma: {es:{...}, en:{...}, ja:{...}}), igual que los guarda el panel.
 * Idempotente: updateOrCreate por (pagina_id, orden) no duplica bloques entre ejecuciones.
 */
class PaginaBloqueSeeder extends Seeder
{
    /**
     * Crea los bloques de cada página recorriendo el catálogo y resolviendo cada página por su clave estable.
     */
    public function run(): void
    {
        // Indexamos las páginas por su clave para enlazar cada grupo de bloques con su pagina_id
        $paginas = Pagina::query()->get()->keyBy('clave');

        foreach ($this->bloquesPorPagina() as $clave => $bloques) {
            $pagina = $paginas->get($clave);

            // Si la página no existe (seeder de páginas no ejecutado) saltamos sus bloques
            if ($pagina === null) {
                continue;
            }

            foreach ($bloques as $orden => $bloque) {
                // Los campos ya vienen en el formato de Translatable ({es:{...}, ...}); Spatie los guarda tal cual
                Bloque::updateOrCreate(
                    ['pagina_id' => $pagina->id, 'orden' => $orden + 1],
                    ['tipo' => $bloque['tipo'], 'campos' => $bloque['campos']],
                );
            }
        }
    }

    /**
     * Devuelve el catálogo completo de bloques agrupado por la clave de su página (el índice del array interno define el orden, empezando en 1).
     */
    private function bloquesPorPagina(): array
    {
        return [
            'home' => $this->bloquesHome(),
            'creditos' => $this->bloquesCreditos(),
            'tecnologias' => $this->bloquesTecnologias(),
            'proyectos' => $this->bloquesProyectos(),
            'contacto' => $this->bloquesContacto(),
        ];
    }

    /**
     * Bloques de ejemplo de la home: título+subtítulo, swiper, título y título+subtítulo+imagen.
     */
    private function bloquesHome(): array
    {
        return [
            [
                'tipo' => BloqueTipoEnum::TITULO_SUBTITULO,
                'campos' => [
                    'es' => [
                        'titulo' => 'Título de ejemplo',
                        'subtitulo' => 'Subtítulo de ejemplo. Texto de relleno para este bloque. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ],
                    'en' => [
                        'titulo' => 'Sample title',
                        'subtitulo' => 'Sample subtitle. Placeholder text for this block. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ],
                    'ja' => [
                        'titulo' => 'サンプルタイトル',
                        'subtitulo' => 'サンプルサブタイトル。このブロック用のプレースホルダーテキストです。ダミーテキスト。',
                    ],
                ],
            ],
            [
                'tipo' => BloqueTipoEnum::SWIPER,
                // Las imágenes del carrusel se suben desde el panel (medialibrary), no se siembran
                'campos' => [],
            ],
            [
                'tipo' => BloqueTipoEnum::TITULO,
                'campos' => [
                    'es' => ['titulo' => 'Título de ejemplo'],
                    'en' => ['titulo' => 'Sample title'],
                    'ja' => ['titulo' => 'サンプルタイトル'],
                ],
            ],
            [
                'tipo' => BloqueTipoEnum::TITULO_SUBTITULO_IMAGEN,
                // El icono y la imagen se suben desde el panel (medialibrary), no se siembran
                'campos' => [
                    'es' => [
                        'titulo' => 'Título de ejemplo',
                        'subtitulo' => 'Subtítulo de ejemplo. Texto de relleno para este bloque. Lorem ipsum dolor sit amet.',
                    ],
                    'en' => [
                        'titulo' => 'Sample title',
                        'subtitulo' => 'Sample subtitle. Placeholder text for this block. Lorem ipsum dolor sit amet.',
                    ],
                    'ja' => [
                        'titulo' => 'サンプルタイトル',
                        'subtitulo' => 'サンプルサブタイトル。このブロック用のプレースホルダーテキストです。',
                    ],
                ],
            ],
        ];
    }

    /**
     * Bloques de ejemplo de créditos: título+subtítulo, título, tabla, dos bloques de texto, título, tabla y bloque de texto.
     */
    private function bloquesCreditos(): array
    {
        return [
            [
                'tipo' => BloqueTipoEnum::TITULO_SUBTITULO,
                'campos' => [
                    'es' => [
                        'titulo' => 'Título de ejemplo',
                        'subtitulo' => 'Subtítulo de ejemplo. Texto de relleno para este bloque.',
                    ],
                    'en' => [
                        'titulo' => 'Sample title',
                        'subtitulo' => 'Sample subtitle. Placeholder text for this block.',
                    ],
                    'ja' => [
                        'titulo' => 'サンプルタイトル',
                        'subtitulo' => 'サンプルサブタイトル。このブロック用のプレースホルダーテキストです。',
                    ],
                ],
            ],
            [
                'tipo' => BloqueTipoEnum::TITULO,
                'campos' => [
                    'es' => ['titulo' => 'Título de ejemplo'],
                    'en' => ['titulo' => 'Sample title'],
                    'ja' => ['titulo' => 'サンプルタイトル'],
                ],
            ],
            [
                'tipo' => BloqueTipoEnum::TABLA,
                'campos' => [
                    'es' => [
                        'columnas' => ['Columna 1', 'Columna 2', 'Columna 3', 'Columna 4'],
                        'filas' => [
                            ['Dato de ejemplo', 'Dato de ejemplo', 'Dato de ejemplo', 'Dato de ejemplo'],
                            ['Dato de ejemplo', 'Dato de ejemplo', 'Dato de ejemplo', 'Dato de ejemplo'],
                        ],
                    ],
                    'en' => [
                        'columnas' => ['Column 1', 'Column 2', 'Column 3', 'Column 4'],
                        'filas' => [
                            ['Sample data', 'Sample data', 'Sample data', 'Sample data'],
                            ['Sample data', 'Sample data', 'Sample data', 'Sample data'],
                        ],
                    ],
                    'ja' => [
                        'columnas' => ['列1', '列2', '列3', '列4'],
                        'filas' => [
                            ['サンプルデータ', 'サンプルデータ', 'サンプルデータ', 'サンプルデータ'],
                            ['サンプルデータ', 'サンプルデータ', 'サンプルデータ', 'サンプルデータ'],
                        ],
                    ],
                ],
            ],
            [
                'tipo' => BloqueTipoEnum::BLOQUE_TEXTO,
                'campos' => [
                    'es' => [
                        'titulo' => 'Título de ejemplo',
                        'subtitulo' => 'Texto de relleno para este bloque. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ],
                    'en' => [
                        'titulo' => 'Sample title',
                        'subtitulo' => 'Placeholder text for this block. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ],
                    'ja' => [
                        'titulo' => 'サンプルタイトル',
                        'subtitulo' => 'このブロック用のプレースホルダーテキストです。ダミーテキスト。',
                    ],
                ],
            ],
            [
                'tipo' => BloqueTipoEnum::BLOQUE_TEXTO,
                'campos' => [
                    'es' => [
                        'titulo' => 'Título de ejemplo',
                        'subtitulo' => 'Texto de relleno para este bloque. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ],
                    'en' => [
                        'titulo' => 'Sample title',
                        'subtitulo' => 'Placeholder text for this block. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ],
                    'ja' => [
                        'titulo' => 'サンプルタイトル',
                        'subtitulo' => 'このブロック用のプレースホルダーテキストです。ダミーテキスト。',
                    ],
                ],
            ],
            [
                'tipo' => BloqueTipoEnum::TITULO,
                'campos' => [
                    'es' => ['titulo' => 'Título de ejemplo'],
                    'en' => ['titulo' => 'Sample title'],
                    'ja' => ['titulo' => 'サンプルタイトル'],
                ],
            ],
            [
                'tipo' => BloqueTipoEnum::TABLA,
                'campos' => [
                    'es' => [
                        'columnas' => ['Columna 1', 'Columna 2', 'Columna 3'],
                        'filas' => [
                            ['Dato de ejemplo', 'Dato de ejemplo', 'Dato de ejemplo'],
                            ['Dato de ejemplo', 'Dato de ejemplo', 'Dato de ejemplo'],
                            ['Dato de ejemplo', 'Dato de ejemplo', 'Dato de ejemplo'],
                        ],
                    ],
                    'en' => [
                        'columnas' => ['Column 1', 'Column 2', 'Column 3'],
                        'filas' => [
                            ['Sample data', 'Sample data', 'Sample data'],
                            ['Sample data', 'Sample data', 'Sample data'],
                            ['Sample data', 'Sample data', 'Sample data'],
                        ],
                    ],
                    'ja' => [
                        'columnas' => ['列1', '列2', '列3'],
                        'filas' => [
                            ['サンプルデータ', 'サンプルデータ', 'サンプルデータ'],
                            ['サンプルデータ', 'サンプルデータ', 'サンプルデータ'],
                            ['サンプルデータ', 'サンプルデータ', 'サンプルデータ'],
                        ],
                    ],
                ],
            ],
            [
                'tipo' => BloqueTipoEnum::BLOQUE_TEXTO,
                'campos' => [
                    'es' => [
                        'titulo' => 'Título de ejemplo',
                        'subtitulo' => 'Texto de relleno para este bloque. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ],
                    'en' => [
                        'titulo' => 'Sample title',
                        'subtitulo' => 'Placeholder text for this block. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ],
                    'ja' => [
                        'titulo' => 'サンプルタイトル',
                        'subtitulo' => 'このブロック用のプレースホルダーテキストです。ダミーテキスト。',
                    ],
                ],
            ],
        ];
    }

    /**
     * Bloques de ejemplo de tecnologías: título+subtítulo y dos carruseles giratorios (normal e inverso).
     */
    private function bloquesTecnologias(): array
    {
        return [
            [
                'tipo' => BloqueTipoEnum::TITULO_SUBTITULO,
                'campos' => [
                    'es' => [
                        'titulo' => 'Título de ejemplo',
                        'subtitulo' => 'Subtítulo de ejemplo. Texto de relleno para este bloque.',
                    ],
                    'en' => [
                        'titulo' => 'Sample title',
                        'subtitulo' => 'Sample subtitle. Placeholder text for this block.',
                    ],
                    'ja' => [
                        'titulo' => 'サンプルタイトル',
                        'subtitulo' => 'サンプルサブタイトル。このブロック用のプレースホルダーテキストです。',
                    ],
                ],
            ],
            [
                'tipo' => BloqueTipoEnum::CARRUSEL_GIRATORIO,
                // Los items (imagen + título) se suben desde el panel (medialibrary), no se siembran
                'campos' => [
                    'es' => ['direccion' => 'normal'],
                    'en' => ['direccion' => 'normal'],
                    'ja' => ['direccion' => 'normal'],
                ],
            ],
            [
                'tipo' => BloqueTipoEnum::CARRUSEL_GIRATORIO,
                // Los items (imagen + título) se suben desde el panel (medialibrary), no se siembran
                'campos' => [
                    'es' => ['direccion' => 'inverso'],
                    'en' => ['direccion' => 'inverso'],
                    'ja' => ['direccion' => 'inverso'],
                ],
            ],
        ];
    }

    /**
     * Bloques de ejemplo de proyectos: título+subtítulo y un hero con botón.
     */
    private function bloquesProyectos(): array
    {
        return [
            [
                'tipo' => BloqueTipoEnum::TITULO_SUBTITULO,
                'campos' => [
                    'es' => [
                        'titulo' => 'Título de ejemplo',
                        'subtitulo' => 'Subtítulo de ejemplo. Texto de relleno para este bloque.',
                    ],
                    'en' => [
                        'titulo' => 'Sample title',
                        'subtitulo' => 'Sample subtitle. Placeholder text for this block.',
                    ],
                    'ja' => [
                        'titulo' => 'サンプルタイトル',
                        'subtitulo' => 'サンプルサブタイトル。このブロック用のプレースホルダーテキストです。',
                    ],
                ],
            ],
            [
                'tipo' => BloqueTipoEnum::HERO,
                // El icono se sube como imagen desde el panel (medialibrary), no se siembra
                'campos' => [
                    'es' => [
                        'titulo' => 'Título de ejemplo',
                        'descripcion' => 'Descripción de ejemplo. Texto de relleno para este bloque.',
                        'texto_boton' => 'Botón de ejemplo',
                        'url_boton' => 'https://example.com',
                    ],
                    'en' => [
                        'titulo' => 'Sample title',
                        'descripcion' => 'Sample description. Placeholder text for this block.',
                        'texto_boton' => 'Sample button',
                        'url_boton' => 'https://example.com',
                    ],
                    'ja' => [
                        'titulo' => 'サンプルタイトル',
                        'descripcion' => 'サンプルの説明。このブロック用のプレースホルダーテキストです。',
                        'texto_boton' => 'サンプルボタン',
                        'url_boton' => 'https://example.com',
                    ],
                ],
            ],
        ];
    }

    /**
     * Bloques de ejemplo de contacto: título+subtítulo y dos heroes con botón.
     */
    private function bloquesContacto(): array
    {
        return [
            [
                'tipo' => BloqueTipoEnum::TITULO_SUBTITULO,
                'campos' => [
                    'es' => [
                        'titulo' => 'Título de ejemplo',
                        'subtitulo' => 'Subtítulo de ejemplo. Texto de relleno para este bloque.',
                    ],
                    'en' => [
                        'titulo' => 'Sample title',
                        'subtitulo' => 'Sample subtitle. Placeholder text for this block.',
                    ],
                    'ja' => [
                        'titulo' => 'サンプルタイトル',
                        'subtitulo' => 'サンプルサブタイトル。このブロック用のプレースホルダーテキストです。',
                    ],
                ],
            ],
            [
                'tipo' => BloqueTipoEnum::HERO,
                // El icono se sube como imagen desde el panel (medialibrary), no se siembra
                'campos' => [
                    'es' => [
                        'titulo' => 'Título de ejemplo',
                        'descripcion' => 'Descripción de ejemplo.',
                        'texto_boton' => 'Botón de ejemplo',
                        'url_boton' => 'https://example.com',
                    ],
                    'en' => [
                        'titulo' => 'Sample title',
                        'descripcion' => 'Sample description.',
                        'texto_boton' => 'Sample button',
                        'url_boton' => 'https://example.com',
                    ],
                    'ja' => [
                        'titulo' => 'サンプルタイトル',
                        'descripcion' => 'サンプルの説明。',
                        'texto_boton' => 'サンプルボタン',
                        'url_boton' => 'https://example.com',
                    ],
                ],
            ],
            [
                'tipo' => BloqueTipoEnum::HERO,
                // El icono se sube como imagen desde el panel (medialibrary), no se siembra
                'campos' => [
                    'es' => [
                        'titulo' => 'Título de ejemplo',
                        'descripcion' => 'Descripción de ejemplo.',
                        'texto_boton' => 'Botón de ejemplo',
                        'url_boton' => 'https://example.com',
                    ],
                    'en' => [
                        'titulo' => 'Sample title',
                        'descripcion' => 'Sample description.',
                        'texto_boton' => 'Sample button',
                        'url_boton' => 'https://example.com',
                    ],
                    'ja' => [
                        'titulo' => 'サンプルタイトル',
                        'descripcion' => 'サンプルの説明。',
                        'texto_boton' => 'サンプルボタン',
                        'url_boton' => 'https://example.com',
                    ],
                ],
            ],
        ];
    }
}
