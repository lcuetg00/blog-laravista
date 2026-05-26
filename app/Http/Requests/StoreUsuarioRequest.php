<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUsuarioRequest extends FormRequest
{
    /**
     * Autoriza la petición exigiendo permiso de creación (doble barrera junto al middleware de ruta).
     */
    public function authorize(): bool
    {
        // Comprobamos que el usuario autenticado puede crear usuarios
        return $this->user()?->can('usuarios_crear') ?? false;
    }

    /**
     * Reglas de validación para la creación de un usuario nuevo.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:70'],
            'primer_apellido' => ['required', 'string', 'max:70'],
            'segundo_apellido' => ['nullable', 'string', 'max:70'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:usuarios,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * Devuelve los nombres traducidos de los campos para los mensajes de validación.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nombre' => trans('fields.input.nombre'),
            'primer_apellido' => trans('fields.input.primer_apellido'),
            'segundo_apellido' => trans('fields.input.segundo_apellido'),
            'email' => trans('fields.input.email'),
            'password' => trans('fields.input.password'),
        ];
    }
}
