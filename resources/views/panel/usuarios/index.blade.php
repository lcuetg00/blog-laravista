@extends('panel.layouts.app')

@section('title', trans('fields.usuarios.titulo'))

@section('breadcrumbs', Breadcrumbs::render('panel.usuarios.index'))

@section('content')
    <div class="mb-3">
        <a href="{{ route('panel.usuarios.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1" aria-hidden="true"></i>
            {{ trans('actions.create') }}
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-top">
            <caption class="visually-hidden">{{ trans('fields.usuarios.titulo') }}</caption>
            <thead>
                <tr>
                    <th scope="col" class="align-top">{{ trans('fields.input.nombre') }}</th>
                    <th scope="col" class="align-top">{{ trans('fields.input.primer_apellido') }}</th>
                    <th scope="col" class="align-top">{{ trans('fields.input.segundo_apellido') }}</th>
                    <th scope="col" class="align-top">{{ trans('fields.input.email') }}</th>
                    <th scope="col" class="align-top text-start col-acciones">{{ trans('fields.acciones') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $usuario)
                    <tr>
                        <td class="align-top"><div class="table-row-text">{{ $usuario->nombre }}</div></td>
                        <td class="align-top"><div class="table-row-text">{{ $usuario->primer_apellido }}</div></td>
                        <td class="align-top"><div class="table-row-text">{{ $usuario->segundo_apellido }}</div></td>
                        <td class="align-top"><div class="table-row-text">{{ $usuario->email }}</div></td>
                        <td class="align-top text-start col-acciones">
                            <a href="{{ route('panel.usuarios.show', $usuario) }}"
                                class="action-item text-blue popup me-2"
                                aria-label="{{ trans('actions.show') }}"
                                data-popup="{{ trans('actions.show') }}">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </a>
                            <a href="{{ route('panel.usuarios.edit', $usuario) }}"
                                class="action-item text-yellow popup me-2"
                                aria-label="{{ trans('actions.edit') }}"
                                data-popup="{{ trans('actions.edit') }}">
                                <i class="fa-solid fa-pencil" aria-hidden="true"></i>
                            </a>
                            <button type="button"
                                class="action-item btn btn-link p-0 border-0 align-baseline text-red popup"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmModal-{{ $usuario->ulid }}"
                                aria-label="{{ trans('actions.delete') }}"
                                data-popup="{{ trans('actions.delete') }}">
                                <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                            </button>

                            <x-confirm-modal
                                :id="$usuario->ulid"
                                :action="route('panel.usuarios.destroy', $usuario)"
                                method="DELETE"
                                acceptClass="btn-danger" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            {{ trans('fields.usuarios.vacio') }}
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
