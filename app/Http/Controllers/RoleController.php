<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\RolesExport;
use App\Helpers\PermissionHelper;
use App\Helpers\RoleHelper;
use App\Http\Requests\ExportExcelRolesRequest;
use App\Http\Requests\IndexRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as MiddlewareItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RoleController extends Controller implements HasMiddleware
{
    /**
     * Declara el middleware aplicado a nivel de clase.
     * El resto de permisos se declaran como atributos #[Middleware] sobre cada método.
     */
    public static function middleware(): array
    {
        return [
            new MiddlewareItem('can:' . PermissionHelper::ROLES_LISTADO_PERMISSION),
        ];
    }

    /**
     * Muestra el listado paginado de roles del panel aplicando la búsqueda libre y la ordenación indicada por URL.
     */
    public function index(IndexRoleRequest $request): View
    {
        $validated = $request->validated();

        // Aplicamos el filtro de búsqueda libre, la ordenación y finalmente siempre se ordena por id descendiente
        $roles = Role::query()
            ->byBusqueda($validated['busqueda'] ?? null)
            ->byOrdenacion($validated['ordenacion'] ?? [])
            ->orderByDesc('id')
            ->paginate()
            // Cuando se generan páginas, copia los parámetros de la url en las siguientes páginas con withQueryString
            // De forma que si estamos filtrando u ordenando, al pasar a la página 2 no se pierden estos parámetros
            ->withQueryString();

        return view('panel.roles.index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Muestra el formulario de creación de un nuevo rol.
     */
    #[Middleware('can:' . PermissionHelper::ROLES_CREAR_PERMISSION)]
    public function create(): View
    {
        return view('panel.roles.create');
    }

    /**
     * Persiste un nuevo rol en la base de datos a partir de los datos validados, forzando el guard 'web'.
     */
    #[Middleware('can:' . PermissionHelper::ROLES_CREAR_PERMISSION)]
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        // Forzamos el guard 'web' para mantener coherencia con el resto de la aplicación
        $datos['guard_name'] = 'web';

        try {
            DB::beginTransaction();

            // Creamos el rol; el trait HasPublicUlid genera automáticamente el ulid
            Role::create($datos);

            DB::commit();
        } catch (\Exception | \Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al crear el rol', ['exception' => $e]);

            return redirect()
                ->route('panel.roles.index')
                ->with('error', trans('actions.generic_error'));
        }

        return redirect()
            ->route('panel.roles.index')
            ->with('success', trans_choice('actions.created', Role::CHOICE->value, ['modelo' => trans('fields.models.rol')]));
    }

    /**
     * Muestra la ficha de detalle de un rol, resuelto por su ulid público.
     */
    #[Middleware('can:' . PermissionHelper::ROLES_VER_PERMISSION)]
    public function show(Role $rol): View
    {
        return view('panel.roles.show', [
            'rol' => $rol,
        ]);
    }

    /**
     * Muestra el formulario de edición de un rol, resuelto por su ulid público.
     */
    #[Middleware('can:' . PermissionHelper::ROLES_EDITAR_PERMISSION)]
    public function edit(Role $rol): View
    {
        return view('panel.roles.edit', [
            'rol' => $rol,
        ]);
    }

    /**
     * Actualiza los datos de un rol existente con los datos validados.
     */
    #[Middleware('can:' . PermissionHelper::ROLES_EDITAR_PERMISSION)]
    public function update(UpdateRoleRequest $request, Role $rol): RedirectResponse
    {
        $datos = $request->validated();

        try {
            DB::beginTransaction();

            // Actualizamos el rol con los datos válidos
            $rol->update($datos);

            DB::commit();
        } catch (\Exception | \Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al actualizar el rol', ['exception' => $e]);

            return redirect()
                ->route('panel.roles.index')
                ->with('error', trans('actions.generic_error'));
        }

        return redirect()
            ->route('panel.roles.index')
            ->with('success', trans_choice('actions.updated', Role::CHOICE->value, ['modelo' => trans('fields.models.rol')]));
    }

    /**
     * Elimina un rol, bloqueando la operación si se trata de uno de los roles del sistema protegidos por RoleEnum.
     */
    #[Middleware('can:' . PermissionHelper::ROLES_ELIMINAR_PERMISSION)]
    public function destroy(Role $rol): RedirectResponse
    {
        // Bloqueamos el borrado si el rol está protegido (id presente en RoleEnum)
        abort_if(RoleHelper::esRolProtegido($rol), 403);

        try {
            DB::beginTransaction();

            // Eliminamos el rol; la tabla pivot model_has_roles arrastra el borrado en cascada (configurado en la migración de Spatie)
            $rol->delete();

            DB::commit();
        } catch (\Exception | \Error $e) {
            DB::rollBack();
            Log::error('Ha ocurrido un error al eliminar el rol', ['exception' => $e]);

            return redirect()
                ->route('panel.roles.index')
                ->with('error', trans('actions.generic_error'));
        }

        return redirect()
            ->route('panel.roles.index')
            ->with('success', trans_choice('actions.deleted', Role::CHOICE->value, ['modelo' => trans('fields.models.rol')]));
    }

    /**
     * Exporta el listado de roles a un archivo Excel aplicando los filtros y la ordenación indicada por URL.
     */
    #[Middleware('can:' . PermissionHelper::ROLES_EXPORTAR_PERMISSION)]
    public function exportExcel(ExportExcelRolesRequest $request): BinaryFileResponse
    {
        // Solo aplicamos los parámetros validados (ordenación + filtro de búsqueda)
        $validated = $request->validated();

        // Generamos el nombre del archivo con el título traducido del recurso y la fecha de exportación
        $nombreArchivo = trans('fields.roles.titulo') . ' - ' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new RolesExport(
                $validated['ordenacion'] ?? [],
                $validated['busqueda'] ?? null,
            ),
            $nombreArchivo,
        );
    }
}
