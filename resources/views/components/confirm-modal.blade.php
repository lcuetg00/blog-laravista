{{-- Modal genérico de confirmación reutilizable para cualquier acción de CRUD --}}
@props([
    'id',
    // URL del endpoint al que se envía el formulario al confirmar.
    'action',
    // Método HTTP del formulario (Debe ser uno de los casos de HttpMethodEnum: POST, PUT, PATCH, DELETE).
    'method',
    // Título del modal
    'title' => trans('actions.delete_confirm_title'),
    // Texto del cuerpo del modal
    'description' => trans('actions.delete_confirm_description'),
    // Texto del botón de aceptar
    'acceptLabel' => trans('actions.accept'),
    // Clases del botón de aceptar ("btn-danger" para borrado, "btn-success" para otro tipo de métodos).
    'acceptClass',
    // Texto del botón de cancelar (por defecto "Cancelar").
    'cancelLabel' => trans('actions.cancel'),
])

@php
    // Validamos que el method recibido sea uno de los aceptados
    $modalMethod = \App\Enums\HttpMethodEnum::from(strtoupper($method))->value;
@endphp

<div class="modal fade" id="confirmModal-{{ $id }}" tabindex="-1"
    aria-labelledby="confirmModalLabel-{{ $id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="confirmModalLabel-{{ $id }}">
                    {{ $title }}
                </h2>
            </div>
            <div class="modal-body">
                {{ $description }}
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <form method="POST" action="{{ $action }}" class="m-0">
                    @csrf
                    @if ($modalMethod !== 'POST')
                        @method($modalMethod)
                    @endif
                    <button type="submit" class="btn {{ $acceptClass }}">{{ $acceptLabel }}</button>
                </form>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ $cancelLabel }}
                </button>
            </div>
        </div>
    </div>
</div>
