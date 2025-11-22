<footer class="container-fluid bg-primary shadow p-3 d-flex flex-column justify-content-center align-items-center">
    <div>
        <img class="img-fluid footer-image" title="Página en construcción" alt="Cartel de página en construcción"
            src="{{ asset('images/underConstruction.png') }}"></img>
    </div>

    <div>
        <p class="mt-3">&copy; {{ today()->format('Y') . ' ' }} . &middot; <a
                href="#">{{ trans('public.politica_privacidad') }}</a> &middot; <a
                href="#">{{ trans('public.terminos_condiciones') }}</a>
        </p>
    </div>

    <div>
        <p class="float-end"><a href="#header-top">{{ trans('actions.back_top') }}</a></p>
    </div>

    @yield('scripts')

    @vite('resources/js/public.js')
</footer>
