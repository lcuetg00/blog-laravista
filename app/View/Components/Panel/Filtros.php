<?php

declare(strict_types=1);

namespace App\View\Components\Panel;

use App\Helpers\OrdenacionHelper;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Component;

class Filtros extends Component
{
    // Para saber si se está ordenando o filtrando
    public bool $filtrosEnUrl;
    public bool $ordenacionEnUrl;

    // Si el crud puede ser exportado
    public bool $exportarActivo;
    public bool $crearActivo;

    // Ordenación en url: "campo:dir?campo:dir"
    public ?string $ordenacionFormatoUrl;

    // URL del listado quitando los campos de filtro y la paginación, conservando la ordenación
    public string $urlBorrarFiltros;
    // URL del listado quitando la ordenación y la paginación, conservando los filtros
    public string $urlBorrarOrdenacion;

    /**
     * Recoge la configuración del bloque de filtros y calcula los flags y URLs derivados de la request actual.
     */
    public function __construct(
        public string $routeIndex,
        public array $camposFiltro,
        public ?string $routeCreate = null,
        public ?string $routeExport = null,
        public ?string $permisoCreate = null,
        public ?string $permisoExport = null,
    ) {
        // Determinamos si algún campo de filtro tiene valor en la URL
        $this->filtrosEnUrl = false;
        foreach ($camposFiltro as $campo) {
            if (request()->filled($campo)) {
                $this->filtrosEnUrl = true;
                // Lo encontramos, salimos del bucle
                break;
            }
        }

        $this->ordenacionEnUrl = request()->filled('ordenacion');
        $this->ordenacionFormatoUrl = OrdenacionHelper::serializar((array) request('ordenacion', []));

        // Construimos las URLs de limpieza quitando también "page" para resetear la paginación
        // Utilizando ... en el array de camposFiltro, sacam0os uno a uno los filtros que hay para tener la url sin ellos
        // A las url de borralo le quitamos la página directamente para volver a la primera página
        $this->urlBorrarFiltros = request()->fullUrlWithoutQuery([...$camposFiltro, 'page']);
        $this->urlBorrarOrdenacion = request()->fullUrlWithoutQuery(['ordenacion', 'page']);

        // Visibilidad de los botones de la cabecera teniendo en cuenta su permiso opcional
        $usuario = auth()->user();
        // Podemos usar lo botones si no existe el permiso o si el usuario puede acceder a ello
        $this->crearActivo = $routeCreate !== null && ($permisoCreate === null || $usuario?->can($permisoCreate));
        $this->exportarActivo = $routeExport !== null && ($permisoExport === null || $usuario?->can($permisoExport));
    }

    /**
     * Devuelve la vista del componente.
     */
    public function render(): Renderable
    {
        return view('components.panel.filtros');
    }
}
