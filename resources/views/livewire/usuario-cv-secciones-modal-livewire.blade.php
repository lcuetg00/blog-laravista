{{-- Modal maestro-detalle de las secciones de un CV: barra lateral derecha (crear, reordenar arrastrando con x-sort) + detalle central de la sección seleccionada, con confirmación (gestionada en el servidor) antes de perder cambios sin guardar --}}
@use('App\Helpers\PermissionHelper')

{{-- Estos tres eventos los dispara el servidor con dispatch() desde el componente Livewire, no el HTML. La lógica PHP decide
cuándo abrir o cerrar cada modal (si hay cambios sin guardar, acción pendiente...) y Alpine solo escucha el evento en window
y llama a la instancia de Bootstrap a mano, en lugar de usar los atributos data-bs-* --}}
<div
    x-on:secciones-abrir-confirmar-descarte.window="window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalSeccionesConfirmarDescarte')).show()"
    x-on:secciones-confirmar-descarte-ocultar.window="window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalSeccionesConfirmarDescarte')).hide()"
    x-on:secciones-cerrar.window="window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalSecciones')).hide()">
    {{-- data-bs-focus="false": desactiva el focus-trap de Bootstrap, que si no le robaría el foco al panel de vista previa del PDF (vive fuera del modal) en cuanto se hiciera clic en sus botones --}}
    <div class="modal fade" id="modalSecciones" tabindex="-1" aria-labelledby="modalSeccionesTitulo" aria-hidden="true"
        wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" data-bs-focus="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5" id="modalSeccionesTitulo">
                        {{ trans('fields.usuarios_cvs.secciones.titulo') }}
                        @if ($this->cv !== null)
                            — {{ $this->cv->nombre }}
                        @endif
                    </h2>
                    <button type="button" class="btn-close" aria-label="{{ trans('actions.cancel') }}"
                        wire:click="intentarCerrarModal"></button>
                </div>

                <div class="modal-body text-start">
                    {{-- Alto fijo solo en escritorio (.secciones-modal-row): en móvil la fila fluye con su contenido, con el listado arriba y el detalle debajo --}}
                    <div class="row secciones-modal-row">
                        {{-- Barra lateral: lista de secciones, reordenable arrastrando por el icono de flechas (primero en el DOM para que en móvil aparezca arriba; en escritorio se coloca a la derecha con order-md-2) --}}
                        <div class="col-md-4 order-md-2 h-100 d-flex flex-column">
                            @can(PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION)
                                <button type="button" class="btn btn-primary btn-sm mb-3 w-100"
                                    x-on:click="$wire.crearSeccionVacia().then(() => { if (window.innerWidth < 768) { $nextTick(() => document.getElementById('seccion-detalle-inicio')?.scrollIntoView({ behavior: 'smooth', block: 'start' })) } })">
                                    <i class="fa-solid fa-plus me-1" aria-hidden="true"></i>
                                    {{ trans('actions.create') }}
                                </button>
                            @endcan

                            @if ($this->secciones->isEmpty())
                                <p class="text-muted mb-0">{{ trans('fields.usuarios_cvs.secciones.sin_secciones') }}</p>
                            @else
                                {{-- Al soltar, x-sort ya ha reordenado el DOM y llama a este callback. Leemos el data-ulid de cada
                                fila en su nuevo orden visual y lo enviamos a reordenarSecciones() para que el servidor recalcule el orden en BD --}}
                                <div class="list-group secciones-modal-lista gap-2 overflow-auto flex-grow-1"
                                    x-sort="$wire.reordenarSecciones(Array.from($el.children).map(fila => fila.dataset.ulid))">
                                    @foreach ($this->secciones as $seccion)
                                        @php
                                            // Mientras se edita la sección seleccionada, su fila refleja el título en vivo desde la propia propiedad del componente, sin persistir en BD
                                            $tituloFila = $seccion->ulid === $seccionSeleccionadaUlid ? $titulo : $seccion->titulo;
                                        @endphp
                                        <button type="button"
                                            class="list-group-item list-group-item-action rounded d-flex align-items-center gap-2 {{ $seccion->ulid === $seccionSeleccionadaUlid ? 'active' : '' }}"
                                            wire:key="seccion-fila-{{ $seccion->ulid }}" data-ulid="{{ $seccion->ulid }}"
                                            x-sort:item="'{{ $seccion->ulid }}'"
                                            x-on:click="$wire.seleccionarSeccion('{{ $seccion->ulid }}').then(() => { if (window.innerWidth < 768) { $nextTick(() => document.getElementById('seccion-detalle-inicio')?.scrollIntoView({ behavior: 'smooth', block: 'start' })) } })">
                                            <span class="text-truncate flex-grow-1">
                                                {{ $tituloFila !== '' ? $tituloFila : trans('fields.usuarios_cvs.secciones.sin_titulo') }}
                                            </span>
                                            <i class="fa-solid fa-up-down-left-right text-muted" x-sort:handle aria-hidden="true"
                                                title="{{ trans('actions.drag_reorder') }}"></i>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Hueco central: campos de la sección seleccionada --}}
                        <div id="seccion-detalle-inicio" class="col-md-8 order-md-1 h-100 overflow-auto">
                            @if ($this->secciones->isEmpty())
                                <div class="d-flex align-items-center justify-content-center h-100">
                                    <p class="text-muted mb-0">{{ trans('fields.usuarios_cvs.secciones.no_hay_secciones') }}</p>
                                </div>
                            @elseif ($this->seccionSeleccionada !== null)
                                @include('livewire.partials.usuario-cv-seccion-detalle', ['seccion' => $this->seccionSeleccionada])
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100">
                                    <p class="text-muted mb-0">{{ trans('fields.usuarios_cvs.secciones.seleccione_seccion') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de confirmación al cambiar de sección, crear una nueva o cerrar con cambios sin guardar --}}
    <div class="modal fade" id="modalSeccionesConfirmarDescarte" tabindex="-1"
        aria-labelledby="modalSeccionesConfirmarDescarteTitulo" aria-hidden="true" wire:ignore.self data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h6" id="modalSeccionesConfirmarDescarteTitulo">
                        {{ trans('fields.usuarios_cvs.modal.descartar_cambios_titulo') }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('actions.cancel') }}"></button>
                </div>
                <div class="modal-body">
                    {{ trans('fields.usuarios_cvs.modal.descartar_cambios_descripcion') }}
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" wire:click="confirmarDescarte">
                        {{ trans('actions.discard_changes') }}
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('actions.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Panel de vista previa del PDF: mostrado/ocultado por JS según el estado real del modal de secciones (livewire-bridge.js) --}}
    @can(PermissionHelper::USUARIOS_CVS_GENERAR_PDF_PERMISSION)
        @if ($this->cv !== null)
            @include('livewire.partials.usuario-cv-preview', ['cv' => $this->cv, 'usuario' => $usuario, 'panelId' => 'cvPreviewSecciones'])
        @endif
    @endcan
</div>
