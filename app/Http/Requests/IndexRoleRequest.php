<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\RoleOrdenacionEnum;
use App\Helpers\PermissionHelper;
use App\Helpers\ValidacionHelper;
use App\Http\Requests\Concerns\RequestOrdenacionTrait;
use Illuminate\Foundation\Http\FormRequest;

class IndexRoleRequest extends FormRequest
{
    use RequestOrdenacionTrait;

    /**
     * Autoriza la petición exigiendo permiso de listado de roles.
     */
    public function authorize(): bool
    {
        // Comprobamos que el usuario autenticado puede listar roles
        return $this->user()?->can(PermissionHelper::ROLES_LISTADO_PERMISSION) ?? false;
    }

    /**
     * Indica al trait qué enum define las claves de ordenación válidas para este recurso.
     */
    protected function ordenacionEnum(): string
    {
        return RoleOrdenacionEnum::class;
    }

    /**
     * Normaliza los parámetros de la URL antes de validar, los de la ordenación.
     */
    protected function prepareForValidation(): void
    {
        $this->prepararOrdenacion();
    }

    /**
     * Reglas de validación (ordenación + filtro opcional de búsqueda libre).
     */
    public function rules(): array
    {
        return array_merge($this->reglasOrdenacion(), [
            'busqueda' => ['nullable', 'string', 'max:255', 'regex:'.ValidacionHelper::REGEX_TEXTO],
        ]);
    }

    /**
     * Devuelve los nombres traducidos de los campos para los mensajes de validación.
     */
    public function attributes(): array
    {
        return array_merge($this->atributosOrdenacion(), [
            'busqueda' => trans('fields.input.busqueda'),
        ]);
    }
}
