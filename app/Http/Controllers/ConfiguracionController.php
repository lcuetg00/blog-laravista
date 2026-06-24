<?php

namespace App\Http\Controllers;

use App\Helpers\ConfiguracionHelper;
use App\Http\Requests\MantenimientoRequest;
use App\Http\Requests\UpdateConfiguracionRequest;
use App\Models\Configuracion;
use Illuminate\Foundation\Http\MaintenanceModeBypassCookie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as MiddlewareItem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ConfiguracionController extends Controller implements HasMiddleware
{
    /**
     * Restringe toda la sección de configuración a los superadministradores.
     */
    public static function middleware(): array
    {
        return [
            new MiddlewareItem('role:superadmin'),
        ];
    }

    /**
     * Muestra la información del sistema (Laravel, PHP, base de datos y almacenamiento).
     */
    public function informacion(): View
    {
        return view('panel.configuracion.informacion', [
            'laravel' => ConfiguracionHelper::informacionLaravel(),
            'php' => ConfiguracionHelper::informacionPhp(),
            'baseDatos' => ConfiguracionHelper::informacionBaseDatos(),
            'almacenamiento' => ConfiguracionHelper::informacionAlmacenamiento(),
        ]);
    }

    /**
     * Muestra el formulario de parámetros editables del sitio (identidad, contacto y redes).
     */
    public function parametros(): View
    {
        return view('panel.configuracion.parametros', [
            'valoresAjustes' => Configuracion::valores(),
        ]);
    }

    /**
     * Muestra la página de mantenimiento (estado de caché y salud, limpieza de caché y modo mantenimiento).
     */
    public function mantenimiento(): View
    {
        return view('panel.configuracion.mantenimiento', [
            'cacheInfo' => ConfiguracionHelper::informacionCache(),
            'salud' => ConfiguracionHelper::informacionSalud(),
            'mantenimientoActivo' => app()->maintenanceMode()->active(),
        ]);
    }

    /**
     * Persiste los parámetros del sitio enviados desde el formulario.
     */
    public function actualizarParametros(UpdateConfiguracionRequest $request): RedirectResponse
    {
        $ajustes = $request->validated();

        try {
            DB::beginTransaction();

            Configuracion::establecer($ajustes);

            DB::commit();
        } catch (\Exception|\Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al guardar los ajustes del sitio', ['exception' => $e]);

            return redirect()
                ->route('panel.configuracion.parametros')
                ->with('error', trans('actions.generic_error'));
        }

        return redirect()
            ->route('panel.configuracion.parametros')
            ->with('success', trans('actions.settings_saved'));
    }

    /**
     * Limpia la caché de configuración, rutas, vistas y eventos de la aplicación.
     */
    public function limpiarCache(): RedirectResponse
    {
        try {
            // Vaciamos cada caché compilada para que la aplicación vuelva a leer los ficheros de origen
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('event:clear');
            Artisan::call('cache:clear');
        } catch (\Exception|\Error $e) {
            Log::error('Ha ocurrido un error al limpiar la caché de la aplicación', ['exception' => $e]);

            return redirect()
                ->route('panel.configuracion.mantenimiento')
                ->with('error', trans('actions.generic_error'));
        }

        return redirect()
            ->route('panel.configuracion.mantenimiento')
            ->with('success', trans('actions.cache_cleared'));
    }

    /**
     * Elimina todas las vistas Blade compiladas (incluidas las de componentes Livewire); se recompilan bajo demanda.
     */
    public function limpiarVistas(): RedirectResponse
    {
        try {
            Artisan::call('view:clear');
        } catch (\Exception|\Error $e) {
            Log::error('Ha ocurrido un error al limpiar las vistas compiladas', ['exception' => $e]);

            return redirect()
                ->route('panel.configuracion.mantenimiento')
                ->with('error', trans('actions.generic_error'));
        }

        return redirect()
            ->route('panel.configuracion.mantenimiento')
            ->with('success', trans('actions.views_cleared'));
    }

    /**
     * Activa o desactiva el modo mantenimiento usando el secreto indicado, manteniendo el acceso del superadmin mediante la cookie de bypass.
     */
    public function cambiarMantenimiento(MantenimientoRequest $request): RedirectResponse
    {
        try {
            // Si ya está activo lo desactivamos
            if (app()->maintenanceMode()->active()) {
                Artisan::call('up');

                return redirect()
                    ->route('panel.configuracion.mantenimiento')
                    ->with('success', trans('actions.maintenance_off'));
            }

            // Activamos con el secreto recibido y guardamos su cookie de bypass
            $secreto = $request->validated()['secreto'];
            Artisan::call('down', ['--secret' => $secreto]);
        } catch (\Exception|\Error $e) {
            Log::error('Ha ocurrido un error al cambiar el modo mantenimiento', ['exception' => $e]);

            return redirect()
                ->route('panel.configuracion.mantenimiento')
                ->with('error', trans('actions.generic_error'));
        }

        // Devolvemos la cookie de bypass para que el superadmin siga teniendo acceso al panel
        return redirect()
            ->route('panel.configuracion.mantenimiento')
            ->withCookie(MaintenanceModeBypassCookie::create($secreto))
            ->with('success', trans('actions.maintenance_on'));
    }
}
