{{-- Bloque título + subtítulo con imagen a la derecha (estilo featurette); si tiene icono se muestra encima del título --}}
@php
    $titulo = $bloque->campo('titulo');
    $subtitulo = $bloque->campo('subtitulo');

    // Imagen de respaldo cuando el bloque aún no tiene su media subida
    $placeholder = asset('images/laravista-smaller.png');
@endphp

<div class="row featurette">
    <div class="col-md-7 d-flex flex-column justify-content-center align-items-center">
        @if ($bloque->iconoUrl())
            <img src="{{ $bloque->iconoUrl() }}" alt="" class="hero-featurette-icono mb-3" loading="lazy">
        @endif
        <h2 class="featurette-heading text-center">{{ $titulo }}</h2>
        <p>{{ $subtitulo }}</p>
    </div>
    <div class="col-md-5 d-flex justify-content-center">
        <img class="image-home shadow-xl image-fade hover-lift" src="{{ $bloque->imagenUrl() ?: $placeholder }}"
            alt="{{ $titulo }}" loading="lazy">
    </div>
</div>
