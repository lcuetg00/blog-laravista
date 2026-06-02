{{--
    Contenedor de toasts del panel.
    Renderiza un toast Bootstrap según lo que haya en sesión:
    - session('success') -> toast verde
    - session('error')   -> toast rojo
--}}
@php
    // Tiempo (en milisegundos) que el toast permanece visible antes de auto-ocultarse
    $toastDelay = 3000;
@endphp

@if (session('success') || session('error'))
    <div class="toast-container position-fixed top-0 end-0 p-3">
        @if (session('success'))
            <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-delay="{{ $toastDelay }}">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fa-solid fa-circle-check me-2" aria-hidden="true"></i>
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="{{ trans('actions.cancel') }}"></button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-delay="{{ $toastDelay }}">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fa-solid fa-circle-exclamation me-2" aria-hidden="true"></i>
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="{{ trans('actions.cancel') }}"></button>
                </div>
            </div>
        @endif
    </div>
@endif
