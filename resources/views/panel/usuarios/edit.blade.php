@extends('panel.layouts.app')

@section('title', trans('fields.usuarios.titulo'))

@section('content')
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3">{{ trans('fields.usuarios.titulo') }} — {{ trans('actions.edit') ?? 'Editar' }}</h1>
    </div>

    {{-- TODO: maquetar formulario de edición (PUT a panel.usuarios.update con ulid del usuario) --}}
    <p class="text-muted">Vista placeholder de edición para {{ $usuario->email }} (ulid: {{ $usuario->ulid }}).</p>
@endsection
