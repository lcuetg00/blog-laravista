<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\PermissionHelper;
use App\Helpers\UsuarioHelper;
use App\Helpers\ValidacionHelper;
use App\Models\Usuario;
use App\Rules\MimeTypeImagenValido;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUsuarioRequest extends FormRequest
{
    /**
     * Autoriza la petición exigiendo permiso de edición y que el usuario activo pueda modificar al usuario objetivo (incluye la regla de autoedición).
     */
    public function authorize(): bool
    {
        /** @var Usuario|null $usuario */
        $usuario = $this->route('usuario');

        // Doble barrera: permiso global + jerarquía de roles + regla de autoedición (solo superadmin puede editarse a sí mismo)
        return $this->user()?->can(PermissionHelper::USUARIOS_EDITAR_PERMISSION) && UsuarioHelper::puedeModificarUsuario($this->user(), $usuario);
    }

    /**
     * Reglas de validación para la actualización de un usuario existente.
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
            'imagen' => [
                'nullable',
                'file',
                'mimes:' . implode(',', array_keys(ValidacionHelper::MIME_TYPES_IMAGEN)),
                new MimeTypeImagenValido,
                'max:' . ValidacionHelper::MAX_KB_IMAGEN,
            ],
        ];
    }

    /**
     * Devuelve los nombres traducidos de los campos para los mensajes de validación.
     */
    public function attributes(): array
    {
        return [
            'nombre' => trans('fields.input.nombre'),
            'primer_apellido' => trans('fields.input.primer_apellido'),
            'segundo_apellido' => trans('fields.input.segundo_apellido'),
            'email' => trans('fields.input.email'),
            'password' => trans('fields.input.password'),
            'imagen' => trans('fields.input.imagen'),
        ];
    }
}
