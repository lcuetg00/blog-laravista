<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Usado en livewires, para hacer dispatch y que se muestre ne un toast en panel.js
 */
trait EmiteToastsTrait
{
    /** Emite un toast de éxito al navegador (evento 'toast' que muestra panel.js sin recargar la página). */
    public function messageSuccess(string $mensaje): void
    {
        $this->dispatch('toast', tipo: 'success', mensaje: $mensaje);
    }

    /** Emite un toast de error al navegador (evento 'toast' que muestra panel.js sin recargar la página). */
    public function messageError(string $mensaje): void
    {
        $this->dispatch('toast', tipo: 'error', mensaje: $mensaje);
    }
}
