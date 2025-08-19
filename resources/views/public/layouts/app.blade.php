<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Luis Cueto">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('images/laravista-smaller.png') }}">

    <!-- Bootstrap core CSS. Dejado de usar, instalado por npm -->

    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous"> --}}

    {{-- Importo el vite que se genera con lo que tengo instalado (FontAwsome, Bootstrap) --}}
    @vite('resources/js/app.js')


    {{-- Css público --}}
    @vite('resources/css/public.css')
</head>

<body>
    <div class="container-fluid bg-primary sticky-top  shadow">
        <header id="header-top"
            class="container-md d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4">

            <div class="col-md-3 mb-2 mb-md-0">
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <img class="logo-nav" src="{{ asset('images/laravista-smaller.png') }}">
                </a>
            </div>

            <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">{{ trans('public.menu.home') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Posts</a>
                </li>
            </ul>

            <div class="col-md-3 d-flex justify-content-end">
                <div class="mx-1">
                    <button type="button" class="btn btn-outline-primary me-2">Login</button>
                </div>

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
    </div>

    <div class="container-md container-fluid">
        @yield('content')
    </div>
</body>


<footer class="container-md">
    <img class="img-fluid" src="{{ asset('images/underConstruction.png') }}"></img>
    <p class="float-end"><a href="#">Back to top</a></p>
    <p>&copy; {{ today()->format('Y') }} Company, Inc. &middot; <a href="#">Privacy</a> &middot; <a
            href="#">Terms</a>
    </p>

    @yield('scripts')


    @vite('resources/js/public.js')
</footer>

</html>
