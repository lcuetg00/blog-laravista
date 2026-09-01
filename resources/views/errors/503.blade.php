@extends('public.layouts.app')

@section('content')
    @include('errors.partials.content', [
        'titulo' => trans('public.errores.503.titulo'),
        'descripcion' => trans('public.errores.503.descripcion'),
    ])
@endsection
