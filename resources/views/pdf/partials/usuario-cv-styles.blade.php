<style>
    @if ($usarNoto)
        @font-face {
            font-family: 'Noto Sans JP';
            font-weight: normal;
            font-style: normal;
            src: url('{{ $notoRegularPath }}') format('truetype');
        }
        @font-face {
            font-family: 'Noto Sans JP';
            font-weight: bold;
            font-style: normal;
            src: url('{{ $notoBoldPath }}') format('truetype');
        }
    @endif

    @page {
        size: A4;
        margin: 0;
        /* Reserva hueco arriba, en todas las páginas, para la banda de continuación (mucho más pequeña que la
           cabecera completa) que dibuja UsuarioController vía setCallbacks("end_document"); "@page :first" lo
           anula justo debajo para que en la 1ª página no quede ese hueco antes de la cabecera completa */
        @if ($mostrarPiePagina)
            margin-top: {{ $altoBandaContinuacion + $margenInferiorBandaContinuacion }}px;
        @endif
    }

    @if ($mostrarPiePagina)
        @page :first {
            margin-top: 0;
        }
    @endif

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: @if ($usarNoto) 'Noto Sans JP', @endif 'DejaVu Sans', sans-serif;
        font-size: 11px;
        line-height: 1.2;
        color: #4a4a4a;
    }

    .documento {
        width: 100%;
        border-collapse: collapse;
    }

    /* Cabecera completa: banda de color sólido con el avatar y los datos personales. Solo aparece una vez, al
       principio de la 1ª página (es una fila normal del <tbody>, no repetida); a partir de la 2ª se ve, en su
       lugar, la banda de continuación (mucho más pequeña) que dibuja UsuarioController */
    .pdf-header {
        background-color: {{ $cv->color_primario }};
        height: {{ $altoCabeceraCompleta }}px;
        padding: 10px 40px;
        vertical-align: middle;
    }

    .pdf-header-tabla {
        width: 100%;
        border-collapse: collapse;
    }

    .pdf-header-tabla td {
        vertical-align: middle;
    }

    .pdf-header .avatar-cell {
        width: 105px;
    }

    .pdf-header .avatar {
        width: 95px;
        height: 95px;
        border-radius: 50%;
        border: 3px solid #ffffff;
    }

    .pdf-header .nombre-cell {
        padding-left: 20px;
    }

    .pdf-header .nombre {
        font-size: {{ $pxNombre }}px;
        font-weight: bold;
        color: #ffffff;
        letter-spacing: 0.5px;
    }

    /* Rejilla de 2 columnas para los datos personales, con el icono, la etiqueta y el valor de cada dato en celdas
       separadas: al ser una única tabla, dompdf calcula el ancho de cada columna según su contenido más ancho, así
       que las etiquetas de una misma columna quedan con el mismo ancho y todos los valores empiezan alineados */
    .pdf-header .subtitulos-rejilla {
        width: auto;
        border-collapse: collapse;
        margin-top: 4px;
    }

    .pdf-header .subtitulos-rejilla td {
        /* line-height: 1 quita el interlineado heredado del body (1.35): ese espacio "fantasma" por encima y por
           debajo del texto es lo que hacía que, aun con vertical-align:middle, el icono se viera descentrado
           respecto al propio texto (se centraba con la caja de línea, no con los caracteres visibles) */
        vertical-align: middle;
        font-size: {{ $pxSubtitulo }}px;
        line-height: 1;
        color: #ffffff;
    }

    .pdf-header .subtitulo-icono-celda {
        /* Ancho fijo (igual para todas las filas) en vez de dejar que lo marque el propio icono: como cada icono
           tiene una proporción distinta (el sobre es cuadrado, el calendario y el pin no), con ancho libre cada
           fila quedaba con el icono y la etiqueta arrancando en una posición distinta */
        width: {{ $pxIconoSubtitulo }}px;
        padding-right: 5px;
        padding-bottom: 5px;
    }

    .pdf-header .subtitulo-etiqueta-celda {
        /* "white-space: nowrap" evita que una etiqueta larga (p.ej. "Fecha de nacimiento:") parta en dos líneas y
           descoloque el ancho que dompdf calcula para el resto de la columna */
        font-weight: bold;
        white-space: nowrap;
        padding-right: 4px;
        padding-bottom: 5px;
    }

    .pdf-header .subtitulo-valor-celda {
        /* Con el mismo padding arriba y abajo el texto queda un pelín más bajo que el icono (asimetría de las
           métricas de la fuente entre ascendentes y descendentes); este padding-bottom extra desplaza el centrado
           de dompdf un poco hacia arriba para compensarlo */
        padding-bottom: 5px;
    }

    /* Separación horizontal entre las 2 columnas de datos personales */
    .pdf-header .subtitulo-separador-celda {
        width: 20px;
    }

    .pdf-header .subtitulo-icono {
        /* Solo se fija el alto: el ancho queda libre para que cada icono conserve su proporción original (los
           iconos de Font Awesome no son todos cuadrados y forzar también el ancho los deformaba).
           Sin "display: block" (como el avatar, que se centra bien): en dompdf un elemento en bloque dentro de
           una celda no se centra igual que uno inline con vertical-align:middle, por eso quedaba más alto que el texto. */
        height: {{ $pxIconoSubtitulo }}px;
        width: auto;
    }

    /* Footer fijo a sangre completa: banda de color sólido con el número de página centrado (solo si hay más de una) */
    .pdf-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 34px;
        background-color: {{ $cv->color_secundario }};
        text-align: center;
        line-height: 34px;
        color: #ffffff;
        font-size: 11px;
        letter-spacing: 1px;
    }

    /* Celda de contenido de cada sección: margen lateral fijo en todas las filas, y el superior/inferior solo en la
       primera/última para no acumular espacio de sobra entre secciones */
    .contenido-celda {
        padding: 0 40px;
    }

    .contenido-celda.es-primera {
        padding-top: 20px;
    }

    .contenido-celda.es-ultima {
        /* Deja hueco suficiente para que el footer fijo (34px) no tape la última línea */
        padding-bottom: 45px;
    }

    /* Secciones del CV */
    .seccion {
        margin-bottom: 12px;
    }

    .seccion-titulo {
        display: inline-block;
        font-size: {{ $pxTituloSeccion }}px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6d28d9;
        margin-bottom: 6px;
    }

    /* DejaVu Sans y Noto Sans JP solo tienen los ficheros "normal" y "bold" embebidos (ver storage/fonts): un
       font-weight intermedio (500, 600...) no tiene ninguna cara de fuente que lo represente y dompdf acaba
       resolviéndolo igual que "bold", no como un peso intermedio. Para que el texto normal se vea con algo más de
       peso sin saltar directamente a negrita, se oscurece el color en vez de tocar el font-weight */
    .seccion-descripcion {
        font-size: {{ $pxDescripcionSeccion }}px;
        color: #333333;
    }

    .seccion-descripcion p {
        margin: 0 0 4px 0;
    }

    /* El editor de texto enriquecido permite marcar líneas como H1-H6 (botón "style" de la toolbar); sin este
       reset, dompdf les aplica su tamaño y margen por defecto (p.ej. un h1 trae ~2em de fuente y 0.67em de margen),
       lo que se veía como un hueco enorme dentro de la sección */
    .seccion-descripcion h1,
    .seccion-descripcion h2,
    .seccion-descripcion h3,
    .seccion-descripcion h4,
    .seccion-descripcion h5,
    .seccion-descripcion h6 {
        margin: 6px 0 4px 0;
        font-weight: bold;
        line-height: 1.2;
    }

    .seccion-descripcion h1 {
        font-size: {{ $pxTitulosDescripcion[1] }}px;
    }

    .seccion-descripcion h2 {
        font-size: {{ $pxTitulosDescripcion[2] }}px;
    }

    .seccion-descripcion h3 {
        font-size: {{ $pxTitulosDescripcion[3] }}px;
    }

    .seccion-descripcion h4 {
        font-size: {{ $pxTitulosDescripcion[4] }}px;
    }

    .seccion-descripcion h5 {
        font-size: {{ $pxTitulosDescripcion[5] }}px;
    }

    .seccion-descripcion h6 {
        font-size: {{ $pxTitulosDescripcion[6] }}px;
    }

    /* Sin esto, el primer elemento (título o párrafo) sumaba su margen superior al margen inferior de
       ".seccion-titulo", duplicando el hueco entre el título de la sección y su contenido */
    .seccion-descripcion > :first-child {
        margin-top: 0;
    }

    .seccion-descripcion ul,
    .seccion-descripcion ol {
        margin: 0 0 4px 0;
        padding-left: 16px;
    }

    .seccion-descripcion li {
        margin-bottom: 2px;
    }

    .seccion-descripcion img {
        max-width: 100%;
    }

    .seccion-galeria {
        margin-top: 6px;
    }

    /* Cada fila es su propia tabla, con un ancho igual a "nº de imágenes de la fila / 3" del contenedor (en vez de
       una única tabla al 100%): así, cuando una fila no llega a las 3 imágenes, la tabla queda más estrecha que el
       contenedor y el "margin: 0 auto" la centra, mientras que cada celda sigue midiendo un tercio del contenedor
       (100% dividido entre el nº de celdas de ESA fila, sobre una tabla que ya mide ese nº/3 del contenedor) y las
       imágenes no cambian de tamaño según cuántas haya */
    .galeria-fila {
        margin: 0 auto;
        border-collapse: collapse;
    }

    .galeria-celda {
        padding: 3px;
        vertical-align: top;
    }

    .galeria-imagen {
        width: 100%;
        height: auto;
        border-radius: 4px;
        border: 1px solid #e5e5e5;
    }

    /* Sangría: barra vertical + contenido desplazado a la derecha, activable por sección */
    .seccion-sangria {
        border-left: 3px solid #9d73ff;
        padding-left: 14px;
        margin-left: 4px;
    }
</style>
