@extends('public.layouts.app')

@section('content')
    @include('errors.partials.content', [
        'titulo' => trans('public.errores.401.titulo'),
        'descripcion' => trans('public.errores.401.descripcion'),
    ])
@endsection
