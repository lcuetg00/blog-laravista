<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\PermissionHelper;
use App\Helpers\ValidacionHelper;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    /**
     * Autoriza la petición exigiendo permiso de creación de roles (doble barrera junto al middleware de ruta).
     */
    public function authorize(): bool
    {
        // Comprobamos que el usuario autenticado puede crear roles
        return $this->user()?->can(PermissionHelper::ROLES_CREAR_PERMISSION) ?? false;
    }

    /**
     * Reglas de validación para la creación de un rol nuevo.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:125', 'regex:' . ValidacionHelper::REGEX_TEXTO, 'unique:' . config('permission.table_names.roles') . ',name'],
            'descripcion' => ['nullable', 'string', 'max:255', 'regex:' . ValidacionHelper::REGEX_TEXTO],
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
