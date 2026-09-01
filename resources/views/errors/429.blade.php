@extends('public.layouts.app')

@section('content')
    @include('errors.partials.content', [
        'titulo' => trans('public.errores.429.titulo'),
        'descripcion' => trans('public.errores.429.descripcion'),
    ])
@endsection
