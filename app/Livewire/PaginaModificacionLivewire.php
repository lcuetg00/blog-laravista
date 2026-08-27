<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Helpers\PermissionHelper;
use App\Helpers\ValidacionHelper;
use App\Models\Pagina;
use App\Services\ConfiguracionService;
use App\Traits\EmiteToastsTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class PaginaModificacionLivewire extends Component
{
    use EmiteToastsTrait;

    /** Página que se está editando (se rehidrata por su clave entre peticiones de Livewire). */
    public Pagina $pagina;

    /** Título de la página por idioma (locale => texto), enlazado a las pestañas del formulario. */
    public array $titulo = [];

    /** Descripción de la página por idioma (locale => texto), enlazada a las pestañas del formulario. */
    public array $descripcion = [];

    /** Estado activo de la página (no editable en páginas no desactivables como el home). */
    public bool $activo = false;

    /**
     * Inicializa las propiedades del formulario con las traducciones actuales de la página y su estado activo.
     */
    public function mount(Pagina $pagina): void
    {
        $this->pagina = $pagina;

        // Rellenamos cada idioma soportado con su traducción actual (sin fallback) para no mezclar idiomas en las pestañas
        foreach (LaravelLocalization::getSupportedLanguagesKeys() as $idioma) {
            $this->titulo[$idioma] = $pagina->getTranslation('titulo', $idioma, false);
            $this->descripcion[$idioma] = $pagina->getTranslation('descripcion', $idioma, false);
        }

        $this->activo = $pagina->activo;
    }

    /**
     * Renderiza el formulario de datos de la página.
     */
    public function render(): View
    {
        return view('livewire.pagina-modificacion-livewire');
    }

    /**
     * Reglas de validación: el título es obligatorio en todos los idiomas y la descripción opcional (la clave es inmutable y no se valida).
     */
    protected function rules(): array
    {
        $reglas = ['activo' => ['required', 'boolean']];

        // Una regla por cada idioma disponible para no repetir .es/.en/.ja a mano: al añadir o quitar idioma soportado no hay que tocar el código
        foreach (LaravelLocalization::getSupportedLanguagesKeys() as $idioma) {
            $reglas['titulo.' . $idioma] = ['required', 'string', 'max:255', 'regex:' . ValidacionHelper::REGEX_TEXTO];
            $reglas['descripcion.' . $idioma] = ['nullable', 'string', 'regex:' . ValidacionHelper::REGEX_TEXTO];
        }

        return $reglas;
    }

    /** Nombres traducidos de los campos (sufijados por idioma) para los mensajes de validación. */
    protected function validationAttributes(): array
    {
        $atributos = ['activo' => trans('fields.input.activo')];

        foreach (LaravelLocalization::getSupportedLanguagesKeys() as $idioma) {
            $atributos['titulo.' . $idioma] = trans('fields.input.titulo') . ' (' . $idioma . ')';
            $atributos['descripcion.' . $idioma] = trans('fields.input.descripcion') . ' (' . $idioma . ')';
        }

        return $atributos;
    }

    /**
     * Formatea los strings del formulario con el helper, pasando a null los campos en blanco antes de validar (Livewire no aplica ConvertEmptyStringsToNull a sus propiedades).
     */
    private function formatearCampos(): void
    {
        $this->titulo = ValidacionHelper::nullificarVacios($this->titulo);
        $this->descripcion = ValidacionHelper::nullificarVacios($this->descripcion);
    }

    /**
     * Valida y guarda los datos de la página, avisa con un toast de éxito y solicita recargar la vista previa.
     */
    public function update(): void
    {
        // Mismo permiso que el panel: editar una página exige el permiso de edición
        abort_unless(Auth::user()?->can(PermissionHelper::PAGINAS_EDITAR_PERMISSION) ?? false, 403);

        // Las páginas no desactivables (el home) permanecen siempre activas, ignorando el valor del formulario
        if (!$this->pagina->es_desactivable) {
            $this->activo = true;
        }

        // Antes de validar, formateo los campos, uno de los cammbios es hacer que "" sea null (ocupa menos en base de datos)
        $this->formatearCampos();

        $datos = $this->validate();

        try {
            DB::beginTransaction();

            $this->pagina->update($datos);

            DB::commit();
        } catch (\Exception|\Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al actualizar la página', ['exception' => $e]);

            $this->messageError(trans('actions.generic_error'));

            return;
        }

        // Olvidamos la caché de páginas activas para que el menú público refleje al instante la activación o desactivación (lo recargue de base de datos)
        Cache::forget(ConfiguracionService::CACHE_PAGINAS_ACTIVAS);

        // Avisamos al usuario y pedimos refrescar la vista previa con el nuevo título
        $this->messageSuccess(trans_choice('actions.updated', Pagina::CHOICE->value, ['modelo' => trans('fields.models.pagina')]));
        // Llamamos a recargar la vista del iframe
        $this->dispatch('recargar-preview', titulo: $this->pagina->getTranslation('titulo', app()->getLocale(), false));
    }
}
