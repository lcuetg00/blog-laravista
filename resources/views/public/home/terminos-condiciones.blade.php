@extends('public.layouts.app')

@section('content')
    <div class="section-main-hero py-3 mb-3">
        <div class="container">
            <h1 class="text-center mb-4">{{ trans('public.terminos_condiciones.titulo') }}</h1>
            <div class="d-flex justify-content-center">
                <p class="text-center text-muted" style="max-width: 700px;">
                    {{ trans('public.terminos_condiciones.descripcion') }}
                </p>
            </div>
        </div>
    </div>

    <hr class="featurette-divider">

    <div class="row mb-5">
        <div class="col-lg-10 offset-lg-1">
            <p>{{ trans('public.terminos_condiciones.contenido') }}</p>
        </div>
    </div>

    @include('public.partials.footer_volver')
@endsection
