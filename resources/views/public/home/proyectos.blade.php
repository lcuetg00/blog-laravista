@extends('public.layouts.app')

@section('content')
    @php
        $bloques = $pagina->bloques;
    @endphp

    {{-- Cabecera de proyectos --}}
    @include($bloques[0]->tipo->vista(), ['bloque' => $bloques[0]])

    {{-- Card del proyecto destacado --}}
    <div class="container mb-5">
        <div class="row justify-content-center">
            @include($bloques[1]->tipo->vista(), ['bloque' => $bloques[1]])
        </div>
    </div>

    <hr class="featurette-divider my-5">

    @include('public.partials.footer_volver')
@endsection
