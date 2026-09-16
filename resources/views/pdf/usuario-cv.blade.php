<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    @php
        // Dompdf solo acepta fuentes format("truetype"); los .woff de resources/fonts (usados en la web) no sirven aquí.
        // Comprobamos que existan los .ttf reales antes de declarar el @font-face: si apuntara a un fichero que no es
        // TrueType de verdad (o no existe), el parser de fuentes de dompdf agota la memoria en vez de ignorarlo.
        $notoRegularPath = resource_path('fonts/noto-sans-jp/NotoSansJP-Regular.ttf');
        $notoBoldPath = resource_path('fonts/noto-sans-jp/NotoSansJP-Bold.ttf');

        // Iconos (Font Awesome Free, trazos "solid") como ficheros .svg sueltos referenciados con <img>: dompdf no
        // soporta de forma fiable el <svg> inline ni los data URI de SVG, solo ficheros de imagen.
        $iconosPath = [
            'envelope' => resource_path('images/pdf-icons/envelope.svg'),
            'calendar-days' => resource_path('images/pdf-icons/calendar-days.svg'),
            'location-dot' => resource_path('images/pdf-icons/location-dot.svg'),
            'flag' => resource_path('images/pdf-icons/flag.svg'),
        ];

        // Datos personales que se muestran bajo el nombre en la cabecera, cada uno con su icono y su etiqueta
        $datosPersonales = collect([
            ['icono' => 'envelope', 'etiqueta' => trans('fields.input.email'), 'valor' => $usuario->email],
            [
                'icono' => 'calendar-days',
                'etiqueta' => trans('fields.input.fecha_nacimiento'),
                'valor' => $usuario->fecha_nacimiento?->format('d/m/Y'),
            ],
            ['icono' => 'location-dot', 'etiqueta' => trans('fields.input.direccion'), 'valor' => $usuario->direccion],
            ['icono' => 'flag', 'etiqueta' => trans('fields.input.nacionalidad'), 'valor' => $usuario->nacionalidad],
        ])->filter(fn(array $dato): bool => filled($dato['valor']));

        // Los datos personales se pintan en una rejilla de 2 columnas leída "por columnas" (1ª y 2ª entrada en la
        // columna izquierda, 3ª y 4ª en la derecha) en vez de "por filas", para que cada dato quede debajo del
        // anterior en su misma columna; ->split(2) reparte la colección en esas 2 columnas y luego se combinan de
        // una en una por posición para formar las filas de la tabla (null si una columna tiene menos elementos)
        $columnasDatosPersonales = $datosPersonales->values()->split(2);
        $filasDatosPersonales = $datosPersonales->isEmpty()
            ? collect()
            : collect(range(0, $columnasDatosPersonales->max(fn($columna) => $columna->count()) - 1))->map(
                fn(int $fila): array => $columnasDatosPersonales->map(fn($columna) => $columna->get($fila))->all(),
            );

        // Tamaños de fuente elegidos para este CV (cabecera y resto del contenido, cada uno independiente)
        $pxNombre = $cv->font_size_cabecera->sizeNombreCabecera();
        $pxSubtitulo = $cv->font_size_cabecera->sizeSubtituloCabecera();
        $pxTituloSeccion = $cv->font_size_contenido->sizeTituloSeccion();
        // +1px sobre el tamaño elegido por el usuario (Pequeño/Mediano/Grande): hace el texto normal del cuerpo
        // (la descripción de cada sección) ligeramente más grande sin tocar la escala de la propia opción
        $pxDescripcionSeccion = $cv->font_size_contenido->sizeDescripcionSeccion() + 1;
        $pxIconoSubtitulo = max(8, (int) round($pxSubtitulo * 0.85));

        // Tamaños de los encabezados (H1-H6) que el editor de texto enriquecido permite insertar dentro de la descripción
        // de una sección, con proporciones similares a las que usa un navegador por defecto (2em, 1.17em, 1em, 0.83em,
        // 0.67em) para que se sigan viendo como una jerarquía de títulos, pero partiendo del tamaño de fuente elegido
        // para el contenido en vez de un tamaño fijo (el H2 es 1.25em en vez del 1.5em por defecto, más pequeño)
        $pxTitulosDescripcion = collect([1 => 2, 2 => 1.25, 3 => 1.17, 4 => 1, 5 => 0.83, 6 => 0.67])->map(
            fn(float $proporcion): int => (int) round($pxDescripcionSeccion * $proporcion),
        );
    @endphp

    {{-- Estilos en un partial aparte en vez de un .css enlazado: dompdf no soporta fiablemente hojas de estilo
         externas (rutas relativas y "Vite" no resuelven ahí dentro). Adenñas el bloque necesita variables PHP
         (colores, tamaños de fuente, ...) --}}
    @include('pdf.partials.usuario-cv-styles')
