@extends('panel.layouts.app')

@use('App\Enums\UsuarioOrdenacionEnum')
@use('App\Helpers\UsuarioHelper')

@section('title', trans('fields.usuarios.titulo'))

@section('breadcrumbs', Breadcrumbs::render('panel.usuarios.index'))

@section('content')
    <x-panel.filtros :route-index="route('panel.usuarios.index')" :route-create="route('panel.usuarios.create')" :route-export="route('panel.usuarios.export', request()->query())" :campos-filtro="['nombre_completo', 'email']" :permiso-export="\App\Helpers\PermissionHelper::USUARIOS_EXPORTAR_PERMISSION">

        {{-- Filtros --}}
        <div class="col-12 col-md-6">
            <x-input name="nombre_completo" :label="trans('fields.input.nombre_completo')" :value="request('nombre_completo')" autocomplete="off" />
        </div>

        <div class="col-12 col-md-6">
            <x-input name="email" type="text" :label="trans('fields.input.email')" :value="request('email')" autocomplete="off" />
        </div>
    </x-panel.filtros>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-top">
            <caption class="visually-hidden">{{ trans('fields.usuarios.titulo') }}</caption>
            <thead>
                <tr>
                    <x-panel.ordenacion-columna :columna="UsuarioOrdenacionEnum::NOMBRE" :etiqueta="trans('fields.input.nombre')" />
                    <x-panel.ordenacion-columna :columna="UsuarioOrdenacionEnum::PRIMER_APELLIDO" :etiqueta="trans('fields.input.primer_apellido')" />
                    <x-panel.ordenacion-columna :columna="UsuarioOrdenacionEnum::SEGUNDO_APELLIDO" :etiqueta="trans('fields.input.segundo_apellido')" />
                    <x-panel.ordenacion-columna :columna="UsuarioOrdenacionEnum::EMAIL" :etiqueta="trans('fields.input.email')" />
                    <th scope="col" class="align-top text-start col-acciones">{{ trans('fields.acciones') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $usuario)
                    <tr>
                        <td class="align-top">
                            <div class="table-row-text">{{ $usuario->nombre }}</div>
                        </td>
                        <td class="align-top">
                            <div class="table-row-text">{{ $usuario->primer_apellido }}</div>
                        </td>
                        <td class="align-top">
                            <div class="table-row-text">{{ $usuario->segundo_apellido }}</div>
                        </td>
                        <td class="align-top">
                            <div class="table-row-text">{{ $usuario->email }}</div>
                        </td>
                        <td class="align-top text-start col-acciones">
                            <a href="{{ route('panel.usuarios.show', $usuario) }}" class="action-item text-blue popup me-2"
                                aria-label="{{ trans('actions.show') }}" data-popup="{{ trans('actions.show') }}">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </a>
                            @if (UsuarioHelper::puedeModificarUsuario(auth()->user(), $usuario))
                                <a href="{{ route('panel.usuarios.edit', $usuario) }}"
                                    class="action-item text-yellow popup me-2" aria-label="{{ trans('actions.edit') }}"
                                    data-popup="{{ trans('actions.edit') }}">
                                    <i class="fa-solid fa-pencil" aria-hidden="true"></i>
                                </a>
                            @endif
                            @if (UsuarioHelper::puedeBorrarUsuario(auth()->user(), $usuario))
                                <button type="button"
                                    class="action-item btn btn-link p-0 border-0 align-baseline text-red popup"
                                    data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $usuario->ulid }}"
                                    aria-label="{{ trans('actions.delete') }}" data-popup="{{ trans('actions.delete') }}">
                                    <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                </button>

                                <x-confirm-modal :id="$usuario->ulid" :action="route('panel.usuarios.destroy', $usuario)" method="DELETE"
                                    acceptClass="btn-danger" />
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            {{ trans('fields.sin_registros') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($usuarios->hasPages())
        <div class="mt-3">
            {{ $usuarios->links() }}
        </div>
    @endif
@endsection
