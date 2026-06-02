<?php

namespace App\Providers;

use App\Helpers\RoleHelper;
use App\Helpers\UsuarioHelper;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Vincula el LoginRequest de Fortify al propio para personalizar la validación.
     */
    public function register(): void
    {
        $this->app->bind(
            \Laravel\Fortify\Http\Requests\LoginRequest::class,
            LoginRequest::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forzar HTTPS cuando APP_URL usa https
        // if (config('app.url') && str_starts_with(config('app.url'), 'https://')) {
        //     URL::forceScheme('https');
        // }

        /**
         * Bypass de permisos para el superadmin (rol con id RoleEnum::SUPERADMIN).
         * Se ejecuta antes que cualquier $user->can() / Gate::allows() y, si el
         * usuario tiene ese rol (comprobado por id), devuelve true y se salta
         * toda comprobación de permisos.
         */
        Gate::before(function ($user, $ability) {
            return RoleHelper::tieneRolSuperadmin($user) ? true : null;
        });

        /**
         * Reglas por defecto para las contraseñas (se aplican en cualquier FormRequest que use Password::defaults()).
         */
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols();
        });
    }
}
