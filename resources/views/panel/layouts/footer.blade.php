<footer class="container-fluid bg-primary shadow p-3 d-flex flex-column justify-content-center align-items-center fixed-bottom">
    <p class="mb-0 text-white-50 small">
        &copy; {{ today()->format('Y') }} {{ config('app.name') }}
    </p>

    @yield('scripts')

    @vite('resources/js/panel.js')
</footer>
