<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Enums\OrdenacionColumnaEnum;
use App\Helpers\OrdenacionHelper;
use Illuminate\Validation\Rule;

/**
 * Trait para FormRequests de listado: parsea la cadena "campo:dir?campo:dir" y filtra por las claves válidas del recurso.
 */
trait RequestOrdenacionTrait
{
    /**
     * Devuelve la clase del enum (BackedEnum + OrdenacionEnum) con las claves válidas para este recurso.
     */
    abstract protected function ordenacionEnum(): string;

    /**
     * Convierte la cadena de URL en array asociativo y descarta claves no declaradas en el enum del recurso.
     */
    protected function prepararOrdenacion(): void
    {
        // Convertimos la cadena de URL al array asociativo (clave URL → dirección)
        $ordenacion = OrdenacionHelper::parseCadenaOrdenacion($this->query('ordenacion'));

        $ordenacionEnum = $this->ordenacionEnum();

        // Nos quedamos solo con las claves declaradas en el enum del recurso para no romper el listado por una URL manipulada
        $clavesValidas = array_column($ordenacionEnum::cases(), 'value');
        // Recogemos en ordenación solamente las claves que han llegado
        $ordenacion = array_intersect_key($ordenacion, array_flip($clavesValidas));

        $this->merge(['ordenacion' => $ordenacion]);
    }

    /**
     * Reglas de validación comunes para el parámetro de ordenación.
     */
    protected function reglasOrdenacion(): array
    {
        return [
            // Siempre debe de llegar un array por cómo funcciona el trait
            'ordenacion' => ['array'],
            'ordenacion.*' => ['required', Rule::enum(OrdenacionColumnaEnum::class)],
        ];
    }

    /**
     * Devuelve los nombres traducidos del parámetro de ordenación para los mensajes de validación.
     */
    protected function atributosOrdenacion(): array
    {
        return [
            'ordenacion' => trans('fields.ordenacion.atributo'),
            'ordenacion.*' => trans('fields.ordenacion.atributo_direccion'),
        ];
    }
}
