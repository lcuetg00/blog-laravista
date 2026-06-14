{{-- Renderiza un único campo editable de un bloque con enlace Livewire (wire:model). Parámetros: $bloque, $clave, $definicion y, para campos traducibles, $locale --}}
@php
    $locale = $locale ?? null;

    $tipo = $definicion['tipo'];
    $requerido = $definicion['requerido'] ?? false;
    $etiqueta = trans('fields.bloques.campos.' . $clave);

    // Los campos de imagen (única, galería o carrusel etiquetado) se gestionan como ficheros subidos, fuera del JSON campos
    $esMedia = in_array($tipo, ['imagen', 'galeria', 'galeria_etiquetada'], true);
    $esRepetidor = in_array($tipo, ['repetidor_json', 'repetidor_traducible', 'matriz_traducible'], true);

    // Id base del campo (los de pestaña añaden el sufijo del idioma)
    $id = 'campo_' . $bloque->ulid . '_' . $clave;

    if (!$esMedia) {
        // Todo campo no-media (escalar, enum o repetidor) es traducible y se enlaza por idioma
        $wire = 'campos.' . $locale . '.' . $clave;
        $id .= '_' . $locale;

        $error = $errors->first($wire);
    }
@endphp

<div class="mb-3">
    @if ($esMedia)
        @php
            // Idiomas activos (locale => nombre nativo) para los alt/títulos de las imágenes (sin reordenar el actual al inicio)
            $idiomas = \App\Helpers\IdiomaHelper::getIdiomasActivos(false);
            $coleccion = $definicion['coleccion'] ?? null;
            $formatos = implode(', ', array_keys(\App\Helpers\ValidacionHelper::MIME_TYPES_IMAGEN));
            $maxMb = \App\Helpers\ValidacionHelper::MAX_KB_IMAGEN / 1024;

            // Prefijo de la propiedad Livewire de fichero según el tipo de campo de medios (para filtrar sus errores)
            $campoFichero = match ($tipo) {
                'imagen' => 'imagenes.' . $clave,
                'galeria' => 'galeriaNuevas',
                'galeria_etiquetada' => 'nuevosItems',
            };
            $erroresMedia = collect($errors->messages())
                ->filter(
                    fn($mensajes, $campo) => $campo === $campoFichero || str_starts_with($campo, $campoFichero . '.'),
                )
                ->flatten();

            // Id del modal de borrado compartido del bloque (uno por bloque, no por imagen)
            $modalBorrar = 'modal-borrar-imagen-' . $bloque->ulid;

            $imagenMedia = $tipo === 'imagen' ? $bloque->getFirstMedia($coleccion) : null;

            $existentes = in_array($tipo, ['galeria', 'galeria_etiquetada'], true)
                ? $bloque->getMedia($coleccion)
                : collect();
        @endphp

        <p @class(['form-label', 'fw-semibold', 'required' => $requerido])>{{ $etiqueta }}</p>

        @if ($tipo === 'imagen')
            @if ($imagenMedia)
                {{-- Tarjeta de la imagen con su franja de borrado (abre el modal compartido del bloque) --}}
                <div class="mb-2" style="max-width: 220px;">
                    @include('livewire.partials.bloque-imagen-card', ['media' => $imagenMedia, 'modalBorrar' => $modalBorrar])
                </div>
            @else
                <p class="text-muted small mb-2">{{ trans('fields.bloques.imagenes.sin_imagen') }}</p>
            @endif

            <label for="{{ $id }}" class="form-label">{{ trans('fields.bloques.imagenes.subir') }}</label>
            <input type="file" accept="image/*" id="{{ $id }}" wire:model="imagenes.{{ $clave }}"
                class="form-control @if ($erroresMedia->isNotEmpty()) is-invalid @endif">
        @elseif ($tipo === 'galeria')
            @if ($existentes->isNotEmpty())
                <p class="form-label mb-1">{{ trans('fields.bloques.imagenes.existentes') }}</p>
                <div class="row g-3 mb-3">
                    @foreach ($existentes as $media)
                        <div class="col-6 col-md-4 col-lg-3" wire:key="media-{{ $media->uuid }}">
                            <div class="h-100 d-flex flex-column gap-2">
                                {{-- Tarjeta de la imagen con su franja de borrado (abre el modal compartido del bloque) --}}
                                @include('livewire.partials.bloque-imagen-card', ['media' => $media, 'modalBorrar' => $modalBorrar])
                                @foreach ($idiomas as $idioma => $nombreIdioma)
                                    <div>
                                        <label class="form-label small mb-0"
                                            for="alt_{{ $bloque->ulid }}_{{ $media->uuid }}_{{ $idioma }}">
                                            {{ trans('fields.bloques.imagenes.alt') }} ({{ $nombreIdioma }})
                                        </label>
                                        <input type="text" maxlength="255"
                                            id="alt_{{ $bloque->ulid }}_{{ $media->uuid }}_{{ $idioma }}"
                                            class="form-control form-control-sm"
                                            wire:model="alts.{{ $media->uuid }}.{{ $idioma }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Subida de varias imágenes a la vez; el alt de cada una se rellena luego en la lista de existentes --}}
            <label for="{{ $id }}" class="form-label">{{ trans('fields.bloques.imagenes.nuevas') }}</label>
            <input type="file" accept="image/*" multiple id="{{ $id }}" wire:model="galeriaNuevas"
                class="form-control @if ($erroresMedia->isNotEmpty()) is-invalid @endif">
        @else
            {{-- Carrusel etiquetado: cada item es una imagen con su título traducible; se editan los existentes y se añaden filas nuevas --}}
            @if ($existentes->isNotEmpty())
                <p class="form-label mb-1">{{ trans('fields.bloques.imagenes.existentes') }}</p>
                <div class="row g-3 mb-3">
                    @foreach ($existentes as $media)
                        <div class="col-12 col-md-6 col-lg-4" wire:key="item-{{ $media->uuid }}">
                            <div class="h-100 d-flex flex-column gap-2">
                                {{-- Tarjeta de la imagen con su franja de borrado (abre el modal compartido del bloque) --}}
                                @include('livewire.partials.bloque-imagen-card', ['media' => $media, 'modalBorrar' => $modalBorrar])
                                @php
                                    // Pestaña de idioma activa del item: la primera, salvo que algún campo traducible del item tenga error en otro idioma
                                    $tabBaseItem = 'item-' . $bloque->ulid . '-' . $media->uuid;
                                    $localeActivoItem = array_key_first($idiomas);
                                    foreach (array_keys($idiomas) as $loc) {
                                        if ($errors->has('etiquetas.' . $media->uuid . '.' . $loc)) {
                                            $localeActivoItem = $loc;
                                            break;
                                        }
                                    }
                                @endphp
                                {{-- Pestañas de idioma del item: agrupan todos sus campos traducibles (de momento solo la etiqueta) --}}
                                <ul class="nav nav-tabs" role="tablist">
                                    @foreach ($idiomas as $idioma => $nombreIdioma)
                                        @php $errItem = $errors->has('etiquetas.' . $media->uuid . '.' . $idioma); @endphp
                                        <li class="nav-item" role="presentation">
                                            <button
                                                class="nav-link py-1 px-2 small {{ $idioma === $localeActivoItem ? 'active' : '' }} {{ $errItem ? 'text-danger' : '' }}"
                                                id="{{ $tabBaseItem }}-tab-{{ $idioma }}" data-bs-toggle="tab"
                                                data-bs-target="#{{ $tabBaseItem }}-pane-{{ $idioma }}"
                                                type="button" role="tab"
                                                aria-controls="{{ $tabBaseItem }}-pane-{{ $idioma }}"
                                                aria-selected="{{ $idioma === $localeActivoItem ? 'true' : 'false' }}">
                                                {{ $nombreIdioma }}
                                                @if ($errItem)
                                                    <i class="fa-solid fa-circle-exclamation ms-1"
                                                        aria-hidden="true"></i>
                                                @endif
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content border border-top-0 rounded-bottom p-2">
                                    @foreach ($idiomas as $idioma => $nombreIdioma)
                                        @php $errEtiqueta = $errors->first('etiquetas.' . $media->uuid . '.' . $idioma); @endphp
                                        <div class="tab-pane fade {{ $idioma === $localeActivoItem ? 'show active' : '' }}"
                                            id="{{ $tabBaseItem }}-pane-{{ $idioma }}" role="tabpanel"
                                            aria-labelledby="{{ $tabBaseItem }}-tab-{{ $idioma }}" tabindex="0">
                                            {{-- Campos traducibles del item para este idioma --}}
                                            <div>
                                                <label class="form-label small mb-0"
                                                    for="etiqueta_{{ $bloque->ulid }}_{{ $media->uuid }}_{{ $idioma }}">
                                                    {{ trans('fields.bloques.carrusel.etiqueta') }}
                                                </label>
                                                <input type="text" maxlength="100"
                                                    id="etiqueta_{{ $bloque->ulid }}_{{ $media->uuid }}_{{ $idioma }}"
                                                    class="form-control form-control-sm @if ($errEtiqueta) is-invalid @endif"
                                                    wire:model="etiquetas.{{ $media->uuid }}.{{ $idioma }}">
                                                @if ($errEtiqueta)
                                                    <div class="invalid-feedback d-block">{{ $errEtiqueta }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Filas nuevas pendientes de guardar: imagen + título por idioma, con botón para quitar cada una --}}
            @foreach ($nuevosItems as $i => $item)
                @php $errImagen = $errors->first('nuevosItems.' . $i . '.imagen'); @endphp
                <div class="border rounded p-3 mb-3 d-flex flex-column gap-2"
                    wire:key="nuevo-item-{{ $i }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold small">{{ trans('fields.bloques.carrusel.nuevo_item') }}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            wire:click="quitarItem({{ $i }})"
                            aria-label="{{ trans('fields.bloques.carrusel.quitar') }}">
                            <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                        </button>
                    </div>

                    @if (!empty($item['imagen']) && $item['imagen']->isPreviewable())
                        <img src="{{ $item['imagen']->temporaryUrl() }}" alt=""
                            class="rounded border align-self-start" style="max-width: 120px; height: auto;">
                    @endif

                    <div>
                        <label for="nuevo_item_{{ $bloque->ulid }}_{{ $i }}"
                            class="form-label small mb-0">{{ trans('fields.bloques.carrusel.imagen') }}</label>
                        <input type="file" accept="image/*" id="nuevo_item_{{ $bloque->ulid }}_{{ $i }}"
                            class="form-control form-control-sm @if ($errImagen) is-invalid @endif"
                            wire:model="nuevosItems.{{ $i }}.imagen">
                        @if ($errImagen)
                            <div class="invalid-feedback d-block">{{ $errImagen }}</div>
                        @endif
                    </div>

                    @php
                        // Pestaña de idioma activa del item nuevo: la primera, salvo que algún campo traducible del item tenga error en otro idioma
                        $tabBaseNuevo = 'nuevo-item-' . $bloque->ulid . '-' . $i;
                        $localeActivoNuevo = array_key_first($idiomas);
                        foreach (array_keys($idiomas) as $loc) {
                            if ($errors->has('nuevosItems.' . $i . '.etiqueta.' . $loc)) {
                                $localeActivoNuevo = $loc;
                                break;
                            }
                        }
                    @endphp
                    {{-- Pestañas de idioma del item: agrupan todos sus campos traducibles (de momento solo la etiqueta) --}}
                    <ul class="nav nav-tabs" role="tablist">
                        @foreach ($idiomas as $idioma => $nombreIdioma)
                            @php $errItem = $errors->has('nuevosItems.' . $i . '.etiqueta.' . $idioma); @endphp
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link py-1 px-2 small {{ $idioma === $localeActivoNuevo ? 'active' : '' }} {{ $errItem ? 'text-danger' : '' }}"
                                    id="{{ $tabBaseNuevo }}-tab-{{ $idioma }}" data-bs-toggle="tab"
                                    data-bs-target="#{{ $tabBaseNuevo }}-pane-{{ $idioma }}" type="button" role="tab"
                                    aria-controls="{{ $tabBaseNuevo }}-pane-{{ $idioma }}"
                                    aria-selected="{{ $idioma === $localeActivoNuevo ? 'true' : 'false' }}">
                                    {{ $nombreIdioma }}
                                    @if ($errItem)
                                        <i class="fa-solid fa-circle-exclamation ms-1" aria-hidden="true"></i>
                                    @endif
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    <div class="tab-content border border-top-0 rounded-bottom p-2">
                        @foreach ($idiomas as $idioma => $nombreIdioma)
                            @php $errTitulo = $errors->first('nuevosItems.' . $i . '.etiqueta.' . $idioma); @endphp
                            <div class="tab-pane fade {{ $idioma === $localeActivoNuevo ? 'show active' : '' }}"
                                id="{{ $tabBaseNuevo }}-pane-{{ $idioma }}" role="tabpanel"
                                aria-labelledby="{{ $tabBaseNuevo }}-tab-{{ $idioma }}" tabindex="0">
                                {{-- Campos traducibles del item para este idioma --}}
                                <div>
                                    <label class="form-label small mb-0"
                                        for="nuevo_item_etiqueta_{{ $bloque->ulid }}_{{ $i }}_{{ $idioma }}">
                                        {{ trans('fields.bloques.carrusel.etiqueta') }}
                                    </label>
                                    <input type="text" maxlength="100"
                                        id="nuevo_item_etiqueta_{{ $bloque->ulid }}_{{ $i }}_{{ $idioma }}"
                                        class="form-control form-control-sm @if ($errTitulo) is-invalid @endif"
                                        wire:model="nuevosItems.{{ $i }}.etiqueta.{{ $idioma }}">
                                    @if ($errTitulo)
                                        <div class="invalid-feedback d-block">{{ $errTitulo }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Botón para añadir otra fila de item al carrusel --}}
            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="agregarItem">
                <i class="fa-solid fa-plus me-1"
                    aria-hidden="true"></i>{{ trans('fields.bloques.carrusel.agregar') }}
            </button>

            @error('nuevosItems')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        @endif

        {{-- Indicador de subida mientras Livewire transfiere el fichero al servidor temporal --}}
        <div class="form-text" wire:loading wire:target="imagenes, galeriaNuevas, nuevosItems">
            <span class="spinner-border spinner-border-sm me-1"
                aria-hidden="true"></span>{{ trans('actions.save') }}…
        </div>
        <div class="form-text">
            {{ trans('fields.bloques.imagenes.formatos_ayuda', ['formatos' => $formatos, 'max' => $maxMb]) }}</div>

        @foreach ($erroresMedia as $mensaje)
            <div class="invalid-feedback d-block">{{ $mensaje }}</div>
        @endforeach
    @elseif ($esRepetidor)
        <label for="{{ $id }}" @class(['form-label', 'required' => $requerido])>{{ $etiqueta }}</label>
        <textarea id="{{ $id }}" wire:model="{{ $wire }}" rows="8"
            class="form-control font-monospace @if ($error) is-invalid @endif"
            aria-describedby="{{ $id }}-help"></textarea>
        <div id="{{ $id }}-help" class="form-text">{{ trans('fields.bloques.json_aviso') }}</div>
        @if ($error)
            <div class="invalid-feedback d-block">{{ $error }}</div>
        @endif
    @elseif ($tipo === 'enum_local')
        <label for="{{ $id }}" @class(['form-label', 'required' => $requerido])>{{ $etiqueta }}</label>
        <select id="{{ $id }}" wire:model="{{ $wire }}"
            class="form-select @if ($error) is-invalid @endif">
            @foreach ($definicion['valores'] as $opcion)
                <option value="{{ $opcion }}">{{ trans('fields.bloques.direcciones.' . $opcion) }}</option>
            @endforeach
        </select>
        @if ($error)
            <div class="invalid-feedback d-block">{{ $error }}</div>
        @endif
    @elseif ($tipo === 'text')
        <label for="{{ $id }}" @class(['form-label', 'required' => $requerido])>{{ $etiqueta }}</label>
        <textarea id="{{ $id }}" wire:model="{{ $wire }}" rows="3"
            class="form-control @if ($error) is-invalid @endif"></textarea>
        @if ($error)
            <div class="invalid-feedback d-block">{{ $error }}</div>
        @endif
    @else
        {{-- string / url: reutilizamos el componente de input enlazándolo a la propiedad Livewire --}}
        <x-input :name="$wire" :id="$id" wire :label="$etiqueta" :type="$tipo === 'url' ? 'url' : 'text'" :required="$requerido"
            :maxlength="$definicion['max'] ?? null" />
    @endif
</div>
