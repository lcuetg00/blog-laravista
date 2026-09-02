<?php

declare(strict_types=1);

namespace App\View\Components\Panel;

use App\Contracts\OrdenacionEnum;
use App\Helpers\OrdenacionHelper;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Component;

/**
 * Cabecera de columna ordenable para listados Livewire con multiordenación (equivalente a OrdenacionColumna pero llamando a un método wire:click en vez de generar una URL).
 */
class OrdenacionColumnaLivewire extends Component
{
    // Dirección actual de esta columna (asc, desc o null si no está activa)
    public ?string $dirActual;

    // Posición de esta columna dentro del orden global recibido (primero, segundo, ...), o null si no está activa
    public ?int $posicionOrden;

    // Icono de FontAwesome a mostrar
    public string $icono;

    // Clase CSS del icono según el estado actual
    public string $iconoClase;

    // Valor del atributo aria-sort
    public string $ariaSort;

    // Texto del atributo aria-label que describe la acción del próximo clic
    public string $ariaLabel;

    /**
     * Calcula el estado visual (dirección, posición dentro de la multiordenación e icono) a partir del array de ordenación activo recibido del componente Livewire padre.
     */
    public function __construct(
        public OrdenacionEnum $columna,
        public string $etiqueta,
        public array $ordenacion,
        public string $metodo = 'ordenarPorColumna',
        public string $clase = '',
    ) {
        $clave = $this->columna->value;
        $this->dirActual = $this->ordenacion[$clave] ?? null;

        $this->posicionOrden = OrdenacionHelper::calcularPosicionOrden($this->ordenacion, $clave);

        // El helper calcula icono, clase, aria-sort y aria-label a partir de la dirección actual (compartido con OrdenacionColumna)
        ['icono' => $this->icono, 'iconoClase' => $this->iconoClase, 'ariaSort' => $this->ariaSort, 'ariaLabel' => $this->ariaLabel]
            = OrdenacionHelper::calcularEstadoVisual($this->dirActual, $this->etiqueta);
    }

    /**
     * Devuelve la vista del componente.
     */
    public function render(): Renderable
    {
        return view('components.panel.ordenacion-columna-livewire');
    }
}
