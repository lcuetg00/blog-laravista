<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Helpers\PermissionHelper;
use App\Helpers\ValidacionHelper;
use App\Models\Usuario;
use App\Models\UsuarioCv;
use App\Traits\EmiteToastsTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class UsuarioCvFormLivewire extends Component
{
    use EmiteToastsTrait;

    /** Usuario propietario del CV que se crea, edita o elimina */
    #[Locked]
    public Usuario $usuario;

    /** Ulid del CV en edición, o null si el modal de formulario está en modo creación */
    #[Locked]
    public ?string $cvUlid = null;

    /** Ulid del CV marcado para eliminar, o null si el modal de borrado está cerrado */
    #[Locked]
    public ?string $cvUlidEliminar = null;

    /** Nombre del CV */
    public string $nombre = '';

    /** Si el formulario tiene cambios en el nombre del CV que aún no se han guardado */
    #[Locked]
    public bool $hayCambiosSinGuardar = false;

    /**
     * Inicializa el componente con el usuario propietario del CV.
     */
    public function mount(Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }

    /**
     * Renderiza los modales de creación/edición y de borrado del CV.
     */
    public function render(): View
    {
        return view('livewire.usuario-cv-form-livewire');
    }

    /**
     * Prepara el modal de formulario en modo creación (sin ulid) o edición (precargando el nombre actual del CV).
     */
    #[On('abrir-modal-cv')]
    public function abrirFormulario(?string $ulid = null): void
    {
        $this->resetValidation();
        $this->cvUlid = $ulid;
        $this->hayCambiosSinGuardar = false;

        // En el caso en que creemos, ulid es null porque no existe
        if ($ulid === null) {
            $this->nombre = '';

            return;
        }

        $this->nombre = $this->usuario->usuariosCvs()->where('ulid', $ulid)->value('nombre') ?? '';
    }

    /**
     * Marca que hay cambios sin guardar al editar el nombre (Livewire llama a este hook tras cada actualización vía wire:model).
     */
    public function updatedNombre(): void
    {
        $this->hayCambiosSinGuardar = true;
    }

    /**
     * Pide cerrar el modal de formulario, mostrando antes la confirmación de descarte si hay cambios sin guardar.
     */
    public function intentarCerrarModal(): void
    {
        if ($this->hayCambiosSinGuardar) {
            $this->dispatch('cv-abrir-confirmar-descarte');

            return;
        }

        $this->dispatch('cv-cerrar');
    }

    /**
     * Confirma el descarte de los cambios sin guardar y cierra el modal de formulario.
     */
    public function confirmarDescarte(): void
    {
        $this->hayCambiosSinGuardar = false;
        $this->dispatch('cv-cerrar');
        $this->dispatch('cv-confirmar-descarte-ocultar');
    }

    /**
     * Reglas de validación del nombre del CV (coincide con el límite de la columna `nombre` en la tabla `usuarios_cvs`).
     */
    protected function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150', 'regex:' . ValidacionHelper::REGEX_TEXTO],
        ];
    }

    /**
     * Nombre traducido del campo para los mensajes de validación.
     */
    protected function validationAttributes(): array
    {
        return [
            'nombre' => trans('fields.usuarios_cvs.nombre'),
        ];
    }

    /**
     * Valida y guarda el nombre del CV (crea uno nuevo o actualiza el existente según el modo del modal) y avisa al listado para que se refresque.
     */
    public function guardar(): void
    {
        $permiso = $this->cvUlid === null
            ? PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION
            : PermissionHelper::USUARIOS_CVS_EDITAR_PERMISSION;

        abort_unless(Auth::user()?->can($permiso) ?? false, 403);

        $datos = $this->validate();

        try {
            DB::beginTransaction();

            if ($this->cvUlid === null) {
                $this->usuario->usuariosCvs()->create($datos);
            } else {
                $cv = $this->usuario->usuariosCvs()->where('ulid', $this->cvUlid)->firstOrFail();
                $cv->update($datos);
            }

            DB::commit();
        } catch (\Exception|\Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al guardar el CV del usuario', ['exception' => $e]);

            $this->messageError(trans('actions.generic_error'));

            return;
        }

        $this->hayCambiosSinGuardar = false;

        $mensajeAccion = $this->cvUlid === null ? 'actions.created' : 'actions.updated';
        $this->messageSuccess(trans_choice($mensajeAccion, UsuarioCv::CHOICE->value, ['modelo' => trans('fields.models.usuario_cv')]));
        $this->dispatch('cv-guardado');
    }

    /**
     * Abre el modal de borrado marcando qué CV se va a eliminar.
     */
    #[On('abrir-modal-eliminar-cv')]
    public function abrirEliminar(string $ulid): void
    {
        $this->cvUlidEliminar = $ulid;
    }

    /**
     * Devuelve el CV marcado para eliminar, o null si el modal de borrado está cerrado.
     */
    #[Computed]
    public function cvEliminar(): ?UsuarioCv
    {
        if ($this->cvUlidEliminar === null) {
            return null;
        }

        return $this->usuario->usuariosCvs()->where('ulid', $this->cvUlidEliminar)->first();
    }

    /**
     * Elimina el CV confirmado y avisa al listado para que se refresque.
     */
    public function eliminarCv(): void
    {
        abort_unless(Auth::user()?->can(PermissionHelper::USUARIOS_CVS_ELIMINAR_PERMISSION) ?? false, 403);

        $cv = $this->cvEliminar;
        if ($cv === null) {
            return;
        }

        try {
            DB::beginTransaction();

            $cv->delete();

            DB::commit();
        } catch (\Exception|\Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al eliminar el CV del usuario', ['exception' => $e]);

            $this->messageError(trans('actions.generic_error'));

            return;
        }

        $this->messageSuccess(trans_choice('actions.deleted', UsuarioCv::CHOICE->value, ['modelo' => trans('fields.models.usuario_cv')]));
        $this->dispatch('cv-eliminado');
    }
}
