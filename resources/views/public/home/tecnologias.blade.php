@extends('public.layouts.app')

@section('content')
    <div class="section-main-hero py-3 mb-3">
        <div class="container">
            <h1 class="text-center mb-4">{{ trans('public.tecnologias.titulo') }}</h1>
            <div class="d-flex justify-content-center">
                <p class="text-center text-muted">
                    {{ trans('public.tecnologias.descripcion') }}
                </p>
            </div>
        </div>
    </div>

    <hr class="featurette-divider">

    {{-- TODO: Quedaría bien añadir una ventana con texto sobre las tecnología cuando se pulsa en ellas --}}

    <!-- Frontend -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">{{ trans('public.tecnologias.frontend') }}</h2>
            <div class="tech-carousel-container">
                <div class="tech-carousel-track">
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-html5 fa-4x mb-3" style="color: rgb(255, 50, 50)" aria-hidden="true"></i>
                            <h5>HTML5</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-css3-alt fa-4x mb-3" style="color: rgb(50, 50, 255)"
                                aria-hidden="true"></i>
                            <h5>CSS3</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-js fa-4x mb-3" style="color: rgb(255, 183, 50)" aria-hidden="true"></i>
                            <h5>JavaScript</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-bootstrap fa-4x mb-3" style="color: rgb(50, 255, 50)"
                                aria-hidden="true"></i>
                            <h5>Bootstrap</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-solid fa-wind fa-4x mb-3" style="color: rgb(255, 50, 255)" aria-hidden="true"></i>
                            <h5>Vite</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-solid fa-bolt fa-4x mb-3" style="color: rgb(255, 255, 50)" aria-hidden="true"></i>
                            <h5>Livewire</h5>
                        </div>
                    </div>
                    {{-- Duplicado para loop infinito. Revisar si se puede cambiar --}}

                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-html5 fa-4x mb-3" style="color: rgb(255, 50, 50)" aria-hidden="true"></i>
                            <h5>HTML5</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-css3-alt fa-4x mb-3" style="color: rgb(50, 50, 255)"
                                aria-hidden="true"></i>
                            <h5>CSS3</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-js fa-4x mb-3" style="color: rgb(255, 183, 50)" aria-hidden="true"></i>
                            <h5>JavaScript</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-bootstrap fa-4x mb-3" style="color: rgb(50, 255, 50)"
                                aria-hidden="true"></i>
                            <h5>Bootstrap</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-solid fa-wind fa-4x mb-3" style="color: rgb(255, 50, 255)" aria-hidden="true"></i>
                            <h5>Vite</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-solid fa-bolt fa-4x mb-3" style="color: rgb(255, 255, 50)" aria-hidden="true"></i>
                            <h5>Livewire</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="featurette-divider">

    <!-- Herramientas -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">{{ trans('public.tecnologias.herramientas') }}</h2>
            <div class="tech-carousel-container">
                <div class="tech-carousel-track tech-carousel-reverse">
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-php fa-4x mb-3" style="color: rgb(50, 50, 255)" aria-hidden="true"></i>
                            <h5>PHP</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-laravel fa-4x mb-3" style="color: rgb(255, 50, 50)"
                                aria-hidden="true"></i>
                            <h5>Laravel</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-solid fa-database fa-4x mb-3" style="color: rgb(50, 255, 50)"
                                aria-hidden="true"></i>
                            <h5>MySQL</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-git-alt fa-4x mb-3" style="color: rgb(255, 255, 50)"
                                aria-hidden="true"></i>
                            <h5>Git</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-github fa-4x mb-3" style="color: rgb(255, 50, 255)"
                                aria-hidden="true"></i>
                            <h5>GitHub</h5>
                        </div>
                    </div>
                    {{-- <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-docker fa-4x mb-3" aria-hidden="true"></i>
                            <h5>Docker</h5>
                        </div>
                    </div> --}}
                    {{-- Duplicado para loop infinito --}}
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-php fa-4x mb-3" style="color: rgb(50, 50, 255)"
                                aria-hidden="true"></i>
                            <h5>PHP</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-laravel fa-4x mb-3" style="color: rgb(255, 50, 50)"
                                aria-hidden="true"></i>
                            <h5>Laravel</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-solid fa-database fa-4x mb-3" style="color: rgb(50, 255, 50)"
                                aria-hidden="true"></i>
                            <h5>MySQL</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-git-alt fa-4x mb-3" style="color: rgb(255, 255, 50)"
                                aria-hidden="true"></i>
                            <h5>Git</h5>
                        </div>
                    </div>
                    <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-github fa-4x mb-3" style="color: rgb(255, 50, 255)"
                                aria-hidden="true"></i>
                            <h5>GitHub</h5>
                        </div>
                    </div>
                    {{-- <div class="tech-item">
                        <div class="text-center">
                            <i class="fa-brands fa-docker fa-4x mb-3" aria-hidden="true"></i>
                            <h5>Docker</h5>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <hr class="featurette-divider">

    @include('public.partials.footer_volver')
@endsection
