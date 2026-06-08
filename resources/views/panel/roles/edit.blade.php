@extends('panel.layouts.app')

@section('title', trans('fields.roles.titulo'))

@section('breadcrumbs', Breadcrumbs::render('panel.roles.edit', $rol))

@section('content')
    <form method="POST" action="{{ route('panel.roles.update', $rol) }}">
        @csrf
        @method('PUT')

        <div class="card shadow">
            <div class="card-header">
                <h2 class="h5 mb-0">{{ trans('fields.roles.datos') }}</h2>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <x-input name="name" :label="trans('fields.input.nombre_rol')" :value="$rol->name" maxlength="125" required />
                    </div>

                    <div class="col-12 col-md-6">
                        <x-input name="descripcion" :label="trans('fields.input.descripcion')" :value="$rol->descripcion" maxlength="255" />
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk me-1" aria-hidden="true"></i>
                    {{ trans('actions.save') }}
                </button>
            </div>
        </div>
    </form>
@endsection
