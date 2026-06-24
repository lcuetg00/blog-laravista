@extends('panel.layouts.app')

@section('title', trans('configuracion.menu.informacion'))

@section('breadcrumbs', Breadcrumbs::render('panel.configuracion.informacion'))

@section('content')
    <div class="row g-3">

        {{-- Card: Laravel y dependencias --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card shadow h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa-brands fa-laravel text-danger" aria-hidden="true"></i>
                    <h2 class="h5 mb-0">{{ trans('configuracion.laravel.titulo') }}</h2>
                </div>

                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="fw-bold">{{ trans('configuracion.laravel.version') }}</span>
                        <span class="badge bg-primary">{{ $laravel['version'] }}</span>
                    </div>

                    <div class="fw-bold mb-2">{{ trans('configuracion.laravel.dependencias') }}</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($laravel['paquetes'] as $paquete => $version)
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                <span class="text-break">{{ $paquete }}</span>
                                <span class="badge bg-secondary ms-2">{{ $version }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- Card: PHP y configuración --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card shadow h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa-brands fa-php text-info" aria-hidden="true"></i>
                    <h2 class="h5 mb-0">{{ trans('configuracion.php.titulo') }}</h2>
                </div>

                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="fw-bold">{{ trans('configuracion.php.version') }}</span>
                        <span class="badge bg-primary">{{ $php['version'] }}</span>
                    </div>

                    <ul class="list-group list-group-flush">
                        @foreach ($php['directivas'] as $directiva => $valor)
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                <span class="text-break">{{ trans('configuracion.php.directivas.' . $directiva) }}</span>
                                <span class="badge bg-secondary ms-2">{{ $valor }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- Card: Base de datos --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card shadow h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa-solid fa-database text-success" aria-hidden="true"></i>
                    <h2 class="h5 mb-0">{{ trans('configuracion.bd.titulo') }}</h2>
                </div>

                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="fw-bold">{{ trans('configuracion.bd.driver') }}</span>
                            <span class="badge bg-secondary ms-2">{{ $baseDatos['driver'] }}</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="fw-bold">{{ trans('configuracion.bd.version') }}</span>
                            <span class="badge bg-secondary ms-2">{{ $baseDatos['version'] }}</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="fw-bold">{{ trans('configuracion.bd.nombre') }}</span>
                            <span class="text-break ms-2">{{ $baseDatos['nombre'] }}</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="fw-bold">{{ trans('configuracion.bd.host') }}</span>
                            <span class="text-break ms-2">{{ $baseDatos['host'] }}</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="fw-bold">{{ trans('configuracion.bd.version_cliente') }}</span>
                            <span class="badge bg-secondary ms-2">{{ $baseDatos['version_cliente'] }}</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="fw-bold">{{ trans('configuracion.bd.estado_conexion') }}</span>
                            <span class="text-break ms-2">{{ $baseDatos['estado_conexion'] }}</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="fw-bold">{{ trans('configuracion.bd.persistente') }}</span>
                            <span class="text-break ms-2">{{ $baseDatos['persistente'] }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Card: Almacenamiento y medios --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card shadow h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa-solid fa-hard-drive text-warning" aria-hidden="true"></i>
                    <h2 class="h5 mb-0">{{ trans('configuracion.almacenamiento.titulo') }}</h2>
                </div>

                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="fw-bold">{{ trans('configuracion.almacenamiento.disco') }}</span>
                            <span class="badge bg-secondary ms-2">{{ $almacenamiento['disco'] }}</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="fw-bold">{{ trans('configuracion.almacenamiento.espacio_libre') }}</span>
                            <span class="badge bg-secondary ms-2">{{ $almacenamiento['espacio_libre'] }}</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="fw-bold">{{ trans('configuracion.almacenamiento.espacio_total') }}</span>
                            <span class="badge bg-secondary ms-2">{{ $almacenamiento['espacio_total'] }}</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="fw-bold">{{ trans('configuracion.almacenamiento.media_total') }}</span>
                            <span class="badge bg-secondary ms-2">{{ $almacenamiento['media_total'] }}</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="fw-bold">{{ trans('configuracion.almacenamiento.media_peso') }}</span>
                            <span class="badge bg-secondary ms-2">{{ $almacenamiento['media_peso'] }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
@endsection