</head>

<body>

    <table class="documento">
        <tbody>
            <tr>
                <td class="pdf-header">
                    <table class="pdf-header-tabla">
                        <tr>
                            <td class="avatar-cell">
                                <img src="{{ $avatarPath }}" class="avatar" alt="">
                            </td>
                            <td class="nombre-cell">
                                <div class="nombre">{{ $usuario->nombre_completo }}</div>
                                {{-- Rejilla de 2 columnas con el icono, la etiqueta y el valor en celdas separadas:
                                     al ser una única tabla, dompdf ajusta el ancho de cada columna de etiqueta al
                                     texto más largo de esa columna y todos los valores empiezan alineados --}}
                                <table class="subtitulos-rejilla">
                                    @foreach ($filasDatosPersonales as $fila)
                                        <tr>
                                            @foreach ($fila as $indice => $dato)
                                                @if ($indice > 0)
                                                    <td class="subtitulo-separador-celda"></td>
                                                @endif
                                                <td class="subtitulo-icono-celda">
                                                    @if ($dato)
                                                        <img src="{{ $iconosPath[$dato['icono']] }}"
                                                            class="subtitulo-icono" alt="">
                                                    @endif
                                                </td>
                                                <td class="subtitulo-etiqueta-celda">
                                                    {{ $dato ? $dato['etiqueta'] . ':' : '' }}</td>
                                                <td class="subtitulo-valor-celda">{{ $dato['valor'] ?? '' }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            @foreach ($cv->secciones as $seccion)
                <tr>
                    <td
                        class="contenido-celda @if ($loop->first) es-primera @endif @if ($loop->last) es-ultima @endif">
                        <div class="seccion">
                            <div class="seccion-titulo">{{ \Illuminate\Support\Str::upper($seccion->titulo) }}</div>

                            @if ($seccion->descripcion)
                                <div
                                    class="seccion-descripcion @if ($seccion->sangria) seccion-sangria @endif">
                                    {!! $seccion->descripcion !!}
                                </div>
                            @endif

                            @if ($seccion->galeria()->isNotEmpty())
                                <div class="seccion-galeria @if ($seccion->sangria) seccion-sangria @endif">
                                    @foreach ($seccion->galeria()->chunk(3) as $fila)
                                        <table class="galeria-fila" style="width: {{ ($fila->count() * 100) / 3 }}%;">
                                            <tr>
                                                @foreach ($fila as $media)
                                                    <td class="galeria-celda"
                                                        style="width: {{ 100 / $fila->count() }}%;">
                                                        <img src="{{ $media->hasGeneratedConversion('pdf') ? $media->getPath('pdf') : $media->getPath() }}"
                                                            class="galeria-imagen" alt="">
                                                    </td>
                                                @endforeach
                                            </tr>
                                        </table>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Solo el fondo; el número de página y el tapado de cabecera en páginas > 1 se dibujan aparte, desde
         UsuarioController::dibujarPiePaginaCv() vía setCallbacks("end_document") de dompdf --}}
    <div class="pdf-footer"></div>

</body>

</html>
