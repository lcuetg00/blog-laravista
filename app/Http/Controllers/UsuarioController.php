<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\Usuario;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as MiddlewareItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UsuarioController extends Controller implements HasMiddleware
{
    /**
     * Declara el middleware aplicado a nivel de clase.
     * El resto de permisos se declaran como atributos #[Middleware] sobre cada método.
     *
     * @return array<int, MiddlewareItem>
     */
    public static function middleware(): array
    {
        return [
            new MiddlewareItem('can:usuarios_listado', only: ['index']),
        ];
    }

    /**
     * Muestra el listado paginado de usuarios del panel.
     */
    public function index(): View
    {
        $usuarios = Usuario::query()
            ->orderByDesc('id')
            ->paginate();

        return view('panel.usuarios.index', [
            'usuarios' => $usuarios,
        ]);
    }

    /**
     * Muestra el formulario de creación de un nuevo usuario.
     */
    #[Middleware('can:usuarios_crear')]
    public function create(): View
    {
        return view('panel.usuarios.create');
    }

    /**
     * Persiste un nuevo usuario en la base de datos a partir de los datos validados.
     */
    #[Middleware('can:usuarios_crear')]
    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        // Si no llega contraseña generamos una aleatoria fuerte; el usuario deberá usar el flujo de recuperación
        if (empty($datos['password'])) {
            $datos['password'] = Str::password(32);
        }

        try {
            DB::beginTransaction();

            // Creamos el usuario; el trait HasPublicUlid genera automáticamente el ulid
            // y el cast "hashed" del modelo se encarga de hashear la contraseña
            Usuario::create($datos);

            DB::commit();
        } catch (\Exception | \Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al crear el usuario', ['exception' => $e]);

            return redirect()
                ->route('panel.usuarios.index')
                ->with('error', trans('actions.generic_error'));
        }

        return redirect()
            ->route('panel.usuarios.index')
            ->with('success', trans_choice('actions.created', Usuario::CHOICE->value, ['modelo' => trans('fields.models.usuario')]));
    }

    /**
     * Muestra la ficha de detalle de un usuario, resuelto por su ulid público.
     */
    #[Middleware('can:usuarios_ver')]
    public function show(Usuario $usuario): View
    {
        return view('panel.usuarios.show', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * Muestra el formulario de edición de un usuario, resuelto por su ulid público.
     */
    #[Middleware('can:usuarios_editar')]
    public function edit(Usuario $usuario): View
    {
        return view('panel.usuarios.edit', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * Actualiza los datos de un usuario existente con los datos validados.
     */
    #[Middleware('can:usuarios_editar')]
    public function update(UpdateUsuarioRequest $request, Usuario $usuario): RedirectResponse
    {
        $datos = $request->validated();

        // Si no llega contraseña nueva, la quitamos del array para no sobrescribirla con vacío
        if (empty($datos['password'])) {
            unset($datos['password']);
        }

        try {
            DB::beginTransaction();

            // Actualizamos el usuario con los datos válidos
            $usuario->update($datos);

            DB::commit();
        } catch (\Exception | \Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al actualizar el usuario', ['exception' => $e]);

            return redirect()
                ->route('panel.usuarios.index')
                ->with('error', trans('actions.generic_error'));
        }

        return redirect()
            ->route('panel.usuarios.index')
            ->with('success', trans_choice('actions.updated', Usuario::CHOICE->value, ['modelo' => trans('fields.models.usuario')]));
    }

    /**
     * Realiza un soft delete del usuario indicado por su ulid público.
     */
    #[Middleware('can:usuarios_eliminar')]
    public function destroy(Usuario $usuario): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Marcamos el usuario como eliminado (soft delete) gracias al trait SoftDeletes del modelo
            $usuario->delete();

            DB::commit();
        } catch (\Exception | \Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al eliminar el usuario', ['exception' => $e]);

            return redirect()
                ->route('panel.usuarios.index')
                ->with('error', trans('actions.generic_error'));
        }

        return redirect()
            ->route('panel.usuarios.index')
            ->with('success', trans_choice('actions.deleted', Usuario::CHOICE->value, ['modelo' => trans('fields.models.usuario')]));
    }

    /**
     * Restaura un usuario previamente eliminado (soft deleted), resuelto por su ulid público.
     */
    #[Middleware('can:usuarios_restaurar')]
    public function restore(Usuario $usuario): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Solo lo restauramos si el usuario está realmente borrado
            if ($usuario->trashed()) {
                $usuario->restore();
            }

            DB::commit();
        } catch (\Exception | \Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al restaurar el usuario', ['exception' => $e]);

            return redirect()
                ->route('panel.usuarios.index')
                ->with('error', trans('actions.generic_error'));
        }

        return redirect()
            ->route('panel.usuarios.index')
            ->with('success', trans_choice('actions.restored', Usuario::CHOICE->value, ['modelo' => trans('fields.models.usuario')]));
    }
}
