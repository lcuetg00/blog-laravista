{{-- Cabecera de página (section-main-hero): título h1 + subtítulo --}}
<div class="section-main-hero py-3 mb-3">
    <div class="container">
        <h1 class="text-center mb-4">{{ $bloque->campo('titulo') }}</h1>
        <div class="d-flex justify-content-center">
            <p class="text-center text-muted">{{ $bloque->campo('subtitulo') }}</p>
        </div>
    </div>
</div>
