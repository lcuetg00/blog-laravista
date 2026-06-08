<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\UsuarioOrdenacionEnum;
use App\Helpers\PermissionHelper;
use App\Helpers\ValidacionHelper;
use App\Http\Requests\Concerns\RequestOrdenacionTrait;
use Illuminate\Foundation\Http\FormRequest;

class IndexUsuarioRequest extends FormRequest
{
    use RequestOrdenacionTrait;

    /**
     * Autoriza la petición exigiendo permiso de listado
     */
    public function authorize(): bool
    {
        // Comprobamos que el usuario autenticado puede listar usuarios
        return $this->user()?->can(PermissionHelper::USUARIOS_LISTADO_PERMISSION) ?? false;
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
     * Reglas de validación (ordenación + filtros opcionales por nombre completo y email)
     */
    public function rules(): array
    {
        return array_merge($this->reglasOrdenacion(), [
            'nombre_completo' => ['nullable', 'string', 'max:255', 'regex:'.ValidacionHelper::REGEX_TEXTO],
            'email' => ['nullable', 'string', 'max:255', 'regex:'.ValidacionHelper::REGEX_EMAIL],
        ]);
    }

    /**
     * Devuelve los nombres traducidos de los campos para los mensajes de validación
     */
    public function attributes(): array
    {
        return array_merge($this->atributosOrdenacion(), [
            'nombre_completo' => trans('fields.input.nombre_completo'),
            'email' => trans('fields.input.email'),
        ]);
    }
}
