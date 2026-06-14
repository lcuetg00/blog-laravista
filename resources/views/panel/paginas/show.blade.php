@extends('panel.layouts.app')

@use('App\Enums\ActivadoEnum')
@use('App\Helpers\PermissionHelper')

@section('title', trans('fields.paginas.detalle'))

@section('breadcrumbs', Breadcrumbs::render('panel.paginas.show', $pagina))

@section('content')
    @php
        // Idiomas activos (locale => nombre nativo) con el idioma actual el primero, para las pestañas del detalle
        $idiomas = \App\Helpers\IdiomaHelper::getIdiomasActivos();
    @endphp

    <div class="card shadow">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h5 mb-0">{{ trans('fields.paginas.detalle') }}</h2>
            <div class="d-flex gap-2">
                {{-- Panel de vista previa anclado abajo a la derecha (minimizable). Solo para páginas públicas (es un iframe que carga la vista real) --}}
                @if ($pagina->activo)
                    @include('panel.paginas.partials.preview', ['pagina' => $pagina])
                @endif
                @can(PermissionHelper::PAGINAS_EDITAR_PERMISSION)
                    <a href="{{ route('panel.paginas.edit', $pagina) }}" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-pencil me-1" aria-hidden="true"></i>
                        {{ trans('actions.edit') }}
                    </a>
                @endcan
            </div>
        </div>

        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="fw-bold">{{ trans('fields.input.clave') }}</div>
                    <div><code>{{ $pagina->clave }}</code></div>
                </div>

                <div class="col-md-6">
                    <div class="fw-bold">{{ trans('fields.input.activo') }}</div>
                    <div>
                        <span class="badge {{ $pagina->activo ? 'text-bg-success' : 'text-bg-secondary' }}">
                            {{ ActivadoEnum::from((int) $pagina->activo)->trans() }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Selector de pestañas por idioma para alternar entre los textos sin saturar la vista --}}
            <ul class="nav nav-tabs mt-4" id="idiomasTab" role="tablist">
                @foreach ($idiomas as $locale => $nombreIdioma)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $locale }}"
                            data-bs-toggle="tab" data-bs-target="#pane-{{ $locale }}" type="button" role="tab"
                            aria-controls="pane-{{ $locale }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ $nombreIdioma }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content border border-top-0 rounded-bottom p-3">
                @foreach ($idiomas as $locale => $nombreIdioma)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pane-{{ $locale }}"
                        role="tabpanel" aria-labelledby="tab-{{ $locale }}" tabindex="0">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="fw-bold">{{ trans('fields.input.titulo') }}</div>
                                <div>{{ $pagina->getTranslation('titulo', $locale, false) }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="fw-bold">{{ trans('fields.input.descripcion') }}</div>
                                <div>{{ $pagina->getTranslation('descripcion', $locale, false) ?: '—' }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('panel.paginas.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i>
                {{ trans('actions.back') }}
            </a>
        </div>
    </div>

    {{-- Listado de los bloques que componen la página, tal y como se verían publicados --}}
    <div class="card shadow mt-4">
        <div class="card-header">
            <h2 class="h5 mb-0">{{ trans('fields.bloques.titulo') }}</h2>
        </div>

        <div class="card-body">
            @if ($pagina->bloques->isNotEmpty())
                {{-- Selector global de idioma del preview: alterna a la vez el idioma de todos los bloques mostrados --}}
                <ul class="nav nav-tabs" id="bloquesIdiomaTab" role="tablist"
                    aria-label="{{ trans('fields.bloques.idioma_preview') }}">
                    @foreach ($idiomas as $locale => $nombreIdioma)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="bloques-tab-{{ $locale }}"
                                data-bs-toggle="tab" data-bs-target="#bloques-pane-{{ $locale }}" type="button"
                                role="tab" aria-controls="bloques-pane-{{ $locale }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                {{ $nombreIdioma }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content border border-top-0 rounded-bottom p-3">
                    @foreach ($idiomas as $locale => $nombreIdioma)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                            id="bloques-pane-{{ $locale }}" role="tabpanel"
                            aria-labelledby="bloques-tab-{{ $locale }}" tabindex="0">
                            @foreach ($pagina->bloques as $bloque)
                                @include('panel.paginas.partials.bloque-preview', ['bloque' => $bloque, 'locale' => $locale])
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0">{{ trans('fields.bloques.sin_bloques') }}</p>
            @endif
        </div>
    </div>

@endsection
