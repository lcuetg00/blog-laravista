<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\UsuariosExport;
use App\Helpers\PermissionHelper;
use App\Helpers\UsuarioHelper;
use App\Http\Requests\ExportExcelUsuariosRequest;
use App\Http\Requests\IndexUsuarioRequest;
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
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UsuarioController extends Controller implements HasMiddleware
{
    /**
     * Declara el middleware aplicado a nivel de clase.
     * El resto de permisos se declaran como atributos #[Middleware] sobre cada método.
     */
    public static function middleware(): array
    {
        return [
            new MiddlewareItem('can:' . PermissionHelper::USUARIOS_LISTADO_PERMISSION),
        ];
    }

    /**
     * Muestra el listado paginado de usuarios del panel aplicando la ordenación indicada por URL.
     */
    public function index(IndexUsuarioRequest $request): View
    {
        // Validamos la request
        $validated = $request->validated();

        // Aplicamos los filtros opcionales, la ordenación y finalmente siempre se ordena por id descendiente
        $usuarios = Usuario::query()
            ->byNombreCompleto($validated['nombre_completo'] ?? null)
            ->byEmail($validated['email'] ?? null)
            ->byOrdenacion($validated['ordenacion'] ?? [])
            ->orderByDesc('id')
            ->paginate()
            // Cuando se generan páginas, copia los parámetros de la url en las siguientes páginas con withQueryString
            // De forma que si estamos filtrando u ordenando, al pasar a la página 2 no se pierden estos parámetros
            ->withQueryString();

        return view('panel.usuarios.index', [
            'usuarios' => $usuarios,
        ]);
    }

    /**
     * Muestra el formulario de creación de un nuevo usuario.
     */
    #[Middleware('can:' . PermissionHelper::USUARIOS_CREAR_PERMISSION)]
    public function create(): View
    {
        return view('panel.usuarios.create');
    }

    /**
     * Persiste un nuevo usuario en la base de datos a partir de los datos validados.
     */
    #[Middleware('can:' . PermissionHelper::USUARIOS_CREAR_PERMISSION)]
    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        unset($datos['imagen']);

        // Si no llega contraseña generamos una aleatoria fuerte; el usuario deberá usar el flujo de recuperación
        if (empty($datos['password'])) {
            $datos['password'] = Str::password(32);
        }

        try {
            DB::beginTransaction();

            // Creamos el usuario; el trait HasPublicUlid genera automáticamente el ulid
            // y el cast "hashed" del modelo se encarga de hashear la contraseña
            $nuevoUsuario = Usuario::create($datos);

            // Si se ha subido una imagen la guardamos como avatar (colección singleFile: sustituye a cualquier anterior)
            if ($request->hasFile('imagen')) {
                $nuevoUsuario->addMediaFromRequest('imagen')->toMediaCollection(Usuario::MEDIA_COLLECTION_AVATAR);
            }

            DB::commit();
        } catch (\Exception|\Error $e) {
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
    #[Middleware('can:' . PermissionHelper::USUARIOS_VER_PERMISSION)]
    public function show(Usuario $usuario): View
    {
        return view('panel.usuarios.show', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * Muestra la pantalla de gestión de los CVs del usuario (CVs y sus secciones), resuelto por su ulid público.
     */
    #[Middleware('can:' . PermissionHelper::USUARIOS_CVS_LISTADO_PERMISSION)]
    public function listadoCvs(Usuario $usuario): View
    {
        // Precargamos los CVs del usuario junto con sus secciones para evitar N+1 al pintar la pantalla
        $usuario->loadMissing('usuariosCvs.secciones');

        return view('panel.usuarios.listado-cvs', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * Muestra el formulario de edición de un usuario, resuelto por su ulid público.
     */
    #[Middleware('can:' . PermissionHelper::USUARIOS_EDITAR_PERMISSION)]
    public function edit(Usuario $usuario): View
    {
        // Bloqueamos el acceso al formulario si el usuario activo no puede modificar a este usuario (regla de autoedición)
        abort_unless(UsuarioHelper::puedeModificarUsuario(auth()->user(), $usuario), 403);

        return view('panel.usuarios.edit', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * Actualiza los datos de un usuario existente con los datos validados.
     */
    #[Middleware('can:' . PermissionHelper::USUARIOS_EDITAR_PERMISSION)]
    public function update(UpdateUsuarioRequest $request, Usuario $usuario): RedirectResponse
    {
        $datos = $request->validated();
        unset($datos['imagen']);

        // Si no llega contraseña nueva, la quitamos del array para no sobrescribirla con vacío
        if (empty($datos['password'])) {
            unset($datos['password']);
        }

        try {
            DB::beginTransaction();

            // Actualizamos el usuario con los datos válidos
            $usuario->update($datos);

            // Si se ha subido una imagen nueva, sustituye a la anterior (colección singleFile)
            if ($request->hasFile('imagen')) {
                $usuario->addMediaFromRequest('imagen')->toMediaCollection(Usuario::MEDIA_COLLECTION_AVATAR);
            }

            DB::commit();
        } catch (\Exception|\Error $e) {
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
    #[Middleware('can:' . PermissionHelper::USUARIOS_ELIMINAR_PERMISSION)]
    public function destroy(Usuario $usuario): RedirectResponse
    {
        // Bloqueamos el borrado si el usuario activo no puede borrar a este usuario (regla de autoeliminación, válida incluso para el superadmin)
        abort_unless(UsuarioHelper::puedeBorrarUsuario(auth()->user(), $usuario), 403);

        try {
            DB::beginTransaction();

            // Marcamos el usuario como eliminado (soft delete) gracias al trait SoftDeletes del modelo
            $usuario->delete();

            DB::commit();
        } catch (\Exception|\Error $e) {
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
    #[Middleware('can:' . PermissionHelper::USUARIOS_RESTAURAR_PERMISSION)]
    public function restore(Usuario $usuario): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Solo lo restauramos si el usuario está realmente borrado
            if ($usuario->trashed()) {
                $usuario->restore();
            }

            DB::commit();
        } catch (\Exception|\Error $e) {
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

    /**
     * Exporta el listado de usuarios a un archivo Excel aplicando la ordenación indicada por URL.
     */
    #[Middleware('can:' . PermissionHelper::USUARIOS_EXPORTAR_PERMISSION)]
    public function exportExcel(ExportExcelUsuariosRequest $request): BinaryFileResponse
    {
        // Solo aplicamos los parámetros validados (ordenación + filtros)
        $validated = $request->validated();

        // Generamos el nombre del archivo con el título traducido del recurso y la fecha de exportación
        $nombreArchivo = trans('fields.usuarios.titulo') . ' - ' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new UsuariosExport(
                $validated['ordenacion'] ?? [],
                $validated['nombre_completo'] ?? null,
                $validated['email'] ?? null,
            ),
            $nombreArchivo,
        );
    }
}
