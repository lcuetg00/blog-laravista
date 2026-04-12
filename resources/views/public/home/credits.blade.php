@extends('public.layouts.app')

@section('content')
    <div class="section-main-hero py-3 mb-3">
        <div class="container">
            <h1 class="text-center mb-4" style="font-size: 3rem;">{{ trans('public.creditos.titulo') }}</h1>
            <div class="d-flex justify-content-center">
                <p class="text-center text-muted" style="max-width: 700px; font-size: 1.1rem;">
                    {{ trans('public.creditos.descripcion') }}
                </p>
            </div>
        </div>
    </div>

    <hr class="featurette-divider">

    <!-- Tabla de Fuentes -->
    <div class="row mb-5">
        <div class="col-lg-10 offset-lg-1">
            <h2 class="mb-4">{{ trans('public.creditos.fuentes.titulo') }}</h2>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th class="tabla-encabezado">{{ trans('public.creditos.fuentes.fuente') }}</th>
                            <th class="tabla-encabezado">{{ trans('public.creditos.fuentes.autor') }}</th>
                            <th class="tabla-encabezado">{{ trans('public.creditos.fuentes.licencia') }}</th>
                            <th class="tabla-encabezado">{{ trans('public.creditos.fuentes.enlace') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong style="font-family: 'Bebas Neue', sans-serif; font-size: 1.2rem;">Bebas
                                    Neue</strong>
                            </td>
                            <td>Ryoichi Tsunekawa</td>
                            <td>SIL OFL 1.1</td>
                            <td>
                                <a href="https://fonts.google.com/specimen/Bebas+Neue" target="_blank"
                                    rel="noopener noreferrer">
                                    Google Fonts
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong
                                    style="font-family: 'Montserrat', sans-serif; font-size: 1.2rem;">Montserrat</strong>
                            </td>
                            <td>Julieta Ulanovsky, Sol Matas, Juan Pablo del Peral, Jacques Le Bailly </td>
                            <td>SIL OFL 1.1</td>
                            <td>
                                <a href="https://fonts.google.com/specimen/Montserrat" target="_blank"
                                    rel="noopener noreferrer">Google
                                    Fonts</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <h2 class="mb-3">{{ trans('public.creditos.fuentes_uso') }}</h2>
                <ul style="font-size: 1.05rem; line-height: 1.8;">
                    <li><strong>Bebas Neue:</strong> {{ trans('public.creditos.fuentes_uso_descripcion_bebas') }}</li>
                    <li><strong>Montserrat:</strong> {{ trans('public.creditos.fuentes_uso_descripcion_montserrat') }}</li>
                </ul>

                <p>
                    {{ trans('public.creditos.fuentes_licencia_sil_descripcion') }}
                    </ul>
            </div>
        </div>
    </div>

    <hr class="featurette-divider">

    <!-- Tabla de Imágenes -->

    <div class="row mb-5">
        <div class="col-lg-8 offset-lg-1">
            <h2 class="mb-4">{{ trans('public.creditos.imagenes.titulo') }}</h2>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th class="tabla-encabezado">{{ trans('public.creditos.imagenes.autor') }}</th>
                            <th class="tabla-encabezado">{{ trans('public.creditos.imagenes.licencia') }}</th>
                            <th class="tabla-encabezado">{{ trans('public.creditos.imagenes.enlace') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Simon Lee</td>
                            <td>Unplash</td>
                            <td>
                                <a href="https://unsplash.com/es/@simonppt" target="_blank" rel="noopener noreferrer">
                                    {{ trans('public.creditos.imagenes.pagina_enlace') }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>Johny Goerend</td>
                            <td>Unplash</td>
                            <td>
                                <a href="https://unsplash.com/es/@johnygoerend" target="_blank" rel="noopener noreferrer">
                                    {{ trans('public.creditos.imagenes.pagina_enlace') }}
                                </a>
                            </td>
                        </tr>
                        </tr>
                        <tr>
                            <td>David Becker</td>
                            <td>Unplash</td>
                            <td>
                                <a href="https://unsplash.com/es/@beckerworks" target="_blank" rel="noopener noreferrer">
                                    {{ trans('public.creditos.imagenes.pagina_enlace') }}
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Licencia -->
    <div class="row mb-5">
        <div class="col-lg-10 offset-lg-1">
            <h2 class="mb-3">{{ trans('public.creditos.imagenes_uso') }}</h2>
            <p>
                {{ trans('public.creditos.imagenes_licencia') }}
            </p>
        </div>
    </div>

    @include('public.partials.footer_volver')
@endsection
