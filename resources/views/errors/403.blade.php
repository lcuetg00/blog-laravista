@extends('public.layouts.app')

@section('content')
    @include('errors.partials.content', [
        'titulo' => trans('public.errores.403.titulo'),
        'descripcion' => trans('public.errores.403.descripcion'),
    ])
@endsection
