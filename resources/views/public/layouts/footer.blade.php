<footer class="container-fluid bg-primary shadow p-3 d-flex flex-column justify-content-center align-items-center">

    <div>
        <p class="mt-3">&copy; {{ today()->format('Y') . ' ' }} . &middot; <a
                href="#">{{ trans('public.politica_privacidad') }}</a> &middot; <a
                href="#">{{ trans('public.terminos_condiciones') }}</a> &middot; <a
                href="{{ route('credits') }}">Créditos</a>
        </p>
    </div>

    <div>
        <p class="float-end"><a href="#header-top">{{ trans('actions.back_top') }}</a></p>
    </div>

    @yield('scripts')

    @vite('resources/js/public.js')
</footer>
