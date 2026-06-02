@extends('panel.layouts.app')

@section('title', trans('fields.usuarios.titulo'))

@section('breadcrumbs', Breadcrumbs::render('panel.usuarios.create'))

@section('content')
    <form method="POST" action="{{ route('panel.usuarios.store') }}">
        @csrf

        <div class="card shadow">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <x-input name="nombre" :label="trans('fields.input.nombre')" maxlength="70" required />
                    </div>

                    <div class="col-12 col-md-4">
                        <x-input name="primer_apellido" :label="trans('fields.input.primer_apellido')" maxlength="70" required />
                    </div>

                    <div class="col-12 col-md-4">
                        <x-input name="segundo_apellido" :label="trans('fields.input.segundo_apellido')" maxlength="70" />
                    </div>

                    <div class="col-12 col-md-4">
                        <x-input name="email" type="email" :label="trans('fields.input.email')" maxlength="255" required />
                    </div>

                    <div class="col-12 col-md-4">
                        <x-password-input name="password" :label="trans('fields.input.password')" required rules />
                    </div>

                    <div class="col-12 col-md-4">
                        <x-password-input name="password_confirmation" :label="trans('fields.input.password_confirmation')" required />
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
