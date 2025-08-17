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
                    <a class="nav-link active" aria-current="page" href="#">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Posts</a>
                </li>
            </ul>

            <div class="col-md-3 text-end"> <button type="button" class="btn btn-outline-primary me-2">Login</button>
                <button type="button" class="btn btn-primary">

                    <i class="fa-solid fa-house"></i>
                </button>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous">
    </script>
</footer>

</html>
