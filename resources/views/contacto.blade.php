@extends('public.layouts.app')

@section('content')
    <div class="my-5">
        <h1 class="text-center mb-4">{{ trans('public.contacto.titulo') }}</h1>
        <div class="d-flex justify-content-center">
            <p class="text-center text-muted" style="max-width: 700px; font-size: 1.1rem;">
                {{ trans('public.contacto.descripcion') }}
            </p>
        </div>
    </div>

    <hr class="featurette-divider">

    <!-- Información de Contacto -->
    <div class="row mb-5">
        <div class="col-lg-8 offset-lg-2">
            <div class="row g-4">
                <!-- Email -->
                <div class="col-md-4 text-center">
                    <div class="card shadow p-4" style="background-color: var(--bs-light);">
                        <i class="fas fa-envelope fa-3x mb-3" style="color: var(--terciary);"></i>
                        <h5 style="color: var(--primary);">{{ trans('public.contacto.email') }}</h5>
                        <a href="mailto:luis.cueto@example.com" class="text-decoration-none" style="color: var(--terciary);">
                            luis.cueto@example.com
                        </a>
                    </div>
                </div>

                <!-- LinkedIn -->
                <div class="col-md-4 text-center">
                    <div class="card shadow p-4" style="background-color: var(--bs-light);">
                        <i class="fab fa-linkedin fa-3x mb-3" style="color: var(--quaternary);"></i>
                        <h5 style="color: var(--primary);">{{ trans('public.contacto.linkedin') }}</h5>
                        <a href="https://linkedin.com/in/luiscueto" target="_blank" rel="noopener" class="text-decoration-none" style="color: var(--quaternary);">
                            linkedin.com/in/luiscueto
                        </a>
                    </div>
                </div>

                <!-- GitHub -->
                <div class="col-md-4 text-center">
                    <div class="card shadow p-4" style="background-color: var(--bs-light);">
                        <i class="fab fa-github fa-3x mb-3" style="color: var(--primary);"></i>
                        <h5 style="color: var(--primary);">{{ trans('public.contacto.github') }}</h5>
                        <a href="https://github.com/luiscueto" target="_blank" rel="noopener" class="text-decoration-none" style="color: var(--primary);">
                            github.com/luiscueto
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="featurette-divider">

    <!-- Formulario de Contacto -->
    <div class="row mb-5">
        <div class="col-lg-6 offset-lg-3">
            <div class="card shadow p-4" style="background-color: var(--bs-light);">
                <h3 class="text-center mb-4" style="color: var(--primary);">{{ trans('public.contacto.formulario_titulo') }}</h3>
                <form>
                    <div class="mb-3">
                        <label for="nombre" class="form-label" style="color: var(--text-color);">{{ trans('public.contacto.nombre') }}</label>
                        <input type="text" class="form-control" id="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label" style="color: var(--text-color);">{{ trans('public.contacto.email') }}</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="mensaje" class="form-label" style="color: var(--text-color);">{{ trans('public.contacto.mensaje') }}</label>
                        <textarea class="form-control" id="mensaje" rows="5" required></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">{{ trans('public.contacto.enviar') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <hr class="featurette-divider">

    <!-- Botón de regreso -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <a href="{{ route('home') }}" class="btn btn-primary">{{ trans('actions.back') }}</a>
        </div>
    </div>
@endsection
