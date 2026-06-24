<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\RoleHelper;
use App\Helpers\ValidacionHelper;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConfiguracionRequest extends FormRequest
{
    /**
     * Autoriza la petición exigiendo rol de superadministrador
     */
    public function authorize(): bool
    {
        return $this->user() !== null && RoleHelper::tieneRolSuperadmin($this->user());
    }

    /**
     * Normaliza la entrada convirtiendo a null las cadenas vacías de cada ajuste antes de validar.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(ValidacionHelper::nullificarVacios($this->only(array_keys($this->rules()))));
    }

    /**
     * Reglas de validación de cada ajuste del sitio según su tipo de campo.
     */
    public function rules(): array
    {
        return [
            'sitio_nombre' => ['nullable', 'string', 'max:255', 'regex:' . ValidacionHelper::REGEX_TEXTO],
            'email_contacto' => ['nullable', 'string', 'email', 'max:512', 'regex:' . ValidacionHelper::REGEX_EMAIL],
            'telefono_contacto' => ['nullable', 'string', 'max:20', 'regex:' . ValidacionHelper::REGEX_TELEFONO],
            'red_github' => ['nullable', 'string', 'url', 'max:512'],
            'red_linkedin' => ['nullable', 'string', 'url', 'max:512'],
            'red_x' => ['nullable', 'string', 'url', 'max:512'],
            'red_instagram' => ['nullable', 'string', 'url', 'max:512'],
        ];
    }

    /**
     * Devuelve los nombres traducidos de los campos para los mensajes de validación.
     */
    public function attributes(): array
    {
        $atributos = [];

        // Reutilizamos las etiquetas traducidas del catálogo de campos para cada clave validada
        foreach (array_keys($this->rules()) as $clave) {
            $atributos[$clave] = trans('configuracion.ajustes.campos.' . $clave);
        }

        return $atributos;
    }
}
