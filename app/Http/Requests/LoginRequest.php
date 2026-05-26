<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;

class LoginRequest extends FortifyLoginRequest
{
    /**
     * Devuelve los nombres de los campos traducidos para los mensajes de validación.
     */
    public function attributes(): array
    {
        return [
            Fortify::username() => trans('fields.input.email'),
            'password' => trans('fields.input.password'),
        ];
    }
}
