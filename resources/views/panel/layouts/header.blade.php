{{-- Ahora mismo este header lo comparte el panel y la parte de login --}}
<header id="panel-header">
    <div class="container-fluid d-flex flex-nowrap align-items-center justify-content-between py-2 px-3">

        {{-- Si el usuario no está autenticado significa que está en el login,
             así que el header no debe revelar nada del panel: el logo y el
             texto enlazan al home público y se oculta el botón de hamburguesa. --}}
        <div class="d-flex align-items-center gap-2">
            <a href="{{ auth()->check() ? route('panel.index') : route('home') }}"
                class="d-flex align-items-center d-none d-md-flex">
                <img class="logo-nav logo-header" src="{{ asset('images/laravistaLogoSmaller.png') }}"
                    alt="{{ config('app.name') }}" title="{{ config('app.name') }}">
            </a>

            {{-- Hamburguesa para abrir el sidebar en móvil (solo si hay sesión) --}}
            @auth
                <button
                    class="btn btn-primary rounded-circle header-button btn-size-45 d-flex align-items-center justify-content-center p-0 d-md-none"
                    type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu"
                    aria-expanded="false" aria-label="{{ trans('panel.toggle_sidebar') }}">
                    <i class="fa-solid fa-bars" aria-hidden="true"></i>
                </button>
            @endauth

            <a class="text-white text-decoration-none fs-5 fw-semibold"
                href="{{ auth()->check() ? route('panel.index') : route('home') }}">
                {{ config('app.name') }}
            </a>
        </div>

        <div class="d-flex align-items-center gap-2">
            {{-- Menú de usuario (solo si está autenticado) --}}
            @auth
                <div class="mx-1">
                    <div class="dropdown">
                        <button
                            class="btn btn-primary rounded-circle header-button btn-size-45 d-flex align-items-center justify-content-center p-0"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false"
                            title="{{ trans('panel.usuario_selector') }}">
                            <i class="fa-solid fa-user" aria-hidden="true"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('home') }}">
                                    {{ trans('panel.inicio_publico') }}
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        {{ trans('panel.sign_out') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            @endauth

            @include('public.partials.selectores_header')
        </div>
    </div>
</header>
