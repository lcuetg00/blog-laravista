<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Helpers\PermissionHelper;
use App\Helpers\ValidacionHelper;
use App\Models\Usuario;
use App\Models\UsuarioCv;
use App\Models\UsuarioCvSeccion;
use App\Traits\EmiteToastsTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class UsuarioCvDuplicarModalLivewire extends Component
{
    use EmiteToastsTrait;

    /** Usuario propietario del CV que se duplica */
    #[Locked]
    public Usuario $usuario;

    /** Ulid del CV origen a duplicar, o null si el modal está cerrado */
    #[Locked]
    public ?string $cvUlid = null;

    /** Nombre nuevo introducido para la copia (rellena tanto "nombre" como "nombre_archivo" del CV duplicado) */
    public string $nombre = '';

    /**
     * Inicializa el componente con el usuario propietario del CV.
     */
    public function mount(Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }

    /**
     * Renderiza el modal de duplicación con el nombre nuevo sugerido para la copia.
     */
    public function render(): View
    {
        return view('livewire.usuario-cv-duplicar-modal-livewire');
    }

    /**
     * Abre el modal precargando un nombre sugerido a partir del CV origen indicado.
     */
    #[On('abrir-modal-duplicar-cv')]
    public function abrirFormulario(string $ulid): void
    {
        $this->resetValidation();
        $this->cvUlid = $ulid;

        $cv = $this->usuario->usuariosCvs()->where('ulid', $ulid)->first(['nombre']);
        $this->nombre = $cv !== null
            ? trans('fields.usuarios_cvs.modal.duplicar_nombre_sugerido', ['nombre' => $cv->nombre])
            : '';
    }

    /**
     * Reglas de validación del nombre nuevo de la copia (mismo límite que la columna "nombre" de usuarios_cvs).
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
     * Valida el nombre nuevo y duplica el CV origen con todas sus secciones y la galería de imágenes de cada una.
     */
    public function duplicar(): void
    {
        abort_unless(Auth::user()?->can(PermissionHelper::USUARIOS_CVS_CREAR_PERMISSION) ?? false, 403);

        $datos = $this->validate();

        try {
            DB::beginTransaction();

            $original = $this->usuario->usuariosCvs()->where('ulid', $this->cvUlid)->with('secciones.media')->firstOrFail();

            // Replicamos el modelo antiguo, le cambiamos el nombre y los guardamos (añade uno un nuevo id)
            $nuevoCv = $original->replicate(['ulid']);
            $nuevoCv->nombre = $datos['nombre'];
            $nuevoCv->nombre_archivo = $datos['nombre'];
            $nuevoCv->save();

            // Duplica cada sección
            foreach ($original->secciones as $seccion) {
                $nuevaSeccion = $seccion->replicate(['ulid']);
                $nuevaSeccion->usuario_cv_id = $nuevoCv->id;
                $nuevaSeccion->save();

                foreach ($seccion->getMedia(UsuarioCvSeccion::MEDIA_COLLECTION_GALLERY) as $media) {
                    $media->copy($nuevaSeccion, UsuarioCvSeccion::MEDIA_COLLECTION_GALLERY);
                }
            }

            DB::commit();
        } catch (\Exception|\Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al duplicar el CV del usuario', ['exception' => $e]);

            $this->messageError(trans('actions.generic_error'));

            return;
        }

        $this->messageSuccess(trans_choice('actions.created', UsuarioCv::CHOICE->value, ['modelo' => trans('fields.models.usuario_cv')]));
        $this->dispatch('cv-guardado');
        $this->dispatch('duplicar-cv-cerrar');
    }
}
