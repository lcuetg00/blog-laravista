@extends('panel.layouts.app')

@section('title', trans('fields.roles.detalle'))

@section('breadcrumbs', Breadcrumbs::render('panel.roles.show', $rol))

@section('content')
    <div class="card shadow">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h5 mb-0">{{ trans('fields.roles.detalle') }}</h2>
            @can(\App\Helpers\PermissionHelper::ROLES_EDITAR_PERMISSION)
                <a href="{{ route('panel.roles.edit', $rol) }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-pencil me-1" aria-hidden="true"></i>
                    {{ trans('actions.edit') }}
                </a>
            @endcan
        </div>

        <div class="card-body">
            <div class="row g-3 mb-0">
                <div class="col-md-6">
                    <div class="fw-bold">{{ trans('fields.input.nombre_rol') }}</div>
                    <div>{{ $rol->name }}</div>
                </div>

                <div class="col-md-6">
                    <div class="fw-bold">{{ trans('fields.input.descripcion') }}</div>
                    <div>{{ $rol->descripcion }}</div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('panel.roles.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i>
                {{ trans('actions.back') }}
            </a>
        </div>
    </div>
@endsection
