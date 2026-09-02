{{-- Listado tipo CRUD de los CVs del usuario, con modales Livewire de creación/edición --}}
@use('App\Enums\UsuarioCvOrdenacionEnum')
@use('App\Helpers\PermissionHelper')

<div>
    <div class="mb-3">
        @can(PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION)
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCv"
                wire:click="$dispatch('abrir-modal-cv')">
                <i class="fa-solid fa-plus me-1" aria-hidden="true"></i>
                {{ trans('fields.usuarios_cvs.crear_cv') }}
            </button>
        @endcan
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-top">
            <caption class="visually-hidden">{{ trans('fields.usuarios_cvs.titulo') }}</caption>
            <thead>
                <tr>
                    <x-panel.ordenacion-columna-livewire :columna="UsuarioCvOrdenacionEnum::NOMBRE" :etiqueta="trans('fields.usuarios_cvs.nombre')" :ordenacion="$ordenacion" />
                    <x-panel.ordenacion-columna-livewire :columna="UsuarioCvOrdenacionEnum::ACTUALIZADO_EN" :etiqueta="trans('fields.input.actualizado_en')" :ordenacion="$ordenacion" />
                    <x-panel.ordenacion-columna-livewire :columna="UsuarioCvOrdenacionEnum::CREADO_EN" :etiqueta="trans('fields.input.creado_en')" :ordenacion="$ordenacion" />
                    <th scope="col" class="text-start col-acciones">{{ trans('fields.acciones') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cvs as $cv)
                    <tr wire:key="cv-{{ $cv->ulid }}">
                        <td class="align-middle">
                            <div class="table-row-text">{{ $cv->nombre }}</div>
                        </td>
                        <td class="align-middle">
                            {{ $cv->updated_at?->format('d/m/Y H:i') }}
                        </td>
                        <td class="align-middle">
                            {{ $cv->created_at?->format('d/m/Y H:i') }}
                        </td>
                        <td class="align-middle text-start col-acciones">
                            @can(PermissionHelper::USUARIOS_CVS_EDITAR_PERMISSION)
                                <button type="button"
                                    class="action-item btn btn-link p-0 border-0 align-baseline text-yellow popup me-2"
                                    data-bs-toggle="modal" data-bs-target="#modalCv"
                                    wire:click="$dispatch('abrir-modal-cv', { ulid: '{{ $cv->ulid }}' })"
                                    aria-label="{{ trans('actions.edit') }}" data-popup="{{ trans('actions.edit') }}">
                                    <i class="fa-solid fa-pencil" aria-hidden="true"></i>
                                </button>

                                <button type="button"
                                    class="action-item btn btn-link p-0 border-0 align-baseline text-blue popup me-2"
                                    data-bs-toggle="modal" data-bs-target="#modalSecciones"
                                    wire:click="$dispatch('abrir-modal-secciones', { ulid: '{{ $cv->ulid }}' })"
                                    aria-label="{{ trans('fields.usuarios_cvs.secciones.titulo') }}"
                                    data-popup="{{ trans('fields.usuarios_cvs.secciones.titulo') }}">
                                    <i class="fa-solid fa-list-ul" aria-hidden="true"></i>
                                </button>
                            @endcan
                            @can(PermissionHelper::USUARIOS_CVS_ELIMINAR_PERMISSION)
                                <button type="button"
                                    class="action-item btn btn-link p-0 border-0 align-baseline text-red popup"
                                    data-bs-toggle="modal" data-bs-target="#modalEliminarCv"
                                    wire:click="$dispatch('abrir-modal-eliminar-cv', { ulid: '{{ $cv->ulid }}' })"
                                    aria-label="{{ trans('actions.delete') }}" data-popup="{{ trans('actions.delete') }}">
                                    <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            {{ trans('fields.sin_registros') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($cvs->hasPages())
        <div class="mt-3">
            {{ $cvs->links() }}
        </div>
    @endif

    {{-- Modal de creación/edición y de borrado de un CV --}}
    <livewire:usuario-cv-form-livewire :usuario="$usuario" />

    {{-- Modal de gestión de las secciones de un CV --}}
    <livewire:usuario-cv-secciones-modal-livewire :usuario="$usuario" />
</div>
