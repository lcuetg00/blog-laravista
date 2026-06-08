@extends('panel.layouts.app')

@section('title', trans('fields.usuarios.detalle'))

@section('breadcrumbs', Breadcrumbs::render('panel.usuarios.show', $usuario))

@section('content')
    <div class="card shadow">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h5 mb-0">{{ trans('fields.usuarios.detalle') }}</h2>
            @can(\App\Helpers\PermissionHelper::USUARIOS_EDITAR_PERMISSION)
                <a href="{{ route('panel.usuarios.edit', $usuario) }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-pencil me-1" aria-hidden="true"></i>
                    {{ trans('actions.edit') }}
                </a>
            @endcan
        </div>

        <div class="card-body">
            <div class="row g-3 mb-0">
                <div class="col-md-4">
                    <div class="fw-bold">{{ trans('fields.input.nombre') }}</div>
                    <div>{{ $usuario->nombre }}</div>
                </div>

                <div class="col-md-4">
                    <div class="fw-bold">{{ trans('fields.input.primer_apellido') }}</div>
                    <div>{{ $usuario->primer_apellido }}</div>
                </div>

                <div class="col-md-4">
                    <div class="fw-bold">{{ trans('fields.input.segundo_apellido') }}</div>
                    <div>{{ $usuario->segundo_apellido }}</div>
                </div>

                <div class="col-md-4">
                    <div class="fw-bold">{{ trans('fields.input.email') }}</div>
                    <div>{{ $usuario->email }}</div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('panel.usuarios.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i>
                {{ trans('actions.back') }}
            </a>
        </div>
    </div>
@endsection
