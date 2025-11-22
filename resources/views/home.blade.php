@extends('public.layouts.app')

@section('content')
    <div class="my-5">
        <h1 class="text-center mb-3">{{ trans('public.home.title') }}</h1>
        <div class="d-flex justify-content-center">
            <p class="text-center" style="max-width: 600px">{{ trans('public.home.description') }}</p>
        </div>
    </div>

    <hr class="featurette-divider">

    <h2 class="text-center mb-3">{{ trans('public.secciones.posts') }}</h1>

        <div class="d-flex justify-content-center">
            <p class="text-center" style="max-width: 600px">{{ trans('public.secciones.posts_descripcion') }}</p>
        </div>

        <hr class="featurette-divider">

        <div class="row featurette">
            <div class="col-md-7 d-flex flex-column justify-content-center align-items-center">
                <h3 class="featurette-heading text-center ">
                    {{ trans('public.secciones.about') }}
                </h3>
                <p class="lead">
                    {{ trans('public.secciones.about_descripcion') }}
                </p>
            </div>
            <div class="col-md-5 d-flex justify-content-center">
                <img class="image-home shadow-xl image-fade" title="{{ trans('public.images.home_globo') }}"
                    src="{{ asset('images/simon-lee-M-6QQXJ8AG4-unsplash.jpg') }}">
            </div>
        </div>

        <hr class="featurette-divider">
    @endsection
