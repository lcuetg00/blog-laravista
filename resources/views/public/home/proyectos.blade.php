@extends('public.layouts.app')

@section('content')
    <div class="section-main-hero py-3 mb-3">
        <div class="container">
            <div class="text-center">
                <h1 class="text-center mb-4">
                    {{ trans('public.proyectos.titulo') }}
                    {{-- <i class="fa-solid fa-code mb-3"></i> --}}
                </h1>
                <div class="d-flex justify-content-center">
                    <p class="lead text-muted">
                        {{ trans('public.proyectos.descripcion') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="project-card card shadow-lg border-0 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <!-- Left side - Visual -->
                            <div class="col-md-7">
                                <img src="{{ asset('images/pageExample.png') }}"
                                    alt="{{ trans('public.proyectos.imagen_ejemplo') }}" class="img-fluid w-100 h-100 p-3"
                                    style="object-fit: contain" loading="lazy">
                            </div>

                            <!-- Right side - Content -->
                            <div class="col-md-5 d-flex align-items-center p-5">
                                <div class="w-100">
                                    <h3 class="mb-4 fw-bold text-white">
                                        {{ trans('public.proyectos.visitar_github') }}
                                    </h3>
                                    <p class="mb-4 text-white">
                                        {{ trans('public.proyectos.descripcion_imagen') }}
                                    </p>

                                    {{-- Añadir página de github desde el back --}}
                                    <a href="https://github.com/" target="_blank" rel="noopener noreferrer"
                                        class="github-btn btn btn-primary btn-lg px-5 py-3 w-100">
                                        <i class="fa-brands fa-github me-2"></i>
                                        {{ trans('public.proyectos.ver_github') }}
                                        <i class="fa-solid fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="featurette-divider my-5">

    @include('public.partials.footer_volver')
@endsection
