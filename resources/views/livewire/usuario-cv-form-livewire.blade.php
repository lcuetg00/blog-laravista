{{-- Modal de creación/edición del nombre de un CV, con confirmación (gestionada en el servidor) antes de cerrar si hay cambios sin guardar --}}
<div
    x-on:cv-abrir-confirmar-descarte.window="window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCvConfirmarDescarte')).show()"
    x-on:cv-confirmar-descarte-ocultar.window="window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCvConfirmarDescarte')).hide()"
    x-on:cv-cerrar.window="window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCv')).hide()">
    <div class="modal fade" id="modalCv" tabindex="-1" aria-labelledby="modalCvTitulo" aria-hidden="true" wire:ignore.self
        data-bs-backdrop="static" data-bs-keyboard="false">
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
                    <x-input name="nombre" :wire="true" :label="trans('fields.usuarios_cvs.nombre')" required />
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
</div>
