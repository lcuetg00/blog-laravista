@use('Mcamara\LaravelLocalization\Facades\LaravelLocalization')
@use('App\Enums\ThemeEnum')

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
                        src="{{ asset('images/laravistaLogo.png') }}">
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
            <div class="mx-1">
                <div class="dropdown">
                    <button
                        class="btn btn-primary rounded-circle header-button btn-size-45 d-flex align-items-center justify-content-center p-0"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        title="{{ trans('public.idioma.selector') }}">
                        <span id="idioma-seleccionado" class="idioma-texto">{{ strtoupper(app()->getLocale()) }}</span>
                    </button>
                    <ul class="dropdown-menu">
                        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            @if (app()->getLocale() !== $localeCode)
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                        <span>{{ $properties['native'] }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="mx-1">
                <div class="dropdown">
                    <button
                        class="btn btn-primary rounded-circle header-button btn-size-45 d-flex align-items-center justify-content-center p-0"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        title="{{ trans('public.modo.selector') }}">
                        <i id="modo-seleccionado" class="fa-solid fa-sun"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li class="dropdown-item" onclick="changePageMode({{ ThemeEnum::LUZ->value }})">
                            <i class="fa-solid fa-sun"></i>
                            <span>{{ trans('public.modo.luz') }}</span>
                        </li>
                        <li class="dropdown-item" onclick="changePageMode({{ ThemeEnum::OSCURO->value }})">
                            <i class="fa-solid fa-moon"></i>
                            <span>{{ trans('public.modo.oscuro') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
