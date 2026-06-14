@extends('panel.layouts.app')

@section('title', trans('fields.paginas.titulo'))

@section('breadcrumbs', Breadcrumbs::render('panel.paginas.edit', $pagina))

@section('content')
    {{-- Datos de la página: formulario reactivo que valida y guarda sin recargar --}}
    <livewire:pagina-modificacion-livewire :pagina="$pagina" />

    {{-- Bloques de la página: cada uno es un componente Livewire que se edita y guarda de forma independiente dentro del acordeón --}}
    <div class="card shadow mt-4">
        <div class="card-header">
            <h2 class="h5 mb-0">{{ trans('fields.bloques.titulo') }}</h2>
        </div>

        <div class="card-body">
            @forelse ($pagina->bloques as $bloque)
                @if ($loop->first)
                    <div class="accordion" id="acordeonBloques">
                @endif

                <livewire:bloque-modificacion-livewire :bloque="$bloque" :key="'bloque-'.$bloque->ulid" />

                @if ($loop->last)
                    </div>
                @endif
            @empty
                <p class="text-muted mb-0">{{ trans('fields.bloques.sin_bloques') }}</p>
            @endforelse
        </div>
    </div>

    {{-- Panel de vista previa anclado abajo a la derecha (minimizable), arranca minimizado y se recarga en vivo al guardar --}}
    @if ($pagina->activo)
        @include('panel.paginas.partials.preview', ['pagina' => $pagina, 'mostrarExtendido' => false])
    @endif
@endsection
