{{-- Contenido común de las páginas de error (icono, título y descripción) --}}
<div class="section-main-hero py-5 my-4 text-center">
    <div class="container">
        <h1 class="mb-3">{{ $titulo }}</h1>

        <i class="fa-solid fa-wrench fa-4x my-4 text-secondary" aria-hidden="true"></i>

        <div class="d-flex justify-content-center">
            <p class="text-center text-muted" style="max-width: 600px;">{{ $descripcion }}</p>
        </div>
    </div>
</div>

@include('public.partials.footer_volver')
