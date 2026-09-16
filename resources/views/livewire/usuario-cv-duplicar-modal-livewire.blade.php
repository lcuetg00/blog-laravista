{{-- Modal de duplicación de un CV: pide un nombre nuevo que se usa para "nombre" y "nombre_archivo" de la copia --}}
<div x-on:duplicar-cv-cerrar.window="window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDuplicarCv')).hide()">
    <div class="modal fade" id="modalDuplicarCv" tabindex="-1" aria-labelledby="modalDuplicarCvTitulo" aria-hidden="true"
        wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <form wire:submit.prevent="duplicar" class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5" id="modalDuplicarCvTitulo">
                        {{ trans('fields.usuarios_cvs.duplicar_cv') }}
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('actions.cancel') }}"></button>
                </div>

                <div class="modal-body text-start">
                    <x-input name="nombre" :wire="true" :label="trans('fields.usuarios_cvs.nombre')" required />
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="duplicar">
                        <span class="spinner-border spinner-border-sm me-1" wire:loading wire:target="duplicar" aria-hidden="true"></span>
                        {{ trans('fields.usuarios_cvs.duplicar_cv') }}
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('actions.cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
