{{-- Formulario reactivo de datos de la página: título y descripción por idioma + estado activo. Valida y guarda sin recargar y avisa al iframe de vista previa --}}
@php
    // Idiomas activos (locale => nombre nativo) con el idioma actual el primero, para las pestañas del formulario
    $idiomas = \App\Helpers\IdiomaHelper::getIdiomasActivos();

    // Si hay errores de validación en algún idioma, abrimos por defecto la pestaña de ese idioma para que el usuario los vea sin tener que cambiar de pestaña
    $localeActivo = array_key_first($idiomas);
    foreach (array_keys($idiomas) as $idiona) {
        if ($errors->has('titulo.' . $idiona) || $errors->has('descripcion.' . $idiona)) {
            $localeActivo = $idiona;
            break;
        }
    }
@endphp

<div>
    <form wire:submit="update">
        <div class="card shadow">
            <div class="card-header">
                <h2 class="h5 mb-0">{{ trans('fields.paginas.datos') }}</h2>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-9">
                        {{-- La clave es inmutable: la mostramos solo lectura para que el editor sepa qué página está editando --}}
                        <label for="clave" class="form-label">{{ trans('fields.input.clave') }}</label>
                        <input type="text" id="clave" class="form-control" value="{{ $pagina->clave }}" readonly
                            disabled aria-describedby="clave-help">
                        <div id="clave-help" class="form-text">{{ trans('fields.paginas.clave_inmutable_aviso') }}</div>
                    </div>

                    <div class="col-12 col-md-3">
                        {{-- El interruptor de activo solo se muestra en páginas desactivables --}}
                        <label for="activo" class="form-label">{{ trans('fields.input.activo') }}</label>
                        @if ($pagina->es_desactivable)
                            <div class="form-check form-switch">
                                <input class="form-check-input @error('activo') is-invalid @enderror" type="checkbox"
                                    role="switch" id="activo" wire:model="activo">
                                @error('activo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <p class="form-text mb-0">
                                {{ trans('fields.paginas.no_desactivable_aviso', ['pagina' => $pagina->titulo]) }}</p>
                        @endif
                    </div>
                </div>

                {{-- Selector de pestañas por idioma, wire:model sincroniza los tres idiomas --}}
                <ul class="nav nav-tabs mt-4" id="idiomasTab" role="tablist">
                    @foreach ($idiomas as $locale => $nombreIdioma)
                        @php($tieneError = $errors->has('titulo.' . $locale) || $errors->has('descripcion.' . $locale))
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link {{ $locale === $localeActivo ? 'active' : '' }} {{ $tieneError ? 'text-danger' : '' }}"
                                id="tab-{{ $locale }}" data-bs-toggle="tab"
                                data-bs-target="#pane-{{ $locale }}" type="button" role="tab"
                                aria-controls="pane-{{ $locale }}"
                                aria-selected="{{ $locale === $localeActivo ? 'true' : 'false' }}">
                                {{ $nombreIdioma }}
                                @if ($tieneError)
                                    <i class="fa-solid fa-circle-exclamation ms-1" aria-hidden="true"></i>
                                @endif
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content border border-top-0 rounded-bottom p-3">
                    @foreach ($idiomas as $locale => $nombreIdioma)
                        <div class="tab-pane fade {{ $locale === $localeActivo ? 'show active' : '' }}"
                            id="pane-{{ $locale }}" role="tabpanel" aria-labelledby="tab-{{ $locale }}"
                            tabindex="0">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <x-input :name="'titulo.' . $locale" wire :label="trans('fields.input.titulo')" maxlength="255"
                                        required />
                                </div>

                                <div class="col-12">
                                    <label for="descripcion_{{ $locale }}"
                                        class="form-label">{{ trans('fields.input.descripcion') }}</label>
                                    <textarea id="descripcion_{{ $locale }}" rows="3" wire:model="descripcion.{{ $locale }}"
                                        class="form-control @error('descripcion.' . $locale) is-invalid @enderror"
                                        @error('descripcion.' . $locale) aria-describedby="descripcion_{{ $locale }}-error" @enderror></textarea>
                                    @error('descripcion.' . $locale)
                                        <div id="descripcion_{{ $locale }}-error" class="invalid-feedback d-block">
                                            {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="update">
                    <span class="spinner-border spinner-border-sm me-1" wire:loading wire:target="update"
                        aria-hidden="true"></span>
                    <i class="fa-solid fa-floppy-disk me-1" wire:loading.remove wire:target="update"
                        aria-hidden="true"></i>
                    {{ trans('actions.save') }}
                </button>
            </div>
        </div>
    </form>
</div>
