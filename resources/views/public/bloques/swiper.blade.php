{{-- Bloque carrusel principal (Swiper): conserva las clases que inicializa app.js. Las imágenes y su alt se gestionan en medialibrary --}}
<div class="carousel-section">
    <div class="swiper swiper-carousel">
        <div class="swiper-wrapper">
            @forelse ($bloque->galeria() as $media)
                <div class="swiper-slide">
                    <img src="{{ $media->getUrl() }}" alt="{{ $bloque->textoMedia($media, 'alt') }}" loading="lazy">
                </div>
            @empty
                {{-- Slides de respaldo (misma imagen repetida) mientras no se han subido imágenes al carrusel --}}
                @for ($i = 0; $i < 4; $i++)
                    <div class="swiper-slide">
                        <img src="{{ asset('images/laravista-smaller.png') }}" alt="{{ config('app.name') }}"
                            loading="lazy">
                    </div>
                @endfor
            @endforelse
        </div>

        {{-- Navegación --}}
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</div>
