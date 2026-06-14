{{-- Renderiza un bloque de forma legible (read-only) en un locale concreto, imitando cómo se vería en la página pública. Parámetros: $bloque y, opcional, $locale (por defecto el del panel) --}}
@use('App\Enums\BloqueTipoEnum')

@php
    $locale = $locale ?? app()->getLocale();
    $fallback = config('app.fallback_locale');

    // Bloque de campos del locale activo (Translatable resuelve el fallback al idioma de respaldo); las celdas ya vienen como texto plano por idioma
    $campos = $bloque->getTranslation('campos', $locale);

    // Resuelve una custom property traducible de media ({es,en,ja}) al locale activo; estas siguen en medialibrary, no en Translatable
    $trMedia = fn ($valor) => is_array($valor) ? ($valor[$locale] ?? $valor[$fallback] ?? '') : ($valor ?? '');

    $tipo = $bloque->tipo;
@endphp

<div class="bloque-preview border rounded p-3 mb-3">
    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="badge text-bg-secondary">#{{ $bloque->orden }}</span>
        <span class="text-muted small">{{ $tipo->etiqueta() }}</span>
    </div>

    @if ($tipo === BloqueTipoEnum::TITULO)
        <h2 class="h4 mb-0">{{ $campos['titulo'] ?? '' }}</h2>
    @elseif ($tipo === BloqueTipoEnum::TITULO_SUBTITULO)
        <h2 class="h4 mb-1">{{ $campos['titulo'] ?? '' }}</h2>
        <p class="mb-0 text-muted">{{ $campos['subtitulo'] ?? '' }}</p>
    @elseif ($tipo === BloqueTipoEnum::BLOQUE_TEXTO)
        <h3 class="h5 mb-1">{{ $campos['titulo'] ?? '' }}</h3>
        <p class="mb-0">{{ $campos['subtitulo'] ?? '' }}</p>
    @elseif ($tipo === BloqueTipoEnum::HERO)
        <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
            @if ($bloque->iconoUrl())
                <img src="{{ $bloque->iconoUrl() }}" alt="" class="rounded bloque-preview-icono" loading="lazy">
            @endif
            @if ($bloque->imagenUrl())
                <img src="{{ $bloque->imagenUrl() }}" alt="" class="rounded"
                    style="max-width: 160px; height: auto;" loading="lazy">
            @endif
            <div>
                <h2 class="h4 mb-1">{{ $campos['titulo'] ?? '' }}</h2>
                @if ($campos['descripcion'] ?? null)
                    <p class="mb-2 text-muted">{{ $campos['descripcion'] }}</p>
                @endif
                @if ($campos['texto_boton'] ?? null)
                    <a href="{{ ($campos['url_boton'] ?? '') ?: '#' }}" target="_blank" rel="noopener"
                        class="btn btn-sm btn-outline-secondary disabled" tabindex="-1" aria-disabled="true">
                        {{ $campos['texto_boton'] }}
                    </a>
                @endif
            </div>
        </div>
    @elseif ($tipo === BloqueTipoEnum::BOTON)
        <a href="{{ ($campos['url'] ?? '') ?: '#' }}" target="_blank" rel="noopener"
            class="btn btn-sm btn-outline-secondary disabled" tabindex="-1" aria-disabled="true">
            {{ $campos['texto'] ?? '' }}
        </a>
    @elseif ($tipo === BloqueTipoEnum::SWIPER)
        <div class="d-flex flex-wrap gap-2">
            @forelse ($bloque->galeria() as $media)
                <img src="{{ $media->getUrl() }}" alt="{{ $trMedia($media->getCustomProperty('alt')) }}"
                    class="rounded border" style="width: 96px; height: 64px; object-fit: cover;" loading="lazy">
            @empty
                <p class="text-muted small mb-0">{{ trans('fields.bloques.imagenes.sin_imagen') }}</p>
            @endforelse
        </div>
    @elseif ($tipo === BloqueTipoEnum::CARRUSEL_GIRATORIO)
        <div class="mb-2">
            <span class="badge text-bg-light">
                {{ trans('fields.bloques.campos.direccion') }}:
                {{ trans('fields.bloques.direcciones.' . ($campos['direccion'] ?? 'normal')) }}
            </span>
        </div>
        <div class="d-flex flex-wrap gap-3">
            @forelse ($bloque->itemsCarrusel() as $media)
                <div class="text-center">
                    <img src="{{ $media->getUrl() }}" alt="{{ $trMedia($media->getCustomProperty('etiqueta')) }}"
                        class="rounded" style="width: 48px; height: 48px; object-fit: contain;" loading="lazy">
                    <div class="small mt-1">{{ $trMedia($media->getCustomProperty('etiqueta')) }}</div>
                </div>
            @empty
                <p class="text-muted small mb-0">{{ trans('fields.bloques.imagenes.sin_imagen') }}</p>
            @endforelse
        </div>
    @elseif ($tipo === BloqueTipoEnum::TABLA)
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
                <thead>
                    <tr>
                        @foreach ($campos['columnas'] ?? [] as $columna)
                            <th scope="col">{{ $columna }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($campos['filas'] ?? [] as $fila)
                        <tr>
                            @foreach ($fila as $celda)
                                <td>{{ $celda }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
