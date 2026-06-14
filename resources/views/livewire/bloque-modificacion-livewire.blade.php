{{-- Ítem de acordeón reactivo con el formulario de edición de un bloque: valida y guarda sin recargar y avisa al iframe de vista previa --}}
@php
    $campos = $bloque->tipo->campos();
    $bid = $bloque->ulid;

    // Idiomas activos (locale => nombre nativo) con el idioma actual el primero, para las pestañas del formulario
    $idiomas = \App\Helpers\IdiomaHelper::getIdiomasActivos();

    // Los campos traducibles (escalares, enum y repetidores) se editan en pestañas por idioma; la media va debajo
    $enPestanas = fn ($d) => \App\Helpers\BloqueHelper::esCampoTraducible($d) || \App\Helpers\BloqueHelper::esRepetidor($d['tipo']);
    $camposTab = array_filter($campos, $enPestanas);
    $camposPlanos = array_filter($campos, fn ($d) => !$enPestanas($d));

    // Si la validación falló, abrimos la pestaña del primer idioma con errores
    $localeActivo = array_key_first($idiomas);
    foreach (array_keys($idiomas) as $loc) {
        foreach (array_keys($camposTab) as $c) {
            if ($errors->has('campos.' . $loc . '.' . $c)) {
                $localeActivo = $loc;
                break 2;
            }
        }
    }
@endphp

<div x-data="{ borrarUuid: null, borrarNombre: '' }">
    <div class="accordion-item">
        <h3 class="accordion-header" id="cabecera-bloque-{{ $bid }}">
            <button class="accordion-button {{ $abierto ? '' : 'collapsed' }} {{ $errors->isNotEmpty() ? 'text-danger' : '' }}"
                type="button" data-bs-toggle="collapse" data-bs-target="#cuerpo-bloque-{{ $bid }}"
                aria-expanded="{{ $abierto ? 'true' : 'false' }}" aria-controls="cuerpo-bloque-{{ $bid }}">
                <span class="badge text-bg-secondary me-2">#{{ $bloque->orden }}</span>
                {{ $bloque->tipo->etiqueta() }}
                @if ($errors->isNotEmpty())
                    <i class="fa-solid fa-circle-exclamation ms-2" aria-hidden="true"></i>
                @endif
            </button>
        </h3>

        <div id="cuerpo-bloque-{{ $bid }}" class="accordion-collapse collapse {{ $abierto ? 'show' : '' }}"
            aria-labelledby="cabecera-bloque-{{ $bid }}" data-bs-parent="#acordeonBloques">
            <div class="accordion-body">
                <form wire:submit="update">
                    @if (count($camposTab) > 0)
                        {{-- Pestañas por idioma: cada pane mantiene sus inputs en el DOM y wire:model sincroniza los tres idiomas --}}
                        <ul class="nav nav-tabs" id="idiomasTab-{{ $bid }}" role="tablist">
                            @foreach ($idiomas as $locale => $nombreIdioma)
                                @php
                                    $tieneError = false;
                                    foreach (array_keys($camposTab) as $c) {
                                        if ($errors->has('campos.' . $locale . '.' . $c)) {
                                            $tieneError = true;
                                            break;
                                        }
                                    }
                                @endphp
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $locale === $localeActivo ? 'active' : '' }} {{ $tieneError ? 'text-danger' : '' }}"
                                        id="tab-{{ $bid }}-{{ $locale }}" data-bs-toggle="tab"
                                        data-bs-target="#pane-{{ $bid }}-{{ $locale }}" type="button" role="tab"
                                        aria-controls="pane-{{ $bid }}-{{ $locale }}"
                                        aria-selected="{{ $locale === $localeActivo ? 'true' : 'false' }}">
                                        {{ $nombreIdioma }}
                                        @if ($tieneError)
                                            <i class="fa-solid fa-circle-exclamation ms-1" aria-hidden="true"></i>
                                        @endif
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content border border-top-0 rounded-bottom p-3 mb-3">
                            @foreach ($idiomas as $locale => $nombreIdioma)
                                <div class="tab-pane fade {{ $locale === $localeActivo ? 'show active' : '' }}"
                                    id="pane-{{ $bid }}-{{ $locale }}" role="tabpanel"
                                    aria-labelledby="tab-{{ $bid }}-{{ $locale }}" tabindex="0">
                                    @foreach ($camposTab as $clave => $definicion)
                                        @include('livewire.partials.bloque-campo', [
                                            'bloque' => $bloque,
                                            'clave' => $clave,
                                            'definicion' => $definicion,
                                            'locale' => $locale,
                                        ])
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @foreach ($camposPlanos as $clave => $definicion)
                        @include('livewire.partials.bloque-campo', [
                            'bloque' => $bloque,
                            'clave' => $clave,
                            'definicion' => $definicion,
                        ])
                    @endforeach

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="update">
                            <span class="spinner-border spinner-border-sm me-1" wire:loading wire:target="update" aria-hidden="true"></span>
                            <i class="fa-solid fa-floppy-disk me-1" wire:loading.remove wire:target="update" aria-hidden="true"></i>
                            {{ trans('actions.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal único de confirmación de borrado de imagen del bloque: cada tarjeta fija en Alpine el uuid y el nombre del fichero antes de abrirlo --}}
    <div class="modal fade" id="modal-borrar-imagen-{{ $bid }}" tabindex="-1"
        aria-labelledby="modal-borrar-imagen-{{ $bid }}-titulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5" id="modal-borrar-imagen-{{ $bid }}-titulo">
                        {{ trans('fields.bloques.imagenes.borrar_titulo') }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ trans('actions.cancel') }}"></button>
                </div>
                <div class="modal-body">
                    {{ trans('fields.bloques.imagenes.borrar_confirmar') }}
                    <p class="fw-semibold mb-0 mt-2" x-text="borrarNombre"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ trans('actions.cancel') }}
                    </button>
                    {{-- Cerramos el modal al confirmar (data-bs-dismiss) para que el backdrop no quede colgado mientras Livewire procesa el borrado --}}
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                        x-on:click="$wire.borrarImagen(borrarUuid)">
                        {{ trans('actions.accept') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
