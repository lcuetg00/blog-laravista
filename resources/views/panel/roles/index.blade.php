@extends('panel.layouts.app')

@use('App\Enums\RoleOrdenacionEnum')
@use('App\Helpers\RoleHelper')

@section('title', trans('fields.roles.titulo'))

@section('breadcrumbs', Breadcrumbs::render('panel.roles.index'))

@section('content')
    <x-panel.filtros :route-index="route('panel.roles.index')" :route-create="route('panel.roles.create')" :route-export="route('panel.roles.export', request()->query())" :campos-filtro="['busqueda']" :permiso-export="\App\Helpers\PermissionHelper::ROLES_EXPORTAR_PERMISSION">

        {{-- Filtros --}}
        <div class="col-12">
            <x-input name="busqueda" :label="trans('fields.input.busqueda')" :value="request('busqueda')" autocomplete="off" />
        </div>
    </x-panel.filtros>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-top">
            <caption class="visually-hidden">{{ trans('fields.roles.titulo') }}</caption>
            <thead>
                <tr>
                    <x-panel.ordenacion-columna :columna="RoleOrdenacionEnum::NOMBRE" :etiqueta="trans('fields.input.nombre_rol')" />
                    <x-panel.ordenacion-columna :columna="RoleOrdenacionEnum::DESCRIPCION" :etiqueta="trans('fields.input.descripcion')" />
                    <th scope="col" class="align-top text-start col-acciones">{{ trans('fields.acciones') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $rol)
                    <tr>
                        <td class="align-top">
                            <div class="table-row-text">{{ $rol->name }}</div>
                        </td>
                        <td class="align-top">
                            <div class="table-row-text">{{ $rol->descripcion }}</div>
                        </td>
                        <td class="align-top text-start col-acciones">
                            <a href="{{ route('panel.roles.show', $rol) }}" class="action-item text-blue popup me-2"
                                aria-label="{{ trans('actions.show') }}" data-popup="{{ trans('actions.show') }}">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </a>
                            @can(\App\Helpers\PermissionHelper::ROLES_EDITAR_PERMISSION)
                                <a href="{{ route('panel.roles.edit', $rol) }}" class="action-item text-yellow popup me-2"
                                    aria-label="{{ trans('actions.edit') }}" data-popup="{{ trans('actions.edit') }}">
                                    <i class="fa-solid fa-pencil" aria-hidden="true"></i>
                                </a>
                            @endcan
                            @can(\App\Helpers\PermissionHelper::ROLES_ELIMINAR_PERMISSION)
                                @if (RoleHelper::puedeBorrarRol($rol))
                                    <button type="button"
                                        class="action-item btn btn-link p-0 border-0 align-baseline text-red popup"
                                        data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $rol->ulid }}"
                                        aria-label="{{ trans('actions.delete') }}" data-popup="{{ trans('actions.delete') }}">
                                        <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                    </button>

                                    <x-confirm-modal :id="$rol->ulid" :action="route('panel.roles.destroy', $rol)" method="DELETE"
                                        acceptClass="btn-danger" />
                                @endif
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

    @if ($roles->hasPages())
        <div class="mt-3">
            {{ $roles->links() }}
        </div>
    @endif
@endsection
