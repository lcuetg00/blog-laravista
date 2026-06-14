<footer class="container-fluid bg-primary shadow p-3 d-flex flex-column justify-content-center align-items-center">
    <div>
        <p class="mt-3">&copy; {{ today()->format('Y') . ' ' . config('app.name') }} <a
                href="{{ route('politica_privacidad') }}">{{ trans('public.footer.politica_privacidad') }}</a> &middot; <a
                href="{{ route('terminos_condiciones') }}">{{ trans('public.footer.terminos_condiciones') }}</a> &middot; <a
                href="{{ route('creditos') }}">{{ trans('public.footer.creditos') }}</a>
        </p>
    </div>

    @yield('scripts')

    @vite('resources/js/public.js')
</footer>
