<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\UsuarioOrdenacionEnum;
use App\Http\Requests\Concerns\PreparaOrdenacion;
use Illuminate\Foundation\Http\FormRequest;

class IndexUsuarioRequest extends FormRequest
{
    use PreparaOrdenacion;

    /**
     * Autoriza la petición exigiendo permiso de listado
     */
    public function authorize(): bool
    {
        // Comprobamos que el usuario autenticado puede listar usuarios
        return $this->user()?->can('usuarios_listado') ?? false;
    }

    /**
     * Indica al trait qué enum define las claves de ordenación válidas para este recurso
     */
    protected function ordenacionEnum(): string
    {
        return UsuarioOrdenacionEnum::class;
    }

    /**
     * Normaliza los parámetros de la URL antes de validar, los de la ordenación
     */
    protected function prepareForValidation(): void
    {
        $this->prepararOrdenacion();
    }

    /**
     * Reglas de validación
     */
    public function rules(): array
    {
        return $this->reglasOrdenacion();
    }

    /**
     * Devuelve los nombres traducidos de los campos para los mensajes de validación
     */
    public function attributes(): array
    {
        return $this->atributosOrdenacion();
    }
}
