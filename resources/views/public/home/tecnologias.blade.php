@extends('public.layouts.app')

@section('content')
    @php
        $bloques = $pagina->bloques;
    @endphp

    {{-- Cabecera de tecnologías --}}
    @include($bloques[0]->tipo->vista(), ['bloque' => $bloques[0]])

    <hr class="featurette-divider">

    {{-- Carrusel de tecnologías frontend --}}
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">{{ trans('public.tecnologias.frontend') }}</h2>
            @include($bloques[1]->tipo->vista(), ['bloque' => $bloques[1]])
        </div>
    </div>

    <hr class="featurette-divider">

    {{-- Carrusel de herramientas --}}
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">{{ trans('public.tecnologias.herramientas') }}</h2>
            @include($bloques[2]->tipo->vista(), ['bloque' => $bloques[2]])
        </div>
    </div>

    <hr class="featurette-divider">

    @include('public.partials.footer_volver')
@endsection
