<form method="GET" action="{{ $routeIndex }}">
    @if ($ordenacionFormatoUrl !== null)
        {{-- Conservamos la ordenación actual al filtrar enviándola como hidden con su formato de URL "campo:dir?campo:dir" --}}
        <input type="hidden" name="ordenacion" value="{{ $ordenacionFormatoUrl }}">
    @endif

    <div class="mb-3 d-flex gap-2 flex-wrap">
        @if ($crearActivo)
            <a href="{{ $routeCreate }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1" aria-hidden="true"></i>
                {{ trans('actions.create') }}
            </a>
        @endif

        @if ($ordenacionEnUrl)
            <a href="{{ $urlBorrarOrdenacion }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-xmark me-1" aria-hidden="true"></i>
                {{ trans('actions.clear_ordenacion') }}
            </a>
        @endif

        @if ($exportarActivo)
            <a href="{{ $routeExport }}" class="btn btn-terciary ms-auto">
                <i class="fa-solid fa-file-excel me-1" aria-hidden="true"></i>
                {{ trans('actions.export') }}
            </a>
        @endif

        <button type="button" class="btn btn-secondary @if (!$exportarActivo) ms-auto @endif"
            data-bs-toggle="collapse" data-bs-target="#filtros-collapse"
            aria-expanded="{{ $filtrosEnUrl ? 'true' : 'false' }}" aria-controls="filtros-collapse">
            <i class="fa-solid fa-filter me-1" aria-hidden="true"></i>
            {{ trans('actions.filter') }}
        </button>
    </div>

    {{-- Sección desplegable de filtros: se mantiene abierta si hay errores o filtros aplicados --}}
    <div id="filtros-collapse" class="collapse @if ($filtrosEnUrl) show @endif mb-3">
        <div class="card card-body">
            <div class="row g-3">
                {{ $slot }}
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3">
                @if ($filtrosEnUrl)
                    <a href="{{ $urlBorrarFiltros }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-xmark me-1" aria-hidden="true"></i>
                        {{ trans('actions.clear_filters') }}
                    </a>
                @endif
                <button type="submit" class="btn btn-secondary">
                    <i class="fa-solid fa-magnifying-glass me-1" aria-hidden="true"></i>
                    {{ trans('actions.filter_submit') }}
                </button>
            </div>
        </div>
    </div>
</form>
