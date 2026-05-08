<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('panel.dashboard')) — {{ config('app.name') }}</title>

    {{-- Bootstrap + FontAwesome --}}
    @vite('resources/js/app.js')

    {{-- CSS del panel --}}
    @vite('resources/css/panel.css')
</head>

<body>
    {{-- Header sticky --}}
    <div class="container-fluid bg-primary sticky-top shadow">
        @include('panel.layouts.header')
    </div>

    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">

            {{-- Sidebar --}}
            <nav class="sidebar border-end col-md-3 col-lg-2 p-0 bg-body-tertiary"
                 aria-label="{{ __('panel.sidebar_nav') }}">
                <div class="offcanvas-md offcanvas-start bg-body-tertiary"
                     tabindex="-1"
                     id="sidebarMenu"
                     aria-labelledby="sidebarMenuLabel">

                    <div class="offcanvas-header border-bottom">
                        <h5 class="offcanvas-title" id="sidebarMenuLabel">{{ config('app.name') }}</h5>
                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="offcanvas"
                                data-bs-target="#sidebarMenu"
                                aria-label="{{ __('panel.close') }}">
                        </button>
                    </div>

                    <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
                        <ul class="nav flex-column px-2 pt-2">
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('panel.index') ? 'active' : '' }}"
                                   aria-current="{{ request()->routeIs('panel.index') ? 'page' : 'false' }}"
                                   href="{{ route('panel.index') }}">
                                    <i class="fa-solid fa-house-chimney" aria-hidden="true"></i>
                                    {{ __('panel.dashboard') }}
                                </a>
                            </li>
                        </ul>

                        <hr class="my-2 mx-2">

                        <ul class="nav flex-column px-2 pb-2 mb-auto">
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2" href="#">
                                    <i class="fa-solid fa-gear" aria-hidden="true"></i>
                                    {{ __('panel.settings') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="sidebar-logout-btn">
                                        <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                                        {{ __('panel.sign_out') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            {{-- Contenido principal --}}
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-3">
                @yield('content')
            </main>

        </div>
    </div>

    @include('panel.layouts.footer')
</body>

</html>
