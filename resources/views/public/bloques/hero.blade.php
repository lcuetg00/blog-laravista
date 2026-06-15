{{--
    Bloque hero: se renderiza en dos variantes según los datos del bloque.
      - con botón e icono        → card de contacto (icono circular, columna col-lg-6).
      - con botón sin icono      → card de proyecto (oscura, imagen a la izquierda, columna col-lg-10).
--}}
@php
    $titulo = $bloque->campo('titulo');
    $descripcion = $bloque->campo('descripcion');
    $textoBoton = $bloque->campo('texto_boton');
    $urlBoton = $bloque->campo('url_boton');

    // Imagen de respaldo cuando el bloque aún no tiene su media subida
    $placeholder = asset('images/laravista-smaller.png');
@endphp

@if ($bloque->iconoUrl())
    {{-- Card de contacto: icono circular sobre fondo, con enlace de contacto --}}
    <div class="col-lg-6 col-md-6">
        <div class="contact-card card shadow-lg border-0 h-100">
            <div class="card-body d-flex flex-column align-items-center text-center p-5">
                <div class="contact-icon-wrapper mb-4 rounded-circle d-flex align-items-center justify-content-center">
                    <img src="{{ $bloque->iconoUrl() ?: $placeholder }}" alt="" class="img-fluid" loading="lazy">
                </div>
                <h3 class="mb-3 fw-bold text-white">{{ $titulo }}</h3>
                <p class="text-white mb-4">{{ $descripcion }}</p>
                <a href="{{ $urlBoton ?: '#' }}" target="_blank" rel="noopener noreferrer"
                    class="contact-link btn btn-outline-primary btn-lg px-4 mt-auto w-100 text-white">
                    {{ $textoBoton }}
                </a>
            </div>
        </div>
    </div>
@else
    {{-- Card de proyecto: imagen a la izquierda y contenido con botón a la derecha --}}
    <div class="col-lg-10">
        <div class="project-card card shadow-lg border-0 overflow-hidden">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-md-7 position-relative">
                        <img src="{{ $bloque->imagenUrl() ?: $placeholder }}" alt="{{ $titulo }}"
                            class="img-fluid hero-project-image p-3" loading="lazy">
                    </div>
                    <div class="col-md-5 d-flex align-items-center p-5">
                        <div class="w-100">
                            <h3 class="mb-4 fw-bold text-white">{{ $titulo }}</h3>
                            <p class="mb-4 text-white">{{ $descripcion }}</p>
                            <a href="{{ $urlBoton ?: '#' }}" target="_blank" rel="noopener noreferrer"
                                class="github-btn btn btn-primary btn-lg px-5 py-3 w-100">
                                {{ $textoBoton }}
                                <i class="fa-solid fa-arrow-right ms-2" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
