@use('App\Enums\ThemeEnum')

<header id="panel-header">
    <div class="container-fluid d-flex flex-nowrap align-items-center justify-content-between py-2 px-3">

        <div class="d-flex align-items-center gap-2">
            {{-- Hamburguesa para abrir el sidebar en móvil --}}
            <button
                class="btn btn-primary rounded-circle header-button btn-size-45 d-flex align-items-center justify-content-center p-0 d-md-none"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#sidebarMenu"
                aria-controls="sidebarMenu"
                aria-expanded="false"
                aria-label="{{ __('panel.toggle_sidebar') }}"
            >
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>

            <a class="text-white text-decoration-none fs-5 fw-semibold" href="{{ route('panel.index') }}">
                {{ config('app.name') }}
            </a>
        </div>

        <div class="d-flex align-items-center gap-2">
            {{-- Nombre del usuario autenticado --}}
            @auth
                <span class="panel-user-name d-none d-sm-inline">
                    <i class="fa-solid fa-circle-user me-1" aria-hidden="true"></i>
                    {{ Auth::user()->name }}
                </span>
            @endauth

            {{-- Selector de tema claro/oscuro --}}
            <div class="mx-1">
                <div class="dropdown">
                    <button
                        class="btn btn-primary rounded-circle header-button btn-size-45 d-flex align-items-center justify-content-center p-0"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        title="{{ __('panel.theme_selector') }}"
                    >
                        <i id="modo-seleccionado" class="fa-solid fa-sun" aria-hidden="true"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-item" onclick="changePageMode({{ ThemeEnum::LUZ->value }})">
                            <i class="fa-solid fa-sun me-2" aria-hidden="true"></i>
                            {{ __('panel.theme_light') }}
                        </li>
                        <li class="dropdown-item" onclick="changePageMode({{ ThemeEnum::OSCURO->value }})">
                            <i class="fa-solid fa-moon me-2" aria-hidden="true"></i>
                            {{ __('panel.theme_dark') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
