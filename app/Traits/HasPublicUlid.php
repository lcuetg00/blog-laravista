<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

/**
 * Añade a un modelo un ULID público (columna "ulid") que se usa como identificador en URLs y respuestas, manteniendo el id numérico como PK
 * interna. La columna "ulid" se autorellena al crear el modelo y queda como clave del route model binding.
 *
 * Requisitos del modelo:
 * - Columna "ulid" en la tabla, única e indexada.
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasPublicUlid
{
    use HasUlids;

    /**
     * Indica al trait HasUlids qué columna debe rellenarse automáticamente
     * con un ULID al crear el modelo. La PK ("id") sigue siendo entera
     * autoincremental; solo la columna "ulid" recibe el valor generado.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    /**
     * Devuelve el nombre de la columna usada por el route model binding,
     * para que las URLs se resuelvan por el ulid público en vez del id real.
     */
    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    /**
     * Scope que filtra por el ulid público.
     */
    #[Scope]
    protected function byUlid(Builder $query, string $ulid): Builder
    {
        // Filtramos por la columna ulid
        return $query->where('ulid', $ulid);
    }
}
