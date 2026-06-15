@extends('public.layouts.app')

@section('content')
    @php
        $bloques = $pagina->bloques;
    @endphp

    {{-- Cabecera con título y descripción --}}
    @include($bloques[0]->tipo->vista(), ['bloque' => $bloques[0]])

    {{-- Carrusel principal (Swiper) --}}
    @include($bloques[1]->tipo->vista(), ['bloque' => $bloques[1]])

    <hr class="featurette-divider">

    {{-- Título de la sección de posts --}}
    @include($bloques[2]->tipo->vista(), ['bloque' => $bloques[2]])

    <hr class="featurette-divider">

    {{-- Featurette «Sobre mí» --}}
    @include($bloques[3]->tipo->vista(), ['bloque' => $bloques[3]])

    <hr class="featurette-divider">
@endsection
