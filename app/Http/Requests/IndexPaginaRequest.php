<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\PaginaOrdenacionEnum;
use App\Helpers\PermissionHelper;
use App\Helpers\ValidacionHelper;
use App\Http\Requests\Concerns\RequestOrdenacionTrait;
use Illuminate\Foundation\Http\FormRequest;

class IndexPaginaRequest extends FormRequest
{
    use RequestOrdenacionTrait;

    /**
     * Autoriza la petición exigiendo permiso de listado de páginas.
     */
    public function authorize(): bool
    {
        // Comprobamos que el usuario autenticado puede listar páginas
        return $this->user()?->can(PermissionHelper::PAGINAS_LISTADO_PERMISSION) ?? false;
    }

    /**
     * Indica al trait qué enum define las claves de ordenación válidas para este recurso.
     */
    protected function ordenacionEnum(): string
    {
        return PaginaOrdenacionEnum::class;
    }

    /**
     * Normaliza los parámetros de la URL antes de validar, los de la ordenación y el checkbox de activo.
     */
    protected function prepareForValidation(): void
    {
        $this->prepararOrdenacion();

        // Si llega vacío lo dejamos en null para que no filtre, accedemos a la request
        // Con query revisamos si está en la url
        $activo = $this->query('activo');
        if ($activo === null || $activo === '') {
            $this->merge(['activo' => null]);
        }
    }

    /**
     * Reglas de validación (ordenación + filtros opcionales por clave, búsqueda libre y estado activo).
     */
    public function rules(): array
    {
        return array_merge($this->reglasOrdenacion(), [
            'busqueda' => ['nullable', 'string', 'max:255', 'regex:' . ValidacionHelper::REGEX_TEXTO],
            'activo' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * Devuelve los nombres traducidos de los campos para los mensajes de validación.
     */
    public function attributes(): array
    {
        return array_merge($this->atributosOrdenacion(), [
            'busqueda' => trans('fields.input.busqueda'),
            'activo' => trans('fields.input.activo'),
        ]);
    }
}
