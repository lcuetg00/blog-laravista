@extends('public.layouts.app')

@section('content')
    @php
        $bloques = $pagina->bloques;
    @endphp

    {{-- Cabecera de créditos --}}
    @include($bloques[0]->tipo->vista(), ['bloque' => $bloques[0]])

    <hr class="featurette-divider">

    {{-- Fuentes tipográficas: título, tabla y apartados de uso/licencia --}}
    <div class="row mb-5">
        <div class="col-lg-10 offset-lg-1">
            @include($bloques[1]->tipo->vista(), ['bloque' => $bloques[1], 'clase' => 'mb-4'])
            @include($bloques[2]->tipo->vista(), ['bloque' => $bloques[2]])

            <div class="mt-4">
                @include($bloques[3]->tipo->vista(), ['bloque' => $bloques[3]])
                @include($bloques[4]->tipo->vista(), ['bloque' => $bloques[4]])
            </div>
        </div>
    </div>

    <hr class="featurette-divider">

    {{-- Imágenes utilizadas: título, tabla y apartado de uso --}}
    <div class="row mb-5">
        <div class="col-lg-10 offset-lg-1">
            @include($bloques[5]->tipo->vista(), ['bloque' => $bloques[5], 'clase' => 'mb-4'])
            @include($bloques[6]->tipo->vista(), ['bloque' => $bloques[6]])

            <div class="mt-4">
                @include($bloques[7]->tipo->vista(), ['bloque' => $bloques[7]])
            </div>
        </div>
    </div>

    @include('public.partials.footer_volver')
@endsection
