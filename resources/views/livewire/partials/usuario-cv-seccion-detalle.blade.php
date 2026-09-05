{{-- Formulario de edición de la sección seleccionada del CV: título, descripción y galería de imágenes --}}
@use('App\Helpers\PermissionHelper')

@php
    $sid = $seccion->ulid;
@endphp

<div x-data="{ borrarUuid: null, borrarNombre: '' }">
    <form wire:submit="guardar">
        <div class="mb-3">
            <label for="titulo-{{ $sid }}" class="form-label required">
                {{ trans('fields.input.titulo') }}
            </label>
            <input type="text" id="titulo-{{ $sid }}" name="titulo" wire:model.live.debounce.500ms="titulo"
                class="form-control @error('titulo') is-invalid @enderror" required>
            @error('titulo')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <x-input name="descripcion" :wire="true" rich-text :label="trans('fields.input.descripcion')" />
        </div>

        <div class="mb-3">
            <label class="form-label">{{ trans('fields.usuarios_cvs.secciones.imagenes.titulo') }}</label>

            @if ($seccion->galeria()->isEmpty())
                <p class="text-muted small mb-2">{{ trans('fields.usuarios_cvs.secciones.imagenes.sin_imagen') }}</p>
            @else
                <div class="row g-2 mb-2">
                    @foreach ($seccion->galeria() as $media)
                        <div class="col-6 col-md-3">
                            <div class="position-relative">
                                <img src="{{ $media->getUrl() }}" alt="" class="img-fluid rounded border">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                    data-bs-toggle="modal" data-bs-target="#modal-borrar-imagen-{{ $sid }}"
                                    x-on:click="borrarUuid = '{{ $media->uuid }}'; borrarNombre = @js($media->file_name)"
                                    aria-label="{{ trans('fields.usuarios_cvs.secciones.imagenes.borrar') }}">
                                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <input type="file" accept="image/*" multiple wire:model="galeriaNuevas"
                class="form-control @error('galeriaNuevas.*') is-invalid @enderror">
            @error('galeriaNuevas.*')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between">
            @can(PermissionHelper::USUARIOS_CVS_ELIMINAR_PERMISSION)
                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                    data-bs-target="#modal-eliminar-seccion-{{ $sid }}">
                    <i class="fa-solid fa-trash-can me-1" aria-hidden="true"></i>
                    {{ trans('actions.delete') }}
                </button>
            @endcan

            <button type="submit" class="btn btn-primary btn-sm ms-auto" wire:loading.attr="disabled"
                wire:target="guardar">
                <span class="spinner-border spinner-border-sm me-1" wire:loading wire:target="guardar"
                    aria-hidden="true"></span>
                {{ trans('actions.save') }}
            </button>
        </div>
    </form>

    {{-- Modal de confirmación de borrado de imagen de la sección --}}
    <div class="modal fade" id="modal-borrar-imagen-{{ $sid }}" tabindex="-1"
        aria-labelledby="modal-borrar-imagen-{{ $sid }}-titulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5" id="modal-borrar-imagen-{{ $sid }}-titulo">
                        {{ trans('fields.usuarios_cvs.secciones.imagenes.borrar') }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ trans('actions.cancel') }}"></button>
                </div>
                <div class="modal-body">
                    {{ trans('fields.usuarios_cvs.secciones.imagenes.modal.borrar_confirmar') }}
                    <p class="fw-semibold mb-0 mt-2" x-text="borrarNombre"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ trans('actions.cancel') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                        x-on:click="$wire.borrarImagen(borrarUuid)">
                        {{ trans('actions.accept') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de confirmación de borrado de la sección --}}
    <div class="modal fade" id="modal-eliminar-seccion-{{ $sid }}" tabindex="-1"
        aria-labelledby="modal-eliminar-seccion-{{ $sid }}-titulo" aria-hidden="true"
        data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5" id="modal-eliminar-seccion-{{ $sid }}-titulo">
                        {{ trans('fields.usuarios_cvs.secciones.modal.eliminar_titulo') }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ trans('actions.cancel') }}"></button>
                </div>
                <div class="modal-body">
                    {{ trans('fields.usuarios_cvs.secciones.modal.eliminar_confirm') }}
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" wire:click="eliminar">
                        {{ trans('actions.delete') }}
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ trans('actions.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
