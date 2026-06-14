@extends('panel.layouts.app')

@use('App\Enums\ActivadoEnum')
@use('App\Enums\PaginaOrdenacionEnum')
@use('App\Helpers\PermissionHelper')

@section('title', trans('fields.paginas.titulo'))

@section('breadcrumbs', Breadcrumbs::render('panel.paginas.index'))

@section('content')
    <x-panel.filtros :route-index="route('panel.paginas.index')" :campos-filtro="['busqueda', 'activo']">

        {{-- Filtros --}}
        <div class="col-12 col-md-6">
            <x-input name="busqueda" :label="trans('fields.input.busqueda')" :value="request('busqueda')" autocomplete="off" />
        </div>

        <div class="col-12 col-md-6">
            @php($activoFiltro = request('activo'))
            <label for="activo" class="form-label">{{ trans('fields.input.activo') }}</label>
            <select id="activo" name="activo" class="form-select @error('activo') is-invalid @enderror">
                <option value="" @selected($activoFiltro === null || $activoFiltro === '')>{{ trans('fields.placeholders.seleccione_opcion') }}</option>
                @foreach (ActivadoEnum::cases() as $opcion)
                    <option value="{{ $opcion->value }}" @selected($activoFiltro === (string) $opcion->value)>{{ $opcion->trans() }}</option>
                @endforeach
            </select>
            @error('activo')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </x-panel.filtros>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-top">
            <caption class="visually-hidden">{{ trans('fields.paginas.titulo') }}</caption>
            <thead>
                <tr>
                    <x-panel.ordenacion-columna :columna="PaginaOrdenacionEnum::TITULO" :etiqueta="trans('fields.input.titulo')" />
                    <x-panel.ordenacion-columna :columna="PaginaOrdenacionEnum::ACTIVO" :etiqueta="trans('fields.input.activo')" />
                    <th scope="col" class="align-top text-start col-acciones">{{ trans('fields.acciones') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($paginas as $pagina)
                    <tr>
                        <td class="align-top">
                            <div class="table-row-text">{{ $pagina->titulo }}</div>
                        </td>
                        <td class="align-top">
                            <div class="table-row-text">
                                <span class="badge {{ $pagina->activo ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ ActivadoEnum::from((int) $pagina->activo)->trans() }}
                                </span>
                            </div>
                        </td>
                        <td class="align-top text-start col-acciones">
                            @can(PermissionHelper::PAGINAS_VER_PERMISSION)
                                <a href="{{ route('panel.paginas.show', $pagina) }}" class="action-item text-blue popup me-2"
                                    aria-label="{{ trans('actions.show') }}" data-popup="{{ trans('actions.show') }}">
                                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                </a>
                            @endcan
                            @can(PermissionHelper::PAGINAS_EDITAR_PERMISSION)
                                <a href="{{ route('panel.paginas.edit', $pagina) }}"
                                    class="action-item text-yellow popup me-2" aria-label="{{ trans('actions.edit') }}"
                                    data-popup="{{ trans('actions.edit') }}">
                                    <i class="fa-solid fa-pencil" aria-hidden="true"></i>
                                </a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            {{ trans('fields.sin_registros') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($paginas->hasPages())
        <div class="mt-3">
            {{ $paginas->links() }}
        </div>
    @endif
@endsection
