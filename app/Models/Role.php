<?php

namespace App\Models;

use App\Enums\ChoiceEnum;
use App\Enums\OrdenacionColumnaEnum;
use App\Enums\RoleOrdenacionEnum;
use App\Traits\HasPublicUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Modelo Role propio de la aplicación que extiende el de Spatie. Configurado como modelo activo en config/permission.php para poder añadir lógica/relaciones propias sin tocar el paquete.
 */
#[Fillable(['name', 'guard_name', 'descripcion'])]
class Role extends SpatieRole
{
    use HasPublicUlid;

    // Usado por trans_choice en mensajes con :modelo
    public const ChoiceEnum CHOICE = ChoiceEnum::MASCULINO;

    /**
     * Filtra por texto libre: cada palabra introducida debe aparecer (LIKE) en el nombre o la descripción del rol.
     */
    public function scopeByBusqueda(Builder $query, ?string $valor): Builder
    {
        // Si no llega valor o llega en blanco no filtramos
        if ($valor === null || trim($valor) === '') {
            return $query;
        }

        // Columnas de texto sobre las que aplicamos LIKE
        $columnasTexto = ['name', 'descripcion'];

        // Lo partimos en palabras para hacer una búsqueda con LIKE
        $palabras = array_filter(preg_split('/\s+/', trim($valor)) ?: []);

        // Por cada palabra exigimos que aparezca en alguna columna (AND entre palabras, OR entre columnas)
        foreach ($palabras as $palabra) {
            $query->where(function (Builder $q) use ($columnasTexto, $palabra) {
                foreach ($columnasTexto as $columna) {
                    $q->orWhere($columna, 'like', '%' . $palabra . '%');
                }
            });
        }

        return $query;
    }

    /**
     * Aplica ORDER BY encadenados respetando el orden de las claves del array (clave URL → dirección).
     */
    public function scopeByOrdenacion(Builder $query, array $ordenacion): Builder
    {
        // Recorremos las claves en su orden de inserción para preservar la prioridad de la ordenación
        foreach ($ordenacion as $clave => $direccion) {
            $caso = RoleOrdenacionEnum::tryFrom((string) $clave);
            $dir = OrdenacionColumnaEnum::tryFrom((string) $direccion);

            // Si la clave o la dirección no son válidas saltamos esa entrada (defensa adicional sobre el FormRequest)
            if ($caso === null || $dir === null) {
                continue;
            }

            $query->orderBy($caso->getNombreColumna(), $dir->value);
        }

        return $query;
    }
}
