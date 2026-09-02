@extends('panel.layouts.app')

@section('title', trans('fields.usuarios_cvs.titulo'))

@section('breadcrumbs', Breadcrumbs::render('panel.usuarios.cvs', $usuario))

@section('content')
    <h1 class="h5 mb-3">{{ trans('fields.usuarios_cvs.titulo') }} — {{ $usuario->nombre_completo }}</h1>

    <livewire:usuario-cv-listado-livewire :usuario="$usuario" />
@endsection
