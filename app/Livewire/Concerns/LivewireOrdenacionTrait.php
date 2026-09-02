<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Contracts\OrdenacionEnum;
use Livewire\Attributes\Locked;

/**
 * Trait para componentes Livewire de listado con columnas ordenables (equivalente a RequestOrdenacionTrait pero para acciones wire:click en vez de query strings).
 */
trait LivewireOrdenacionTrait
{
    /** Ordenación activa del listado, clave => dirección ('asc'/'desc'), preservando el orden de ordenación de columnas */
    #[Locked]
    public array $ordenacion = [];

    /**
     * Devuelve el enum por el que se ordena en el listado
     *
     * @return class-string<OrdenacionEnum>
     */
    abstract private function ordenacionEnum(): string;

    /**
     * Cambia el estado de ordenación de una columna aplicando el ciclo asc → desc → fuera, sin tocar la posición ni el estado de las demás columnas activas.
     */
    public function ordenarPorColumna(string $columna): void
    {
        $enum = $this->ordenacionEnum();

        // Revisamos si la clave de ordenación es correcta, utilizando el enum correspondiente del listado
        if ($enum::tryFrom($columna) === null) {
            return;
        }

        $actual = $this->ordenacion[$columna] ?? null;

        if ($actual === null) {
            $this->ordenacion[$columna] = 'asc';
        } elseif ($actual === 'asc') {
            $this->ordenacion[$columna] = 'desc';
        } else {
            unset($this->ordenacion[$columna]);
        }

        $this->resetPage();
    }
}
