{{-- Modal de creación/edición del nombre de un CV, con confirmación (gestionada en el servidor) antes de cerrar si hay cambios sin guardar --}}
@use('App\Helpers\PermissionHelper')

{{-- Estos tres eventos los dispara el servidor con dispatch() desde el componente Livewire, no el HTML. La lógica PHP decide
cuándo abrir o cerrar cada modal (si hay cambios sin guardar, acción pendiente...) y Alpine solo escucha el evento en window
y llama a la instancia de Bootstrap a mano, en lugar de usar los atributos data-bs-* --}}
<div
    x-on:cv-abrir-confirmar-descarte.window="window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCvConfirmarDescarte')).show()"
    x-on:cv-confirmar-descarte-ocultar.window="window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCvConfirmarDescarte')).hide()"
    x-on:cv-cerrar.window="window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCv')).hide()">
    {{-- data-bs-focus="false": desactiva el focus-trap de Bootstrap, que si no le robaría el foco al panel de vista previa del PDF (vive fuera del modal) en cuanto se hiciera clic en sus botones --}}
    <div class="modal fade" id="modalCv" tabindex="-1" aria-labelledby="modalCvTitulo" aria-hidden="true" wire:ignore.self
        data-bs-backdrop="static" data-bs-keyboard="false" data-bs-focus="false">
        <div class="modal-dialog modal-dialog-centered">
            <form wire:submit.prevent="guardar" class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5" id="modalCvTitulo">
                        {{ $cvUlid === null ? trans('fields.usuarios_cvs.crear_cv') : trans('actions.edit') }}
                    </h2>
                    <button type="button" class="btn-close" aria-label="{{ trans('actions.cancel') }}"
                        wire:click="intentarCerrarModal"></button>
                </div>

                <div class="modal-body text-start">
                    <div class="mb-3">
                        <x-input name="nombre" :wire="true" :label="trans('fields.usuarios_cvs.nombre')" required />
                    </div>

                    <div class="mb-3">
                        <x-input name="nombreArchivo" :wire="true" :label="trans('fields.usuarios_cvs.nombre_archivo')" />
                        <div class="form-text">{{ trans('fields.usuarios_cvs.nombre_archivo_ayuda') }}</div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <x-input name="colorPrimario" :wire="true" type="color" :label="trans('fields.usuarios_cvs.color_primario')" required />
                            <div class="form-text">{{ trans('fields.usuarios_cvs.color_primario_ayuda') }}</div>
                        </div>
                        <div class="col-6">
                            <x-input name="colorSecundario" :wire="true" type="color" :label="trans('fields.usuarios_cvs.color_secundario')" required />
                            <div class="form-text">{{ trans('fields.usuarios_cvs.color_secundario_ayuda') }}</div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-6">
                            <label for="fontSizeCabecera" class="form-label required">{{ trans('fields.usuarios_cvs.font_size_cabecera') }}</label>
                            <select id="fontSizeCabecera" wire:model="fontSizeCabecera"
                                class="form-select @error('fontSizeCabecera') is-invalid @enderror">
                                @foreach (\App\Enums\FontSizeEnum::cases() as $tamano)
                                    <option value="{{ $tamano->value }}">{{ $tamano->etiqueta() }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">{{ trans('fields.usuarios_cvs.font_size_cabecera_ayuda') }}</div>
                            @error('fontSizeCabecera')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label for="fontSizeContenido" class="form-label required">{{ trans('fields.usuarios_cvs.font_size_contenido') }}</label>
                            <select id="fontSizeContenido" wire:model="fontSizeContenido"
                                class="form-select @error('fontSizeContenido') is-invalid @enderror">
                                @foreach (\App\Enums\FontSizeEnum::cases() as $tamano)
                                    <option value="{{ $tamano->value }}">{{ $tamano->etiqueta() }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">{{ trans('fields.usuarios_cvs.font_size_contenido_ayuda') }}</div>
                            @error('fontSizeContenido')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="guardar">
                        <span class="spinner-border spinner-border-sm me-1" wire:loading wire:target="guardar" aria-hidden="true"></span>
                        {{ trans('actions.save') }}
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('actions.cancel') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal de confirmación al cerrar con cambios sin guardar --}}
    <div class="modal fade" id="modalCvConfirmarDescarte" tabindex="-1" aria-labelledby="modalCvConfirmarDescarteTitulo"
        aria-hidden="true" wire:ignore.self data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h6" id="modalCvConfirmarDescarteTitulo">
                        {{ trans('fields.usuarios_cvs.modal.descartar_cambios_titulo') }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('actions.cancel') }}"></button>
                </div>
                <div class="modal-body">
                    {{ trans('fields.usuarios_cvs.modal.descartar_cambios_descripcion') }}
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" wire:click="confirmarDescarte">
                        {{ trans('actions.discard_changes') }}
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('actions.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de confirmación de borrado de un CV --}}
    <div class="modal fade" id="modalEliminarCv" tabindex="-1" aria-labelledby="modalEliminarCvTitulo" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5" id="modalEliminarCvTitulo">
                        {{ trans('fields.usuarios_cvs.modal.eliminar_confirm_title') }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('actions.cancel') }}"></button>
                </div>
                <div class="modal-body">
                    {{ trans('fields.usuarios_cvs.modal.eliminar_confirm_description') }}
                    <p class="mb-0 mt-2">
                        {{ trans('fields.usuarios_cvs.modal.eliminar_confirm_registro_label') }} {{ $this->cvEliminar?->nombre }}
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ trans('actions.cancel') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" wire:click="eliminarCv">
                        {{ trans('actions.accept') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Panel de vista previa del PDF: mostrado/ocultado por JS según el estado real del modal del formulario (livewire-bridge.js) --}}
    @can(PermissionHelper::USUARIOS_CVS_GENERAR_PDF_PERMISSION)
        @if ($this->cv !== null)
            @include('livewire.partials.usuario-cv-preview', ['cv' => $this->cv, 'usuario' => $usuario, 'panelId' => 'cvPreviewForm'])
        @endif
    @endcan
</div>
