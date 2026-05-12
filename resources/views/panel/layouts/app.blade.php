<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', trans('panel.dashboard')) — {{ config('app.name') }}</title>

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
            @include('panel.layouts.sidebar')

            {{-- Contenido principal --}}
            <main class="col-md-9 col-lg-10">
                <div class="py-3 px-4 fw-bold breadcrumb-bar">
                    @yield('breadcrumbs')
                </div>

                <div class="ms-sm-auto px-4 py-3">
                    @yield('content')
                </div>
            </main>

        </div>
    </div>

    @include('panel.layouts.footer')
</body>

</html>
