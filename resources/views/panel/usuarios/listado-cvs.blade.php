@extends('panel.layouts.app')

@section('title', trans('fields.usuarios_cvs.titulo'))

@section('breadcrumbs', Breadcrumbs::render('panel.usuarios.cvs', $usuario))

@section('content')
    <div class="card shadow">
        <div class="card-header">
            <h1 class="h5 mb-0">{{ trans('fields.usuarios_cvs.titulo') }} — {{ $usuario->nombre_completo }}</h1>
        </div>

        <div class="card-body">
            {{-- El componente Livewire de gestión de CVs y secciones se añade en la siguiente fase --}}
            <p class="text-muted mb-0">{{ trans('fields.usuarios_cvs.titulo') }}</p>
        </div>
    </div>
@endsection
