@extends('public.layouts.app')

@section('content')
    <div class="section-main-hero py-3 mb-3">
        <div class="container">
            <h1 class="text-center mb-4">{{ trans('public.politica_privacidad.titulo') }}</h1>
            <div class="d-flex justify-content-center">
                <p class="text-center text-muted" style="max-width: 700px;">
                    {{ trans('public.politica_privacidad.descripcion') }}
                </p>
            </div>
        </div>
    </div>

    <hr class="featurette-divider">

    <div class="row mb-5">
        <div class="col-lg-10 offset-lg-1">
            <p>{{ trans('public.politica_privacidad.contenido') }}</p>
        </div>
    </div>

    @include('public.partials.footer_volver')
@endsection
