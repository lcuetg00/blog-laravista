@extends('public.layouts.app')

@section('content')
    <div class="section-main-hero py-3 mb-3">
        <div class="container">
            <div class="text-center">
                <h1 class="text-center mb-4">
                    {{ trans('public.contacto.titulo') }}
                    {{-- <i class="fa-solid fa-paper-plane mb-3"></i> --}}
                </h1>
                <div class="d-flex justify-content-center">
                    <p class="lead text-muted">
                        {{ trans('public.contacto.descripcion') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Cards -->
    <div class="container mb-5">
        <div class="row g-4 justify-content-center">

            <!-- LinkedIn Card -->
            <div class="col-lg-6 col-md-6">
                <div class="contact-card card shadow-lg border-0 h-100">
                    <div class="card-body d-flex flex-column align-items-center text-center p-5">
                        <div
                            class="contact-icon-wrapper mb-4 rounded-circle d-flex align-items-center justify-content-center">
                            <i class="fa-brands fa-linkedin-in fa-3x text-white"></i>
                        </div>
                        <h3 class="mb-3 fw-bold text-white">{{ trans('public.contacto.linkedin') }}</h3>
                        <p class="text-white mb-4">
                            {{ trans('public.contacto.contacta') }}
                        </p>
                        {{-- Cargar el linkedin/github que esté en configuración --}}
                        <a href="https://linkedin.com/" target="_blank" rel="noopener noreferrer"
                            class="contact-link btn btn-outline-primary btn-lg px-4 mt-auto w-100 text-white">
                            <i class="fa-brands fa-linkedin me-2"></i>
                            linkedin.com/in/
                        </a>
                    </div>
                </div>
            </div>

            <!-- GitHub Card -->
            <div class="col-lg-6 col-md-6">
                <div class="contact-card card shadow-lg border-0 h-100">
                    <div class="card-body d-flex flex-column align-items-center text-center p-5">
                        <div
                            class="contact-icon-wrapper mb-4 rounded-circle d-flex align-items-center justify-content-center">
                            <i class="fa-brands fa-github fa-3x text-white"></i>
                        </div>
                        <h3 class="mb-3 fw-bold text-white">{{ trans('public.contacto.github') }}</h3>
                        <p class="mb-4 text-white">
                            {{ trans('public.contacto.github_descripcion') }}
                        </p>
                        {{-- Cargar el linkedin/github que esté en configuración --}}
                        <a href="https://github.com/" target="_blank" rel="noopener noreferrer"
                            class="contact-link btn btn-outline-primary btn-lg px-4 mt-auto w-100 text-white">
                            <i class="fa-brands fa-github me-2"></i>
                            github.com/
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="featurette-divider my-5">

    @include('public.partials.footer_volver')
@endsection
