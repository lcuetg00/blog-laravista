<?php

namespace App\Actions\Fortify;

use App\Models\Usuario;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function update(Usuario $user, array $input): void
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
                Rule::unique('usuarios')->ignore($user->id),
            ],
        ])->validateWithBag('updateProfileInformation');

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'nombre' => $input['nombre'],
                'primer_apellido' => $input['primer_apellido'],
                'segundo_apellido' => $input['segundo_apellido'] ?? null,
                'email' => $input['email'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(Usuario $user, array $input): void
    {
        $user->forceFill([
            'nombre' => $input['nombre'],
            'primer_apellido' => $input['primer_apellido'],
            'segundo_apellido' => $input['segundo_apellido'] ?? null,
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
