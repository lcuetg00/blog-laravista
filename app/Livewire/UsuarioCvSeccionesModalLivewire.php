<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\UsuarioCvSeccionAccionPendienteEnum;
use App\Helpers\PermissionHelper;
use App\Helpers\ValidacionHelper;
use App\Models\Usuario;
use App\Models\UsuarioCv;
use App\Models\UsuarioCvSeccion;
use App\Rules\MimeTypeImagenValido;
use App\Traits\EmiteToastsTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class UsuarioCvSeccionesModalLivewire extends Component
{
    use EmiteToastsTrait;
    use WithFileUploads;

    /** Usuario propietario del CV cuyas secciones se gestionan */
    #[Locked]
    public Usuario $usuario;

    /** Ulid del CV cuyas secciones se gestionan, o null si el modal está cerrado */
    #[Locked]
    public ?string $cvUlid = null;

    /** Ulid de la sección seleccionada (mostrada en el hueco central), null = ninguna seleccionada */
    #[Locked]
    public ?string $seccionSeleccionadaUlid = null;

    /** Título editable de la sección seleccionada */
    public string $titulo = '';

    /** Descripción editable de la sección seleccionada (admite HTML escrito a mano) */
    public string $descripcion = '';

    /** Imágenes nuevas pendientes de subir a la galería de la sección seleccionada */
    public array $galeriaNuevas = [];

    /** Si hay cambios en el formulario de la sección seleccionada que aún no se han guardado */
    #[Locked]
    public bool $hayCambiosSinGuardar = false;

    /** Acción que quedó pendiente de confirmar por descartar cambios, o null si no hay ninguna */
    #[Locked]
    public ?UsuarioCvSeccionAccionPendienteEnum $accionPendiente = null;

    /** Ulid destino de la acción pendiente cuando es seleccionar una sección, o null en el resto de casos */
    #[Locked]
    public ?string $accionPendienteUlid = null;

    /**
     * Inicializa el componente con el usuario propietario del CV.
     */
    public function mount(Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }

    /**
     * Renderiza el modal con la barra lateral de secciones y el formulario de la seleccionada.
     */
    public function render(): View
    {
        return view('livewire.usuario-cv-secciones-modal-livewire');
    }

    /**
     * Abre el modal mostrando las secciones del CV indicado, seleccionando automáticamente la primera por orden (si tiene alguna).
     */
    #[On('abrir-modal-secciones')]
    public function abrir(string $ulid): void
    {
        $this->cvUlid = $ulid;
        unset($this->cv, $this->secciones);

        $this->seccionSeleccionadaUlid = $this->secciones->first()?->ulid;
        unset($this->seccionSeleccionada);

        $this->resetAccionPendiente();
        $this->cargarSeccionEnFormulario($this->seccionSeleccionada);
    }

    /**
     * Marca que hay cambios sin guardar al editar el título (Livewire llama a este hook tras cada actualización vía wire:model).
     */
    public function updatedTitulo(): void
    {
        $this->hayCambiosSinGuardar = true;
    }

    /**
     * Marca que hay cambios sin guardar al editar la descripción.
     */
    public function updatedDescripcion(): void
    {
        $this->hayCambiosSinGuardar = true;
    }

    /**
     * Marca que hay cambios sin guardar en cuanto se seleccionan imágenes nuevas para la galería.
     */
    public function updatedGaleriaNuevas(): void
    {
        $this->hayCambiosSinGuardar = true;
    }

    /**
     * Selecciona una sección para mostrar sus campos en el hueco central, pidiendo confirmación antes si hay cambios sin guardar.
     */
    public function seleccionarSeccion(string $ulid): void
    {
        if ($this->hayCambiosSinGuardar) {
            $this->accionPendiente = UsuarioCvSeccionAccionPendienteEnum::SELECCIONAR_SECCION;
            $this->accionPendienteUlid = $ulid;
            $this->dispatch('secciones-abrir-confirmar-descarte');

            return;
        }

        $this->seccionSeleccionadaUlid = $ulid;
        unset($this->seccionSeleccionada);
        $this->cargarSeccionEnFormulario($this->seccionSeleccionada);
    }

    /**
     * Pide cerrar el modal, mostrando antes la confirmación de descarte si hay cambios sin guardar.
     */
    public function intentarCerrarModal(): void
    {
        if ($this->hayCambiosSinGuardar) {
            $this->accionPendiente = UsuarioCvSeccionAccionPendienteEnum::CERRAR_MODAL;
            $this->dispatch('secciones-abrir-confirmar-descarte');

            return;
        }

        $this->dispatch('secciones-cerrar');
    }

    /**
     * Confirma el descarte de los cambios sin guardar y ejecuta la acción que había quedado pendiente.
     */
    public function confirmarDescarte(): void
    {
        $this->hayCambiosSinGuardar = false;

        match ($this->accionPendiente) {
            UsuarioCvSeccionAccionPendienteEnum::CERRAR_MODAL => $this->dispatch('secciones-cerrar'),
            UsuarioCvSeccionAccionPendienteEnum::CREAR_SECCION => $this->crearSeccionVacia(),
            UsuarioCvSeccionAccionPendienteEnum::SELECCIONAR_SECCION => $this->seleccionarSeccion((string) $this->accionPendienteUlid),
            null => null,
        };

        $this->resetAccionPendiente();
        $this->dispatch('secciones-confirmar-descarte-ocultar');
    }

    /**
     * Limpia la acción que había quedado pendiente de confirmar, sin ejecutar ninguna.
     */
    private function resetAccionPendiente(): void
    {
        $this->accionPendiente = null;
        $this->accionPendienteUlid = null;
    }

    /**
     * Invalida la caché de secciones/sección seleccionada para que se recalcule desde BD, limpiando además la selección si se indica.
     */
    private function invalidarSecciones(bool $limpiarSeleccion = false): void
    {
        if ($limpiarSeleccion) {
            $this->seccionSeleccionadaUlid = null;
        }

        unset($this->secciones, $this->seccionSeleccionada);
    }

    /**
     * Carga en las propiedades editables los datos de la sección indicada y limpia el estado de edición previo.
     */
    private function cargarSeccionEnFormulario(?UsuarioCvSeccion $seccion): void
    {
        $this->titulo = $seccion?->titulo ?? '';
        $this->descripcion = (string) ($seccion?->descripcion ?? '');
        $this->reset('galeriaNuevas');
        $this->resetValidation();
        $this->hayCambiosSinGuardar = false;
    }

    /**
     * Devuelve el CV cuyas secciones se están gestionando, o null si el modal está cerrado.
     */
    #[Computed]
    public function cv(): ?UsuarioCv
    {
        if ($this->cvUlid === null) {
            return null;
        }

        return $this->usuario->usuariosCvs()->where('ulid', $this->cvUlid)->first();
    }

    /**
     * Devuelve todas las secciones del CV seleccionado, ordenadas por su posición.
     */
    #[Computed]
    public function secciones(): Collection
    {
        return $this->cv?->secciones()->get() ?? new Collection;
    }

    /**
     * Devuelve la sección actualmente seleccionada para mostrar en el hueco central, o null si no hay ninguna.
     */
    #[Computed]
    public function seccionSeleccionada(): ?UsuarioCvSeccion
    {
        if ($this->seccionSeleccionadaUlid === null) {
            return null;
        }

        return $this->secciones->firstWhere('ulid', $this->seccionSeleccionadaUlid);
    }

    /**
     * Crea una sección vacía al final del CV y la selecciona para editarla de inmediato, pidiendo confirmación antes si hay cambios sin guardar.
     */
    public function crearSeccionVacia(): void
    {
        abort_unless(Auth::user()?->can(PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION) ?? false, 403);

        if ($this->hayCambiosSinGuardar) {
            $this->accionPendiente = UsuarioCvSeccionAccionPendienteEnum::CREAR_SECCION;
            $this->dispatch('secciones-abrir-confirmar-descarte');

            return;
        }

        $cv = $this->cv;
        if ($cv === null) {
            return;
        }

        try {
            DB::beginTransaction();

            $siguienteOrden = ((int) $cv->secciones()->max('orden')) + 1;
            $seccion = $cv->secciones()->create([
                'titulo' => '',
                'descripcion' => null,
                'orden' => $siguienteOrden,
            ]);

            DB::commit();
        } catch (\Exception|\Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al crear la sección del CV', ['exception' => $e]);

            $this->messageError(trans('actions.generic_error'));

            return;
        }

        $this->seccionSeleccionadaUlid = $seccion->ulid;
        $this->invalidarSecciones();
        $this->cargarSeccionEnFormulario($this->seccionSeleccionada);

        $this->dispatch('secciones-actualizadas');
    }

    /**
     * Reordena las secciones del CV a partir del array completo de ulids en su nuevo orden (calculado en el navegador leyendo el DOM tras el arrastre), desplazando primero el orden fuera de rango para no chocar con la restricción de unicidad.
     */
    public function reordenarSecciones(array $ulidsEnOrden): void
    {
        abort_unless(Auth::user()?->can(PermissionHelper::USUARIOS_CVS_EDITAR_PERMISSION) ?? false, 403);

        $cv = $this->cv;
        if ($cv === null) {
            return;
        }

        // Solo aceptamos el array si contiene exactamente los mismos ulids que tiene el CV, sin añadidos ni huecos
        $ulidsActuales = $cv->secciones()->pluck('ulid')->values()->all();
        $ulids = array_values(array_intersect($ulidsEnOrden, $ulidsActuales));
        if (count($ulids) !== count($ulidsActuales)) {
            return;
        }

        try {
            DB::beginTransaction();

            // 1ª pasada: desplazamos todos los 'orden' fuera de rango para no chocar con unique(usuario_cv_id, orden)
            UsuarioCvSeccion::where('usuario_cv_id', $cv->id)->update(['orden' => DB::raw('orden + 1000')]);

            // 2ª pasada: asignamos el orden definitivo 0..N-1 según la nueva secuencia
            foreach ($ulids as $indice => $ulid) {
                UsuarioCvSeccion::where('usuario_cv_id', $cv->id)->where('ulid', $ulid)->update(['orden' => $indice]);
            }

            DB::commit();
        } catch (\Exception|\Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al reordenar las secciones del CV', ['exception' => $e]);

            $this->messageError(trans('actions.generic_error'));

            return;
        }

        unset($this->secciones);
    }

    /**
     * Reglas de validación del formulario de la sección: descripción sin la regex de texto del proyecto (que prohíbe HTML), a propósito.
     */
    protected function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255', 'regex:' . ValidacionHelper::REGEX_TEXTO],
            'descripcion' => ['nullable', 'string', 'max:20000'],
            'galeriaNuevas.*' => [
                'nullable',
                'file',
                'mimes:' . implode(',', array_keys(ValidacionHelper::MIME_TYPES_IMAGEN)),
                new MimeTypeImagenValido,
                'max:' . ValidacionHelper::MAX_KB_IMAGEN,
            ],
        ];
    }

    /**
     * Valida y guarda el título/descripción de la sección seleccionada y sube las imágenes nuevas de la galería.
     */
    public function guardar(): void
    {
        abort_unless(Auth::user()?->can(PermissionHelper::USUARIOS_CVS_EDITAR_PERMISSION) ?? false, 403);

        $seccion = $this->seccionSeleccionada;
        if ($seccion === null) {
            return;
        }

        $datos = $this->validate();

        try {
            DB::beginTransaction();

            $seccion->update([
                'titulo' => $datos['titulo'],
                'descripcion' => $datos['descripcion'],
            ]);

            foreach ($this->galeriaNuevas as $fichero) {
                if ($fichero instanceof TemporaryUploadedFile) {
                    $seccion->addMedia($fichero->getRealPath())
                        ->usingFileName($fichero->getClientOriginalName())
                        ->toMediaCollection(UsuarioCvSeccion::MEDIA_COLLECTION_GALLERY);
                }
            }

            DB::commit();
        } catch (\Exception|\Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al guardar la sección del CV', ['exception' => $e]);

            $this->messageError(trans('actions.generic_error'));

            return;
        }

        $this->reset('galeriaNuevas');
        $this->invalidarSecciones();
        $this->hayCambiosSinGuardar = false;

        $this->messageSuccess(trans_choice('actions.updated', UsuarioCvSeccion::CHOICE->value, ['modelo' => trans('fields.models.usuario_cv_seccion')]));
        $this->dispatch('secciones-actualizadas');
    }

    /**
     * Borra una imagen de la galería de la sección seleccionada.
     */
    public function borrarImagen(string $mediaUuid): void
    {
        abort_unless(Auth::user()?->can(PermissionHelper::USUARIOS_CVS_EDITAR_PERMISSION) ?? false, 403);

        $seccion = $this->seccionSeleccionada;
        if ($seccion === null) {
            return;
        }

        try {
            DB::beginTransaction();

            // Acotamos el media por su uuid dentro de los de la sección (defensa frente a uuids forjados)
            $seccion->media()->where('uuid', $mediaUuid)->first()?->delete();

            DB::commit();
        } catch (\Exception|\Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al borrar la imagen de la sección del CV', ['exception' => $e]);

            $this->messageError(trans('actions.generic_error'));

            return;
        }

        $this->invalidarSecciones();

        $this->messageSuccess(trans('fields.usuarios_cvs.secciones.imagenes.borrada'));
    }

    /**
     * Elimina (soft delete) la sección seleccionada y limpia la selección.
     */
    public function eliminar(): void
    {
        abort_unless(Auth::user()?->can(PermissionHelper::USUARIOS_CVS_ELIMINAR_PERMISSION) ?? false, 403);

        $seccion = $this->seccionSeleccionada;
        if ($seccion === null) {
            return;
        }

        try {
            DB::beginTransaction();

            $seccion->delete();

            DB::commit();
        } catch (\Exception|\Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al eliminar la sección del CV', ['exception' => $e]);

            $this->messageError(trans('actions.generic_error'));

            return;
        }

        $this->invalidarSecciones(limpiarSeleccion: true);
        $this->cargarSeccionEnFormulario(null);

        $this->messageSuccess(trans_choice('actions.deleted', UsuarioCvSeccion::CHOICE->value, ['modelo' => trans('fields.models.usuario_cv_seccion')]));
        $this->dispatch('secciones-actualizadas');
    }
}
