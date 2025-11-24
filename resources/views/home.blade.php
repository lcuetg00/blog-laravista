@extends('public.layouts.app')

@section('content')
    <div class="my-5">
        <h1 class="text-center mb-3">{{ trans('public.home.title') }}</h1>
        <div class="d-flex justify-content-center">
            <p class="text-center" style="max-width: 600px">{{ trans('public.home.description') }}</p>
        </div>
    </div>

    <!-- Carrusel 3D -->
    <div class="carousel-section">
        <div class="swiper swiper-carousel">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="{{ asset('images/laravista.png') }}" alt="{{ trans('public.carousel.slide_1_alt') }}">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/simonlee1.jpg') }}" alt="{{ trans('public.carousel.slide_2_alt') }}">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/simonlee2.jpg') }}" alt="{{ trans('public.carousel.slide_3_alt') }}">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/david-becker-crs2vlkSe98-unsplash.jpg') }}"
                        alt="{{ trans('public.carousel.slide_4_alt') }}">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/johny-goerend-Oz2ZQ2j8We8-unsplash.jpg') }}"
                        alt="{{ trans('public.carousel.slide_5_alt') }}">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/pawel-czerwinski-6lQDFGOB1iw-unsplash.jpg') }}"
                        alt="{{ trans('public.carousel.slide_6_alt') }}">
                </div>
            </div>

            <!-- Navegación -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>

            <!-- Paginación -->
            <div class="swiper-pagination"></div>
        </div>
    </div>

    <hr class="featurette-divider">

    <h2 class="text-center mb-3">{{ trans('public.secciones.posts') }}</h2>

    <div class="d-flex justify-content-center">
        <p class="text-center" style="max-width: 600px">{{ trans('public.secciones.posts_descripcion') }}</p>
    </div>

    <hr class="featurette-divider">

    <div class="row featurette">
        <div class="col-md-7 d-flex flex-column justify-content-center align-items-center">
            <h2 class="featurette-heading text-center">
                {{ trans('public.secciones.about') }}
            </h2>
            <p>
                {{ trans('public.secciones.about_descripcion') }}
            </p>
        </div>
        <div class="col-md-5 d-flex justify-content-center">
            <img class="image-home shadow-xl image-fade hover-lift" title="{{ trans('public.images.home_globo') }}"
                src="{{ asset('images/simon-lee-M-6QQXJ8AG4-unsplash.jpg') }}">
        </div>
    </div>

    <hr class="featurette-divider">
@endsection
