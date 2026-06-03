<?php

declare(strict_types=1);

namespace App\View\Components\Panel;

use App\Contracts\OrdenacionEnum;
use App\Helpers\OrdenacionHelper;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SortHeader extends Component
{
    // Estado actual de ordenación parseado desde la URL
    public array $actual;

    // Dirección actual de esta columna (asc, desc o null si no está ordenada)
    public ?string $dirActual;

    // Posición de esta columna dentro del orden global según la URL (primero, segundo, ...), o null si no está ordenada
    public ?int $posicionOrden;

    // URL del próximo estado al hacer clic en la cabecera
    public string $url;

    // Icono de FontAwesome a mostrar
    public string $icono;

    // Clase CSS del icono según el estado actual
    public string $iconoClase;

    // Valor del atributo aria-sort
    public string $ariaSort;

    // Texto del atributo aria-label que describe la acción del próximo clic
    public string $ariaLabel;

    /**
     * Calcula todo el estado visual y de navegación de la cabecera ordenable a partir de la columna y la URL actual.
     */
    public function __construct(
        public OrdenacionEnum $columna,
        public string $etiqueta,
        public string $clase = '',
    ) {
        // Recogemos el estado actual de ordenación parseando la cadena compacta "campo:dir?campo:dir" de la URL
        $this->actual = OrdenacionHelper::parseCadenaOrdenacion(request()->query('ordenacion'));

        $clave = $this->columna->value;
        $this->dirActual = $this->actual[$clave] ?? null;

        $this->posicionOrden = $this->calcularPosicionOrden($clave);
        $this->url = $this->calcularUrlSiguiente($clave);

        $this->calcularEstadoVisual();
    }

    /**
     * Devuelve la posición de la columna dentro del orden global según la URL (primero, segundo, ...), o null si no está ordenada.
     */
    private function calcularPosicionOrden(string $clave): ?int
    {
        if ($this->dirActual === null) {
            return null;
        }

        // Devuelvo la posición más 1 (el primero será 1, el segundo 2, ...)
        return array_search($clave, array_keys($this->actual), true) + 1;
    }

    /**
     * Construye la URL del próximo estado aplicando el ciclo asc → desc → fuera sobre esta columna.
     */
    private function calcularUrlSiguiente(string $clave): string
    {
        // Aplicamos el ciclo asc → desc → fuera, sin tocar otras columnas
        $siguiente = $this->actual;
        if ($this->dirActual === null) {
            $siguiente[$clave] = 'asc';
        } elseif ($this->dirActual === 'asc') {
            $siguiente[$clave] = 'desc';
        } else {
            unset($siguiente[$clave]);
        }

        // Construimos la URL manualmente para que ":" y "?" no aparezcan codificadas en la barra del navegador
        // Quitamos también "page" para que al cambiar la ordenación se vuelva siempre a la página 1
        $cadenaSiguiente = OrdenacionHelper::serializar($siguiente);
        $params = request()->query();
        unset($params['ordenacion'], $params['page']);

        $partes = [];
        if ($params !== []) {
            $partes[] = http_build_query($params);
        }
        if ($cadenaSiguiente !== null) {
            $partes[] = 'ordenacion=' . $cadenaSiguiente;
        }

        return request()->url() . ($partes !== [] ? '?' . implode('&', $partes) : '');
    }

    /**
     * Asigna icono, claseIcono, ariaSort y ariaLabel según la dirección actual de ordenación.
     */
    private function calcularEstadoVisual(): void
    {
        if ($this->dirActual === 'asc') {
            $this->icono = 'fa-arrow-up';
            $this->iconoClase = 'sort-header-icon-activo';
            $this->ariaSort = 'ascending';
            $this->ariaLabel = trans('fields.ordenacion.ordenar_descendente', ['columna' => $this->etiqueta]);

            return;
        }

        if ($this->dirActual === 'desc') {
            $this->icono = 'fa-arrow-down';
            $this->iconoClase = 'sort-header-icon-activo';
            $this->ariaSort = 'descending';
            $this->ariaLabel = trans('fields.ordenacion.quitar_ordenacion', ['columna' => $this->etiqueta]);

            return;
        }

        // Sin orden: mostramos un icono de flechas arriba/abajo como indicador de que la columna es ordenable
        $this->icono = 'fa-up-down';
        $this->iconoClase = 'sort-header-icon-inactivo';
        $this->ariaSort = 'none';
        $this->ariaLabel = trans('fields.ordenacion.ordenar_ascendente', ['columna' => $this->etiqueta]);
    }

    /**
     * Devuelve la vista del componente.
     */
    public function render(): View|Closure|string
    {
        return view('components.panel.sort-header');
    }
}
