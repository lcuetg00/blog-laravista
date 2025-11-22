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
        @include('public.layouts.header')
    </div>

    <div class="container-md container-fluid">
        @yield('content')
    </div>

    @include('public.layouts.footer')
</body>

</html>
