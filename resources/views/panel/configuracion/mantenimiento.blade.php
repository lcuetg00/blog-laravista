@extends('panel.layouts.app')

@use('Mcamara\LaravelLocalization\Facades\LaravelLocalization')

@section('title', trans('configuracion.menu.mantenimiento'))

@section('breadcrumbs', Breadcrumbs::render('panel.configuracion.mantenimiento'))

@section('content')
    <div class="row g-3">

        {{-- Card: Caché --}}
        <div class="col-12 col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa-solid fa-gauge-high text-primary" aria-hidden="true"></i>
                    <h2 class="h5 mb-0">{{ trans('configuracion.cache.titulo') }}</h2>
                </div>

                <div class="card-body">
                    <p class="mb-3">{{ trans('configuracion.mantenimiento.cache_descripcion') }}</p>

                    <ul class="list-group list-group-flush">
                        @foreach (['config', 'rutas', 'eventos'] as $estado)
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                <span class="fw-bold">{{ trans('configuracion.cache.' . $estado) }}</span>
                                <span class="badge {{ $cacheInfo[$estado] ? 'bg-success' : 'bg-secondary' }} ms-2">
                                    {{ $cacheInfo[$estado] ? trans('configuracion.cache.cacheado') : trans('configuracion.cache.no_cacheado') }}
                                </span>
                            </li>
                        @endforeach
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="fw-bold">{{ trans('configuracion.cache.vistas') }}</span>
                            <span class="badge bg-secondary ms-2">{{ $cacheInfo['vistas'] }}</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="fw-bold">{{ trans('configuracion.cache.driver_cache') }}</span>
                            <span class="badge bg-secondary ms-2">{{ $cacheInfo['driver_cache'] }}</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="fw-bold">{{ trans('configuracion.cache.driver_cola') }}</span>
                            <span class="badge bg-secondary ms-2">{{ $cacheInfo['driver_cola'] }}</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="fw-bold">{{ trans('configuracion.cache.jobs_fallidos') }}</span>
                            <span
                                class="badge {{ $cacheInfo['jobs_fallidos'] > 0 ? 'bg-danger' : 'bg-secondary' }} ms-2">{{ $cacheInfo['jobs_fallidos'] }}</span>
                        </li>
                    </ul>
                </div>

                <div class="card-footer d-flex flex-wrap justify-content-end">
                    <button type="button" class="btn btn-secondary m-1" data-bs-toggle="modal"
                        data-bs-target="#confirmModal-vistas-limpiar">
                        <i class="fa-solid fa-eraser me-1" aria-hidden="true"></i>
                        {{ trans('configuracion.mantenimiento.limpiar_vistas') }}
                    </button>

                    <button type="button" class="btn btn-warning m-1" data-bs-toggle="modal"
                        data-bs-target="#confirmModal-cache">
                        <i class="fa-solid fa-broom me-1" aria-hidden="true"></i>
                        {{ trans('configuracion.mantenimiento.limpiar_cache') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Card: Mantenimiento --}}
        <div class="col-12 col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa-solid fa-screwdriver-wrench" aria-hidden="true"></i>
                    <h2 class="h5 mb-0">{{ trans('configuracion.mantenimiento.titulo') }}</h2>
                </div>

                @if ($mantenimientoActivo)
                    <div class="card-body">
                        <span class="badge bg-danger">{{ trans('configuracion.mantenimiento.modo_activo') }}</span>
                        <p class="mt-3 mb-0">{{ trans('configuracion.mantenimiento.activo_ayuda') }}</p>
                    </div>

                    <div class="card-footer d-flex justify-content-end">
                        <form method="POST" action="{{ route('panel.configuracion.modo') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fa-solid fa-power-off me-1" aria-hidden="true"></i>
                                {{ trans('configuracion.mantenimiento.desactivar') }}
                            </button>
                        </form>
                    </div>
                @else
                    <form method="POST" action="{{ route('panel.configuracion.modo') }}"
                        class="m-0 d-flex flex-column h-100" x-data="{
                            {{-- Añadimos varables para cada parte --}}
                            secreto: @js(old('secreto', 'pagina-en-mantenimiento')),
                                urlBase: @js(rtrim(config('app.url'), '/') . '/' . LaravelLocalization::getCurrentLocale()),
                                textoAyuda: @js(trans('configuracion.mantenimiento.secreto_help')),
                        }">
                        @csrf

                        <div class="card-body">
                            <span class="badge bg-success">{{ trans('configuracion.mantenimiento.modo_inactivo') }}</span>

                            <div class="mt-3">
                                <label for="secreto"
                                    class="form-label">{{ trans('configuracion.mantenimiento.secreto_label') }}</label>

                                {{-- x-model es para que cuando se modifique el input, se actualicen las variables del x-data --}}
                                <input type="text" id="secreto" name="secreto" x-model="secreto" maxlength="50"
                                    class="form-control @error('secreto') is-invalid @enderror"
                                    aria-describedby="secreto-help @error('secreto') secreto-error @enderror" required>

                                {{-- x-text es para mostrar la expresión como texto, reemplazamos la url y le ponemos la base + lo que ha cambiado del secreto --}}
                                <div id="secreto-help" class="form-text mt-4"
                                    x-text="textoAyuda.replace(':url', urlBase + '/' + secreto)"></div>
                                @error('secreto')
                                    <div id="secreto-error" class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" class="btn btn-danger">
                                <i class="fa-solid fa-power-off me-1" aria-hidden="true"></i>
                                {{ trans('configuracion.mantenimiento.activar') }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

    </div>

    {{-- Card: Salud del sistema --}}
    <div class="card shadow mt-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fa-solid fa-heart-pulse text-danger" aria-hidden="true"></i>
            <h2 class="h5 mb-0">{{ trans('configuracion.salud.titulo') }}</h2>
        </div>

        <div class="card-body">
            <ul class="list-group list-group-flush">
                @foreach ($salud as $clave => $ok)
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                        <span class="fw-bold">{{ trans('configuracion.salud.' . $clave) }}</span>
                        <span class="badge {{ $ok ? 'bg-success' : 'bg-danger' }} ms-2">
                            <i class="fa-solid {{ $ok ? 'fa-check' : 'fa-xmark' }} me-1" aria-hidden="true"></i>
                            {{ $ok ? trans('configuracion.salud.ok') : trans('configuracion.salud.error') }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Modales de confirmación de las acciones de caché y vistas --}}
    <x-confirm-modal id="vistas-limpiar" :action="route('panel.configuracion.vistas.limpiar')" method="POST" :title="trans('configuracion.mantenimiento.limpiar_vistas')" :description="trans('configuracion.mantenimiento.limpiar_vistas_confirm')"
        :accept-label="trans('configuracion.mantenimiento.limpiar_vistas')" accept-class="btn-secondary" />

    <x-confirm-modal id="cache" :action="route('panel.configuracion.cache')" method="POST" :title="trans('configuracion.mantenimiento.limpiar_cache')" :description="trans('configuracion.mantenimiento.limpiar_cache_confirm')" :accept-label="trans('configuracion.mantenimiento.limpiar_cache')"
        accept-class="btn-warning" />
@endsection
