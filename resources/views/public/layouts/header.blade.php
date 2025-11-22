<header id="header-top"
    class="container-md d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4">

    <div class="col-md-3 mb-2 mb-md-0">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img class="logo-nav" title="{{ config('app.name') }}" alt="{{ trans('public.images.logo_principal') }}"
                src="{{ asset('images/laravista-smaller.png') }}">
        </a>
    </div>

    <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
        <li class="nav-item">
            <a class="nav-link menu-titulo" aria-current="page" href="#">{{ trans('public.menu.home') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link menu-titulo" href="#">{{ trans('public.menu.posts') }}</a>
        </li>
    </ul>

    <div class="col-md-3 d-flex justify-content-end">
        <div class="mx-1">
            <button type="button" class="btn btn-primary">
                <i class="fa-solid fa-house"></i>
            </button>
        </div>

        <div class="mx-1">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
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
