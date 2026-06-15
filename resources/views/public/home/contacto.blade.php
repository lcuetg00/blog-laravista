@extends('public.layouts.app')

@section('content')
    @php
        $bloques = $pagina->bloques;
    @endphp

    {{-- Cabecera de contacto --}}
    @include($bloques[0]->tipo->vista(), ['bloque' => $bloques[0]])

    {{-- Cards de contacto (LinkedIn, GitHub, ...) --}}
    <div class="container mb-5">
        <div class="row g-4 justify-content-center">
            @foreach ($bloques->skip(1) as $bloque)
                @include($bloque->tipo->vista(), ['bloque' => $bloque])
            @endforeach
        </div>
    </div>

    <hr class="featurette-divider my-5">

    @include('public.partials.footer_volver')
@endsection
