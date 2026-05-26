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
        <table class="table table-striped table-hover align-middle">
            <caption class="visually-hidden">{{ trans('fields.usuarios.titulo') }}</caption>
            <thead>
                <tr>
                    <th scope="col">{{ trans('fields.input.nombre') }}</th>
                    <th scope="col">{{ trans('fields.input.primer_apellido') }}</th>
                    <th scope="col">{{ trans('fields.input.segundo_apellido') }}</th>
                    <th scope="col">{{ trans('fields.input.email') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->nombre }}</td>
                        <td>{{ $usuario->primer_apellido }}</td>
                        <td>{{ $usuario->segundo_apellido }}</td>
                        <td>{{ $usuario->email }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
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
