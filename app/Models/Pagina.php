<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ChoiceEnum;
use App\Enums\OrdenacionColumnaEnum;
use App\Enums\PaginaClaveEnum;
use App\Enums\PaginaOrdenacionEnum;
use App\Traits\HasPublicUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Table('paginas')]
#[Fillable(['clave', 'titulo', 'descripcion', 'activo'])]
// Columnas traducidas con Translatable de spatie
#[Translatable(['titulo', 'descripcion'])]
class Pagina extends Model
{
    use HasFactory, HasPublicUlid, HasTranslations, SoftDeletes;

    // Usado por trans_choice en mensajes con :modelo (la página → "actualizada")
    public const ChoiceEnum CHOICE = ChoiceEnum::FEMENINO;

    /**
     * Tipos de los atributos del modelo.
     */
    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    /**
     * Indica si la página puede desactivarse desde el panel (delega en su clave; las claves no catalogadas se consideran desactivables).
     */
    protected function esDesactivable(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => PaginaClaveEnum::tryFrom($this->clave)?->esDesactivable() ?? true,
        );
    }

    /**
     * Relación con bloques, una página puede tener n bloques
     */
    public function bloques(): HasMany
    {
        return $this->hasMany(Bloque::class)->orderBy('orden');
    }

    /**
     * Filtro por activo: null devuelve todas, true/false devuelve solo las páginas activas o inactivas
     */
    public function scopeByActivo(Builder $query, ?bool $valor): Builder
    {
        // Si no llega valor no filtramos
        if ($valor === null) {
            return $query;
        }

        return $query->where('activo', $valor);
    }

    /**
     * Búsqueda libre por texto: busca el término en la clave y en la traducción del titulo del locale activo
     */
    public function scopeByBusqueda(Builder $query, ?string $valor): Builder
    {
        // Si no llega valor o llega en blanco no filtramos
        if ($valor === null || trim($valor) === '') {
            return $query;
        }

        $busqueda = '%' . trim($valor) . '%';
        // Usamos la sintaxis (titulo->idioma) para apuntar a la traducción del locale activo
        $rutaTitulo = 'titulo->' . app()->getLocale();

        return $query->where(function (Builder $q) use ($busqueda, $rutaTitulo): void {
            $q->where('clave', 'like', $busqueda)
                ->orWhere($rutaTitulo, 'like', $busqueda);
        });
    }

    /**
     * Aplica ORDER BY encadenados respetando el orden de las claves del array (clave URL → dirección).
     */
    public function scopeByOrdenacion(Builder $query, array $ordenacion): Builder
    {
        // Recorremos las claves en su orden de inserción para preservar la prioridad de la ordenación
        foreach ($ordenacion as $clave => $direccion) {
            $caso = PaginaOrdenacionEnum::tryFrom((string) $clave);
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
