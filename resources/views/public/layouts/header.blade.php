@use('Mcamara\LaravelLocalization\Facades\LaravelLocalization')

<input type="checkbox" id="mobile-menu-toggle" class="mobile-menu-toggle" />

<div class="mobile-menu-overlay">
    <nav class="mobile-menu-sidebar">
        <label for="mobile-menu-toggle" class="mobile-menu-close">
            <i class="fa-solid fa-times"></i>
        </label>

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

<header id="header-top"
    class="container-md d-flex flex-wrap align-items-center justify-content-between py-3 mb-4">

    <div class="d-flex align-items-center gap-2">
        <label for="mobile-menu-toggle" class="mobile-menu-hamburger">
            <i class="fa-solid fa-bars"></i>
        </label>

        <div class="mb-2 mb-md-0">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img class="logo-nav" title="{{ config('app.name') }}" alt="{{ trans('public.images.logo_principal') }}"
                    src="{{ asset('images/laravista-smaller.png') }}">
            </a>
        </div>
    </div>

    <ul class="nav col-auto mb-2 justify-content-center mb-md-0 desktop-menu">
        <li class="nav-item">
            <a class="nav-link menu-titulo" aria-current="page" href="{{ route('home') }}" style="color: var(--terciary);">{{ trans('public.menu.home') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link menu-titulo" href="{{ route('tecnologias') }}" style="color: var(--quaternary);">{{ trans('public.menu.tecnologias') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link menu-titulo" href="{{ route('proyectos') }}" style="color: var(--terciary);">{{ trans('public.menu.proyectos') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link menu-titulo" href="{{ route('contacto') }}" style="color: var(--quaternary);">{{ trans('public.menu.contacto') }}</a>
        </li>
    </ul>

    <div class="d-flex gap-2">
        <div class="mx-1">
            <div class="dropdown">
                <button class="btn btn-primary rounded-circle header-button" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false" title="{{ trans('public.idioma.selector') }}" style="width: 45px; height: 45px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-globe"></i>
                </button>
                <ul class="dropdown-menu">
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        <li>
                            <a class="dropdown-item" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                <i class="fa-solid fa-check" style="visibility: {{ app()->getLocale() === $localeCode ? 'visible' : 'hidden' }}; width: 1rem;"></i>
                                {{ $properties['native'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mx-1">
            <div class="dropdown">
                <button class="btn btn-primary rounded-circle header-button" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false" style="width: 45px; height: 45px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    {{-- // TODO: añadir el que se ha seleccionado, guardándolo en una cookie --}}
                    <i id="modo-seleccionado" class="fa-solid fa-sun"></i>
                </button>
                <ul class="dropdown-menu">
                    <li class="dropdown-item" onclick="changePageMode(1)"><i class="fa-solid fa-sun"></i>
                        {{ trans('public.modo.luz') }}
                    </li>
                    <li class="dropdown-item" onclick="changePageMode(2)"><i class="fa-solid fa-moon"></i>
                        {{ trans('public.modo.oscuro') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
