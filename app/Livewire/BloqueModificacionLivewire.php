<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Helpers\BloqueHelper;
use App\Helpers\PermissionHelper;
use App\Helpers\ValidacionHelper;
use App\Models\Bloque;
use App\Traits\EmiteToastsTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class BloqueModificacionLivewire extends Component
{
    use EmiteToastsTrait;
    use WithFileUploads;

    /** Bloque que se está editando (se rehidrata por su clave entre peticiones de Livewire) */
    public Bloque $bloque;

    /** Valores traducibles del bloque por idioma ({es:{...}, en:{...}}): escalares en su forma final y repetidores como JSON crudo de su textarea  */
    public array $campos = [];

    /** Título traducible editable de los items existentes del carrusel, indexado por uuid de media e idioma */
    public array $etiquetas = [];

    /**  Imágenes únicas recién subidas que reemplazarán a la actual, indexadas por la clave del campo  */
    public array $imagenes = [];

    /** Imágenes nuevas a añadir a la galería (bloques con campo de tipo galería) */
    public array $galeriaNuevas = [];

    /** Texto alternativo editable de las imágenes existentes de la galería, indexado por uuid de media e idioma */
    public array $alts = [];

    /** Filas nuevas del carrusel pendientes de subir, cada una con su fichero y su título por idioma ([['imagen' => file, 'etiqueta' => [es, en, ja]]]) */
    public array $nuevosItems = [];

    /** Mantiene el acordeón del bloque abierto tras cualquier interacción con Livewire (evita que se cierre de golpe al subir una imagen o al validar) */
    public bool $abierto = false;

    /**
     * Inicializa los valores editables a partir del contenido actual del bloque según la metadata de su tipo.
     */
    public function mount(Bloque $bloque): void
    {
        $this->bloque = $bloque;

        // Traducciones almacenadas tal cual las guarda Translatable: un bloque de campos por idioma ({es:{...}, en:{...}})
        $traducciones = $bloque->getTranslations('campos');
        $idiomas = LaravelLocalization::getSupportedLanguagesKeys();

        foreach ($bloque->tipo->campos() as $clave => $definicion) {
            $tipo = $definicion['tipo'];

            // Los campos de imagen no se precargan en 'campos': se gestionan con sus propias propiedades de ficheros y los medios existentes se leen de la BD
            if (BloqueHelper::esCampoMedia($tipo)) {
                if ($tipo === 'galeria') {
                    $this->cargarAltsGaleria($definicion['coleccion'] ?? null);
                } elseif ($tipo === 'galeria_etiquetada') {
                    $this->cargarEtiquetasCarrusel($definicion['coleccion'] ?? null);
                }

                continue;
            }

            // Traducibles  un valor por idioma
            foreach ($idiomas as $idioma) {
                $valor = $traducciones[$idioma][$clave] ?? null;

                if (BloqueHelper::esRepetidor($tipo)) {
                    $this->campos[$idioma][$clave] = json_encode($valor ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } elseif ($tipo === 'enum_local') {
                    // El select arranca en la primera opción si no hay valor guardado
                    $this->campos[$idioma][$clave] = $valor ?? ($definicion['valores'][0] ?? null);
                } else {
                    $this->campos[$idioma][$clave] = $valor;
                }
            }
        }
    }

    /**
     * Renderiza el ítem de acordeón con el formulario de edición del bloque.
     */
    public function render(): View
    {
        return view('livewire.bloque-modificacion-livewire');
    }

    /**
     * Carga el alt traducible actual de cada imagen de la galería para poder editarlo en el formulario.
     */
    private function cargarAltsGaleria(?string $coleccion): void
    {
        foreach ($this->bloque->getMedia($coleccion) as $media) {
            foreach (LaravelLocalization::getSupportedLanguagesKeys() as $idioma) {
                $this->alts[$media->uuid][$idioma] = data_get($media->getCustomProperty('alt'), $idioma, '');
            }
        }
    }

    /**
     * Carga el título traducible actual de cada item existente del carrusel para poder editarlo en el formulario.
     */
    private function cargarEtiquetasCarrusel(?string $coleccion): void
    {
        foreach ($this->bloque->getMedia($coleccion) as $media) {
            foreach (LaravelLocalization::getSupportedLanguagesKeys() as $idioma) {
                $this->etiquetas[$media->uuid][$idioma] = data_get($media->getCustomProperty('etiqueta'), $idioma, '');
            }
        }
    }

    /**
     * Añade una fila vacía (fichero + título por idioma) al repetidor de items nuevos del carrusel.
     */
    public function agregarItem(): void
    {
        $this->abierto = true;

        $etiqueta = array_fill_keys(LaravelLocalization::getSupportedLanguagesKeys(), '');
        $this->nuevosItems[] = ['imagen' => null, 'etiqueta' => $etiqueta];
    }

    /**
     * Quita una fila del repetidor de items nuevos del carrusel y reindexa el array para que las claves sigan siendo consecutivas.
     */
    public function quitarItem(int $indice): void
    {
        $this->abierto = true;

        unset($this->nuevosItems[$indice]);
        $this->nuevosItems = array_values($this->nuevosItems);
    }

    /**
     * Cualquier actualización reactiva (subir una imagen, etc.) deja el bloque abierto para no plegarlo de forma brusca.
     */
    public function updated(): void
    {
        $this->abierto = true;
    }

    /**
     * Refresca los medios del bloque desde la BD y recarga los alts/etiquetas editables (tras subir o borrar imágenes).
     */
    private function refrescarMedios(): void
    {
        $this->bloque->refresh();
        $this->alts = [];
        $this->etiquetas = [];

        foreach ($this->bloque->tipo->campos() as $definicion) {
            if (($definicion['tipo'] ?? null) === 'galeria') {
                $this->cargarAltsGaleria($definicion['coleccion'] ?? null);
            } elseif (($definicion['tipo'] ?? null) === 'galeria_etiquetada') {
                $this->cargarEtiquetasCarrusel($definicion['coleccion'] ?? null);
            }
        }
    }

    /**
     * Borra una imagen del bloque (confirmada en el modal compartido del bloque), avisa con un toast y refresca la galería y la vista previa.
     */
    public function borrarImagen(string $mediaUuid): void
    {
        // Borrar una imagen del bloque es editar su página: exige el mismo permiso de edición
        abort_unless(Auth::user()?->can(PermissionHelper::PAGINAS_EDITAR_PERMISSION) ?? false, 403);

        $this->abierto = true;

        try {
            DB::beginTransaction();

            // Acotamos el media por su uuid dentro de los del bloque (defensa frente a uuids forjados)
            $this->bloque->media()->where('uuid', $mediaUuid)->first()?->delete();

            DB::commit();
        } catch (\Exception|\Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al borrar la imagen del bloque', ['exception' => $e]);

            $this->messageError(trans('actions.generic_error'));

            return;
        }

        $this->refrescarMedios();

        $this->messageSuccess(trans('fields.bloques.imagenes.borrada'));
        $this->dispatch('recargar-preview');
    }

    /**
     * Valida los campos del bloque, guarda su contenido e imágenes, avisa con un toast y solicita recargar la vista previa.
     */
    public function update(): void
    {
        // Editar un bloque es editar su página: exige el mismo permiso de edición
        abort_unless(Auth::user()?->can(PermissionHelper::PAGINAS_EDITAR_PERMISSION) ?? false, 403);

        $this->abierto = true;

        // Limpiamos los errores previos: al validar con Validator::make manual, Livewire no resetea el error bag por nosotros (solo lo hace $this->validate())
        $this->resetValidation();

        // Validamos sobre una copia normalizada (repetidores ya decodificados a array) junto a los ficheros subidos
        $datos = [
            'campos' => $this->camposNormalizados(),
            'imagenes' => $this->imagenes,
            'galeriaNuevas' => $this->galeriaNuevas,
            'alts' => $this->alts,
            'etiquetas' => $this->etiquetas,
            'nuevosItems' => $this->nuevosItems,
        ];

        $validados = Validator::make(
            $datos,
            BloqueHelper::reglas($this->bloque),
            BloqueHelper::mensajes($this->bloque),
            BloqueHelper::atributos($this->bloque),
        )->validate();

        // Montamos el almacenamiento de Translatable: cada idioma con sus campos traducibles
        $almacen = [];
        foreach (LaravelLocalization::getSupportedLanguagesKeys() as $idioma) {
            $almacen[$idioma] = $validados['campos'][$idioma] ?? [];
        }

        try {
            DB::beginTransaction();

            $this->bloque->update(['campos' => $almacen]);

            $this->guardarImagenes();

            DB::commit();
        } catch (\Exception|\Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al actualizar el bloque', ['exception' => $e]);

            $this->messageError(trans('actions.generic_error'));

            return;
        }

        // Limpiamos los ficheros ya procesados y refrescamos para que la galería y el carrusel muestren los nuevos medios
        $this->reset('imagenes', 'galeriaNuevas', 'nuevosItems');
        $this->refrescarMedios();

        $this->messageSuccess(trans('fields.bloques.guardado'));
        $this->dispatch('recargar-preview');
    }

    /**
     * Devuelve una copia de los campos traducibles por idioma lista para validar: repetidores decodificados a array y cadenas vacías de escalares como null.
     */
    private function camposNormalizados(): array
    {
        $campos = $this->campos;

        foreach ($this->bloque->tipo->campos() as $clave => $definicion) {
            $tipo = $definicion['tipo'];

            // Solo normalizamos los campos que viven en 'campos' (la media viaja en sus propias propiedades de ficheros)
            if (BloqueHelper::esCampoMedia($tipo)) {
                continue;
            }

            foreach (array_keys($campos) as $idioma) {
                $valor = $campos[$idioma][$clave] ?? null;

                // Los repetidores llegan como JSON crudo del textarea: lo decodificamos a array para poder validar su estructura
                if (BloqueHelper::esRepetidor($tipo)) {
                    if (is_string($valor) && json_validate($valor)) {
                        $campos[$idioma][$clave] = json_decode($valor, true);
                    }

                    continue;
                }

                // Escalares: las cadenas vacías pasan a null para que 'nullable' no choque con la regex (y 'required' siga fallando)
                $campos[$idioma][$clave] = ValidacionHelper::nullificarVacios($valor);
            }
        }

        return $campos;
    }

    /**
     * Sube las imágenes únicas y de galería nuevas y los items nuevos del carrusel, y actualiza el alt/etiqueta de los medios existentes.
     */
    private function guardarImagenes(): void
    {
        foreach ($this->bloque->tipo->campos() as $clave => $definicion) {
            $coleccion = $definicion['coleccion'] ?? null;

            // Imagen única (icono, imagen): el fichero nuevo de su clave reemplaza al anterior (colección singleFile)
            if ($definicion['tipo'] === 'imagen') {
                $fichero = $this->imagenes[$clave] ?? null;
                if ($fichero instanceof TemporaryUploadedFile) {
                    $this->bloque->addMedia($fichero->getRealPath())
                        ->usingFileName($fichero->getClientOriginalName())
                        ->toMediaCollection($coleccion);
                }

                continue;
            }

            // Galería: actualizamos el alt de las existentes y añadimos las nuevas
            if ($definicion['tipo'] === 'galeria') {
                $this->actualizarCustomProperty($coleccion, 'alt', $this->alts);

                foreach ($this->galeriaNuevas as $fichero) {
                    if (!$fichero instanceof TemporaryUploadedFile) {
                        continue;
                    }

                    $this->bloque->addMedia($fichero->getRealPath())
                        ->usingFileName($fichero->getClientOriginalName())
                        ->toMediaCollection($coleccion);
                }

                continue;
            }

            // Carrusel: actualizamos el título de los items existentes y subimos las filas nuevas con su título como custom property
            if ($definicion['tipo'] === 'galeria_etiquetada') {
                $this->actualizarCustomProperty($coleccion, 'etiqueta', $this->etiquetas);

                foreach ($this->nuevosItems as $item) {
                    $fichero = $item['imagen'] ?? null;
                    if (!$fichero instanceof TemporaryUploadedFile) {
                        continue;
                    }

                    $this->bloque->addMedia($fichero->getRealPath())
                        ->usingFileName($fichero->getClientOriginalName())
                        ->withCustomProperties(['etiqueta' => $item['etiqueta'] ?? []])
                        ->toMediaCollection($coleccion);
                }
            }
        }
    }

    /**
     * Vuelca el valor traducible editado de cada medio existente (alt o etiqueta) sobre su custom property, indexado por uuid de media.
     */
    private function actualizarCustomProperty(?string $coleccion, string $propiedad, array $valores): void
    {
        foreach ($this->bloque->getMedia($coleccion) as $media) {
            if (!array_key_exists($media->uuid, $valores)) {
                continue;
            }

            $media->setCustomProperty($propiedad, $valores[$media->uuid]);
            $media->save();
        }
    }
}
