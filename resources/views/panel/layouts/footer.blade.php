{{-- Ahora mismo este footer lo comparte el panel y la parte de login --}}
<footer
    class="footer-panel container-fluid bg-primary shadow p-3 d-flex justify-content-between align-items-center fixed-bottom">
    <p class="mb-0 text-white-50 small">
        &copy; {{ today()->format('Y') }} {{ config('app.name') }}
    </p>
    <p class="mb-0 text-white-50 small">
        {{ trans('panel.footer.version') }}: {{ config('app.version') }}
    </p>

    @yield('scripts')

    @vite('resources/js/panel.js')
</footer>
