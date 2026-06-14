{{--
    Vista previa de una página: panel flotante anclado abajo a la derecha con el iframe de la ruta pública.
    Es arrastrable y redimensionable, y se puede minimizar (estilo ventana) para dejar visible solo la cabecera.
    Utiliza preview.js para el javascript necesario para hacerlo funcionar
    Parámetros:
      - $pagina           (obligatorio) página a previsualizar con un iframe.
      - $mostrarExtendido (opcional, por defecto true) si arranca extendido; en false arranca minimizado (solo cabecera).
--}}
@php
    $mostrarExtendido = $mostrarExtendido ?? true;
    $minimizado = !$mostrarExtendido;
@endphp

<section id="paginaPreview" class="pagina-preview shadow @if ($minimizado) is-minimized @endif"
    role="dialog" aria-modal="false" aria-label="{{ trans('fields.paginas.preview.titulo') }}" tabindex="-1">
    <header class="pagina-preview-header" data-preview-drag>
        <span class="pagina-preview-title text-truncate">
            <i class="fa-solid fa-up-down-left-right me-2" aria-hidden="true"></i>
            {{-- El texto se refresca en vivo (data-preview-title) cuando se actualiza el título de la página --}}
            <span data-preview-title>{{ $pagina->getTranslation('titulo', app()->getLocale(), false) }}</span>
        </span>
        <span class="pagina-preview-actions">
            {{-- Presets de tamaño: ajustan el panel al ancho aproximado de cada dispositivo. Cada control usa el popup CSS-only del proyecto (data-popup) con el texto de lo que hace --}}
            <span class="pagina-preview-sizes" role="group" aria-label="{{ trans('fields.paginas.preview.size') }}">
                <button type="button" class="pagina-preview-btn popup" data-preview-size="movil"
                    aria-label="{{ trans('fields.paginas.preview.movil') }}"
                    data-popup="{{ trans('fields.paginas.preview.movil') }}">
                    <i class="fa-solid fa-mobile-screen-button" aria-hidden="true"></i>
                </button>
                <button type="button" class="pagina-preview-btn popup" data-preview-size="tablet"
                    aria-label="{{ trans('fields.paginas.preview.tablet') }}"
                    data-popup="{{ trans('fields.paginas.preview.tablet') }}">
                    <i class="fa-solid fa-tablet-screen-button" aria-hidden="true"></i>
                </button>
                <button type="button" class="pagina-preview-btn popup" data-preview-size="escritorio"
                    aria-label="{{ trans('fields.paginas.preview.escritorio') }}"
                    data-popup="{{ trans('fields.paginas.preview.escritorio') }}">
                    <i class="fa-solid fa-desktop" aria-hidden="true"></i>
                </button>
                <button type="button" class="pagina-preview-btn popup" data-preview-flip
                    aria-label="{{ trans('fields.paginas.preview.voltear') }}"
                    data-popup="{{ trans('fields.paginas.preview.voltear') }}">
                    <i class="fa-solid fa-rotate" aria-hidden="true"></i>
                </button>
            </span>

            <span class="pagina-preview-divider" aria-hidden="true"></span>

            <a href="{{ route($pagina->clave) }}" target="_blank" rel="noopener" class="pagina-preview-btn popup"
                aria-label="{{ trans('fields.paginas.preview.abrir_pestana') }}"
                data-popup="{{ trans('fields.paginas.preview.abrir_pestana') }}">
                <i class="fa-solid fa-up-right-from-square" aria-hidden="true"></i>
            </a>
            {{-- Botón de minimizar/restaurar (al final del todo, estilo ventana): colapsa el panel hasta dejar solo la cabecera --}}
            <button type="button" class="pagina-preview-btn popup" data-preview-minimize
                data-label-minimizar="{{ trans('fields.paginas.preview.minimizar') }}"
                data-label-restaurar="{{ trans('fields.paginas.preview.restaurar') }}"
                aria-label="{{ $minimizado ? trans('fields.paginas.preview.restaurar') : trans('fields.paginas.preview.minimizar') }}"
                data-popup="{{ $minimizado ? trans('fields.paginas.preview.restaurar') : trans('fields.paginas.preview.minimizar') }}">
                <i class="fa-solid {{ $minimizado ? 'fa-window-restore' : 'fa-minus' }}" data-preview-minimize-icon
                    aria-hidden="true"></i>
            </button>
        </span>
    </header>

    <div class="pagina-preview-body">
        <iframe data-src="{{ route($pagina->clave) }}" title="{{ trans('fields.paginas.preview.titulo') }}"
            loading="lazy"></iframe>
    </div>

    <span class="pagina-preview-resizer" data-preview-resize
        aria-label="{{ trans('fields.paginas.preview.redimensionar') }}"></span>
</section>
