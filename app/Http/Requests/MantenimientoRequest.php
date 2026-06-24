<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\RoleHelper;
use App\Helpers\ValidacionHelper;
use Illuminate\Foundation\Http\FormRequest;

class MantenimientoRequest extends FormRequest
{
    /**
     * Autoriza la petición exigiendo rol de superadministrador (doble barrera junto al middleware de ruta).
     */
    public function authorize(): bool
    {
        return $this->user() !== null && RoleHelper::tieneRolSuperadmin($this->user());
    }

    /**
     * Normaliza la entrada convirtiendo a null el secreto vacío antes de validar.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(['secreto' => ValidacionHelper::nullificarVacios($this->input('secreto'))]);
    }

    /**
     * Reglas de validación: el secreto solo se exige al activar el mantenimiento (al desactivar no se envía).
     */
    public function rules(): array
    {
        // Si la aplicación ya está en mantenimiento, esta petición la desactiva y no necesita secreto
        if (app()->maintenanceMode()->active()) {
            return [];
        }

        return [
            'secreto' => ['required', 'string', 'min:4', 'max:50', 'regex:' . ValidacionHelper::REGEX_SLUG],
        ];
    }

    /**
     * Devuelve el nombre traducido del campo para los mensajes de validación.
     */
    public function attributes(): array
    {
        return [
            'secreto' => trans('configuracion.mantenimiento.secreto_label'),
        ];
    }

    /**
     * Mensaje específico cuando el secreto contiene caracteres no aptos para una URL.
     */
    public function messages(): array
    {
        return [
            'secreto.regex' => trans('configuracion.mantenimiento.secreto_regex'),
        ];
    }
}
