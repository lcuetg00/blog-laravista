<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\PermissionHelper;
use App\Helpers\ValidacionHelper;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Autoriza la petición exigiendo permiso de edición de roles.
     */
    public function authorize(): bool
    {
        // Comprobamos que el usuario autenticado puede editar roles
        return $this->user()?->can(PermissionHelper::ROLES_EDITAR_PERMISSION) ?? false;
    }

    /**
     * Reglas de validación para la actualización de un rol existente.
     */
    public function rules(): array
    {
        // Obtenemos el rol vinculado a la ruta para excluirlo de la regla unique del nombre
        /** @var Role|null $rol */
        $rol = $this->route('rol');

        return [
            'name' => [
                'required',
                'string',
                'max:125',
                'regex:'.ValidacionHelper::REGEX_TEXTO,
                Rule::unique(config('permission.table_names.roles'), 'name')->ignore($rol?->getKey()),
            ],
            'descripcion' => ['nullable', 'string', 'max:255', 'regex:'.ValidacionHelper::REGEX_TEXTO],
        ];
    }

    /**
     * Devuelve los nombres traducidos de los campos para los mensajes de validación.
     */
    public function attributes(): array
    {
        return [
            'name' => trans('fields.input.nombre_rol'),
            'descripcion' => trans('fields.input.descripcion'),
        ];
    }
}
