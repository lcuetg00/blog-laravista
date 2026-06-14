<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <x-seo-meta :meta="$seo ?? []" />

    <link rel="icon" href="{{ asset('images/laravista-smaller.png') }}">
    <link rel="sitemap" type="application/xml" href="{{ asset('sitemap.xml') }}" />

    {{-- Importo el vite que se genera con lo que tengo instalado (FontAwsome, Bootstrap) --}}
    @vite('resources/js/app.js')

    {{-- Css público --}}
    @vite('resources/css/public.css')

    {{-- Livewire en bundling manual (inject_assets=false): estilos y config que lee el Livewire empaquetado en app.js --}}
    @livewireStyles
</head>

<body>
    <div class="container-fluid bg-primary sticky-top shadow">
        @include('public.layouts.header')
    </div>

    {{-- Añadido min-vh-100 para que la página siempre ocupe el 100% y el footer no se suba --}}
    <div class="container-md container-fluid min-vh-100">
        @yield('content')
    </div>

    @include('public.layouts.footer')

    {{-- Botón scroll to top --}}
    <button id="scroll-to-top"
        class="btn btn-primary rounded-circle header-button btn-size-45 d-flex align-items-center justify-content-center p-0 position-fixed shadow"
        style="bottom: 2rem; right: 2rem; z-index: 1000;" type="button" title="{{ trans('public.scroll_to_top') }}"
        aria-label="{{ trans('public.scroll_to_top') }}" onclick="window.scrollTo({ top: 0, behavior: 'smooth' })">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    {{-- Livewire en bundling manual (inject_assets=false): estilos y config que lee el Livewire empaquetado en app.js --}}
    @livewireScriptConfig
</body>

</html>
