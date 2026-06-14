{{--
    Contenedor de toasts del panel.
    Renderiza los toasts del flash de sesión (recargas normales); panel.js los muestra con initToasts()
    e inyecta sobre este mismo contenedor los avisos en vivo de Livewire (evento 'toast').
--}}
@php
    // Toasts iniciales del flash de sesión (recargas normales, sin Livewire)
    $iniciales = collect([
        ['tipo' => 'success', 'mensaje' => session('success')],
        ['tipo' => 'error', 'mensaje' => session('error')],
    ])
        ->filter(fn ($t) => filled($t['mensaje']))
        ->values();
@endphp

{{-- data-cerrar-label: texto traducido del botón de cierre que panel.js reutiliza en los toasts que crea en vivo --}}
<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3"
    data-cerrar-label="{{ trans('actions.close') }}">
    @foreach ($iniciales as $toast)
        <div class="toast toast-panel align-items-center shadow {{ $toast['tipo'] === 'error' ? 'toast-danger' : 'toast-success' }}"
            role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
            <div class="d-flex align-items-center">
                <div class="toast-body d-flex align-items-center gap-2">
                    {{-- Icono dentro de un chip circular para un aspecto más desenfadado --}}
                    <span class="toast-icon shadow">
                        <i class="fa-solid {{ $toast['tipo'] === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check' }}" aria-hidden="true"></i>
                    </span>
                    <span>{{ $toast['mensaje'] }}</span>
                </div>
                <button type="button" class="toast-close shadow me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="{{ trans('actions.close') }}">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>
            {{-- Barra de progreso (Bootstrap) que se vacía durante el tiempo de autocierre (sincronizada con data-bs-delay vía --toast-duration) --}}
            <div class="progress toast-progress" aria-hidden="true">
                <div class="progress-bar progress-bar-striped"></div>
            </div>
        </div>
    @endforeach
</div>
