<?php

namespace App\Actions\Fortify;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): Usuario
    {
        Validator::make($input, [
            'nombre' => ['required', 'string', 'max:70'],
            'primer_apellido' => ['required', 'string', 'max:70'],
            'segundo_apellido' => ['nullable', 'string', 'max:70'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(Usuario::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        return Usuario::create([
            'nombre' => $input['nombre'],
            'primer_apellido' => $input['primer_apellido'],
            'segundo_apellido' => $input['segundo_apellido'] ?? null,
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
