@extends('public.layouts.app')

@section('content')
    @include('errors.partials.content', [
        'titulo' => trans('public.errores.500.titulo'),
        'descripcion' => trans('public.errores.500.descripcion'),
    ])
@endsection
