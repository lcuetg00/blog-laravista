{{-- Bloque carrusel giratorio (tecnologías): pista animada con sus elementos imagen+etiqueta. Los items se duplican para el loop infinito --}}
@php
    $direccion = $bloque->campo('direccion', 'normal');
    $items = $bloque->itemsCarrusel();
@endphp

<div class="tech-carousel-container">
    <div class="tech-carousel-track @if ($direccion === 'inverso') tech-carousel-reverse @endif">
        {{-- Se recorre dos veces la misma lista de items para conseguir el desplazamiento infinito --}}
        @for ($vuelta = 0; $vuelta < 2; $vuelta++)
            @forelse ($items as $media)
                <div class="tech-item">
                    <div class="text-center">
                        <img src="{{ $media->getUrl() }}" alt="{{ $bloque->textoMedia($media, 'etiqueta') }}"
                            class="mb-3" style="height: 4rem; object-fit: contain;" loading="lazy">
                        <h5>{{ $bloque->textoMedia($media, 'etiqueta') }}</h5>
                    </div>
                </div>
            @empty
                {{-- Elementos de respaldo (misma imagen repetida) mientras no se han subido elementos al carrusel --}}
                @for ($i = 0; $i < 6; $i++)
                    <div class="tech-item">
                        <div class="text-center">
                            <img src="{{ asset('images/laravista-smaller.png') }}" alt="{{ config('app.name') }}"
                                class="mb-3" style="height: 4rem; object-fit: contain;" loading="lazy">
                            <h5>{{ config('app.name') }}</h5>
                        </div>
                    </div>
                @endfor
            @endforelse
        @endfor
    </div>
</div>
