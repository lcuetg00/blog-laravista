<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUsuarioRequest extends FormRequest
{
    /**
     * Autoriza la petición exigiendo permiso de edición (doble barrera junto al middleware de ruta).
     */
    public function authorize(): bool
    {
        // Comprobamos que el usuario autenticado puede editar usuarios
        return $this->user()?->can('usuarios_editar') ?? false;
    }

    /**
     * Reglas de validación para la actualización de un usuario existente.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        // Obtenemos el usuario vinculado a la ruta para excluirlo de la regla unique del email
        /** @var Usuario|null $usuario */
        $usuario = $this->route('usuario');

        return [
            'nombre' => ['required', 'string', 'max:70'],
            'primer_apellido' => ['required', 'string', 'max:70'],
            'segundo_apellido' => ['nullable', 'string', 'max:70'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('usuarios', 'email')->ignore($usuario?->getKey()),
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
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
