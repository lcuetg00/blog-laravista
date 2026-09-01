@extends('public.layouts.app')

@section('content')
    @include('errors.partials.content', [
        'titulo' => trans('public.errores.404.titulo'),
        'descripcion' => trans('public.errores.404.descripcion'),
    ])
@endsection
