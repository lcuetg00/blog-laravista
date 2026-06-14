<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use App\Http\Requests\IndexPaginaRequest;
use App\Models\Pagina;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as MiddlewareItem;

class PaginaController extends Controller implements HasMiddleware
{
    /**
     * Declara el middleware aplicado a nivel de clase (permiso de listado como baseline).
     * El resto de permisos se declaran como atributos #[Middleware] sobre cada método.
     */
    public static function middleware(): array
    {
        return [
            new MiddlewareItem('can:' . PermissionHelper::PAGINAS_LISTADO_PERMISSION),
        ];
    }

    /**
     * Muestra el listado paginado de páginas del panel aplicando los filtros y la ordenación indicada por URL.
     */
    public function index(IndexPaginaRequest $request): View
    {
        $validated = $request->validated();

        // Aplicamos los filtros opcionales, la ordenación y finalmente siempre se ordena por id descendiente
        $paginas = Pagina::query()
            ->byBusqueda($validated['busqueda'] ?? null)
            ->byActivo($validated['activo'] ?? null)
            ->byOrdenacion($validated['ordenacion'] ?? [])
            ->orderByDesc('id')
            ->paginate()
            // Mantenemos los parámetros de la URL en los enlaces de paginación
            ->withQueryString();

        return view('panel.paginas.index', [
            'paginas' => $paginas,
        ]);
    }

    /**
     * Muestra la ficha de detalle de una página, resuelta por su ulid público.
     */
    #[Middleware('can:' . PermissionHelper::PAGINAS_VER_PERMISSION)]
    public function show(Pagina $pagina): View
    {
        // Cargamos los bloques de la página para listarlos en el detalle
        $pagina->loadMissing('bloques');

        return view('panel.paginas.show', [
            'pagina' => $pagina,
        ]);
    }

    /**
     * Muestra el formulario de edición de una página, resuelta por su ulid público (el guardado lo gestionan los componentes Livewire de la vista).
     */
    #[Middleware('can:' . PermissionHelper::PAGINAS_EDITAR_PERMISSION)]
    public function edit(Pagina $pagina): View
    {
        // Cargamos los bloques de la página para pintar el acordeón de edición
        $pagina->loadMissing('bloques');

        return view('panel.paginas.edit', [
            'pagina' => $pagina,
        ]);
    }
}
