{{--
    Vista previa del PDF de un CV: panel flotante arrastrable, redimensionable y minimizable (ver preview.js).
    wire:ignore para que no lo refresque por livewire
--}}
<section id="{{ $panelId }}" class="preview-panel shadow" wire:ignore role="dialog" aria-modal="false"
    aria-label="{{ trans('fields.usuarios_cvs.preview.titulo') }}" tabindex="-1">
    <header class="preview-panel-header" data-preview-drag>
        <span class="preview-panel-title text-truncate">
            <i class="fa-solid fa-up-down-left-right me-2" aria-hidden="true"></i>
            {{-- El texto se refresca en vivo (data-preview-title) cuando cambia el CV en edición --}}
            <span data-preview-title>{{ $cv->nombre }}</span>
        </span>
        <span class="preview-panel-actions">
            <a href="{{ route('panel.usuarios.cvs.pdf', [$usuario, $cv]) }}" target="_blank" rel="noopener"
                class="preview-panel-btn popup" aria-label="{{ trans('fields.usuarios_cvs.preview.abrir_pestana') }}"
                data-popup="{{ trans('fields.usuarios_cvs.preview.abrir_pestana') }}">
                <i class="fa-solid fa-up-right-from-square" aria-hidden="true"></i>
            </a>
            {{-- Botón de minimizar/restaurar (estilo ventana): colapsa el panel hasta dejar solo la cabecera --}}
            <button type="button" class="preview-panel-btn popup" data-preview-minimize
                data-label-minimizar="{{ trans('fields.usuarios_cvs.preview.minimizar') }}"
                data-label-restaurar="{{ trans('fields.usuarios_cvs.preview.restaurar') }}"
                aria-label="{{ trans('fields.usuarios_cvs.preview.minimizar') }}"
                data-popup="{{ trans('fields.usuarios_cvs.preview.minimizar') }}">
                <i class="fa-solid fa-minus" data-preview-minimize-icon aria-hidden="true"></i>
            </button>
        </span>
    </header>

    <div class="preview-panel-body">
        <iframe data-src="{{ route('panel.usuarios.cvs.pdf', [$usuario, $cv]) }}"
            title="{{ trans('fields.usuarios_cvs.preview.titulo') }}" loading="lazy"></iframe>
    </div>

    <span class="preview-panel-resizer" data-preview-resize
        aria-label="{{ trans('fields.usuarios_cvs.preview.redimensionar') }}"></span>
</section>
