{{-- Bloque botón: enlace de acción suelto --}}
<div class="text-center">
    <a href="{{ $bloque->campo('url') ?: '#' }}" target="_blank" rel="noopener noreferrer"
        class="btn btn-primary btn-lg px-5">
        {{ $bloque->campo('texto') }}
    </a>
</div>
