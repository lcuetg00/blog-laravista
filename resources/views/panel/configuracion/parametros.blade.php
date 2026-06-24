@extends('panel.layouts.app')

@section('title', trans('configuracion.menu.parametros'))

@section('breadcrumbs', Breadcrumbs::render('panel.configuracion.parametros'))

@section('content')
    <div class="card shadow">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fa-solid fa-sliders" aria-hidden="true"></i>
            <h2 class="h5 mb-0">{{ trans('configuracion.ajustes.titulo') }}</h2>
        </div>

        <form method="POST" action="{{ route('panel.configuracion.parametros.update') }}">
            @csrf
            @method('PUT')

            <div class="card-body">
                <fieldset class="mb-4">
                    <legend class="h6 fw-bold border-bottom pb-2">{{ trans('configuracion.ajustes.grupos.sitio') }}</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-input name="sitio_nombre" :label="trans('configuracion.ajustes.campos.sitio_nombre')"
                                :value="$valoresAjustes['sitio_nombre'] ?? null" maxlength="255" />
                        </div>
                    </div>
                </fieldset>

                <fieldset class="mb-4">
                    <legend class="h6 fw-bold border-bottom pb-2">{{ trans('configuracion.ajustes.grupos.contacto') }}</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-input name="email_contacto" type="email" :label="trans('configuracion.ajustes.campos.email_contacto')"
                                :value="$valoresAjustes['email_contacto'] ?? null" maxlength="255" />
                        </div>
                        <div class="col-md-6">
                            <x-input name="telefono_contacto" :label="trans('configuracion.ajustes.campos.telefono_contacto')"
                                :value="$valoresAjustes['telefono_contacto'] ?? null" maxlength="255" />
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="h6 fw-bold border-bottom pb-2">{{ trans('configuracion.ajustes.grupos.redes') }}</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-input name="red_github" type="url" icon="fa-brands fa-github"
                                :label="trans('configuracion.ajustes.campos.red_github')"
                                :value="$valoresAjustes['red_github'] ?? null" maxlength="255" />
                        </div>
                        <div class="col-md-6">
                            <x-input name="red_linkedin" type="url" icon="fa-brands fa-linkedin"
                                :label="trans('configuracion.ajustes.campos.red_linkedin')"
                                :value="$valoresAjustes['red_linkedin'] ?? null" maxlength="255" />
                        </div>
                        <div class="col-md-6">
                            <x-input name="red_x" type="url" icon="fa-brands fa-x-twitter"
                                :label="trans('configuracion.ajustes.campos.red_x')"
                                :value="$valoresAjustes['red_x'] ?? null" maxlength="255" />
                        </div>
                        <div class="col-md-6">
                            <x-input name="red_instagram" type="url" icon="fa-brands fa-instagram"
                                :label="trans('configuracion.ajustes.campos.red_instagram')"
                                :value="$valoresAjustes['red_instagram'] ?? null" maxlength="255" />
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk me-1" aria-hidden="true"></i>
                    {{ trans('configuracion.ajustes.guardar') }}
                </button>
            </div>
        </form>
    </div>
@endsection
