@extends('panel.layouts.app')

@section('title', trans('fields.usuarios.titulo'))

@section('content')
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3">{{ trans('fields.usuarios.titulo') }} — {{ trans('actions.create') ?? 'Crear' }}</h1>
    </div>

    {{-- TODO: maquetar formulario de creación (POST a panel.usuarios.store) --}}
    <p class="text-muted">Vista placeholder del formulario de creación.</p>
@endsection
