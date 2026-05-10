<header id="header-top">
    <div class="container-md d-flex flex-wrap align-items-center justify-content-between py-3 mb-4">

        <div class="d-flex align-items-center gap-2">
            <details class="mobile-menu-details d-md-none">
                <summary class="mobile-menu-hamburger" aria-label="Menú">
                    <i class="fa-solid fa-bars"></i>
                </summary>

                <div class="mobile-menu-overlay">
                    <nav class="mobile-menu-sidebar">
                        <ul class="mobile-menu-links">
                            <li>
                                <a href="{{ route('home') }}" style="color: var(--terciary);">
                                    <i class="fa-solid fa-home"></i>
                                    {{ trans('public.menu.home') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('tecnologias') }}" style="color: var(--quaternary);">
                                    <i class="fa-solid fa-code"></i>
                                    {{ trans('public.menu.tecnologias') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('proyectos') }}" style="color: var(--terciary);">
                                    <i class="fa-solid fa-briefcase"></i>
                                    {{ trans('public.menu.proyectos') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contacto') }}" style="color: var(--quaternary);">
                                    <i class="fa-solid fa-envelope"></i>
                                    {{ trans('public.menu.contacto') }}
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </details>

            <div class="mb-2 mb-md-0">
                <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                    <img class="logo-nav" title="{{ config('app.name') }}"
                        alt="{{ trans('public.images.logo_principal') }}"
                        src="{{ asset('images/laravistaLogoSmaller.png') }}">
                </a>
            </div>
        </div>

        <ul class="nav col-auto mb-2 justify-content-center mb-md-0 desktop-menu">
            <li class="nav-item">
                <a class="nav-link menu-titulo" aria-current="page" href="{{ route('home') }}"
                    style="color: var(--terciary); -webkit-text-stroke: 1px var(--terciary);">{{ trans('public.menu.home') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link menu-titulo" href="{{ route('tecnologias') }}"
                    style="color: var(--quaternary); -webkit-text-stroke: 1px var(--quaternary);">{{ trans('public.menu.tecnologias') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link menu-titulo" href="{{ route('proyectos') }}"
                    style="color: var(--terciary); -webkit-text-stroke: 1px var(--terciary);">{{ trans('public.menu.proyectos') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link menu-titulo" href="{{ route('contacto') }}"
                    style="color: var(--quaternary); -webkit-text-stroke: 1px var(--quaternary);">{{ trans('public.menu.contacto') }}</a>
            </li>
        </ul>

        {{-- Se alinea al final y con el flex-grow toma todo el espacio que pueda ocupar --}}
        <div class="d-flex justify-content-end flex-grow-1 gap-2">
            @auth
                <div class="mx-1">
                    <div class="dropdown">
                        <button
                            class="btn btn-primary rounded-circle header-button btn-size-45 d-flex align-items-center justify-content-center p-0"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false"
                            title="{{ trans('public.usuario.selector') }}">
                            <i class="fa-solid fa-user" aria-hidden="true"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('panel.index') }}">
                                    {{ trans('public.usuario.panel') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            @endauth

            @include('public.partials.selectores_header')
        </div>
    </div>
</header>
