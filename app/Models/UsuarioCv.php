<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ChoiceEnum;
use App\Enums\OrdenacionColumnaEnum;
use App\Enums\UsuarioCvOrdenacionEnum;
use App\Traits\HasPublicUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table('usuarios_cvs')]
#[Fillable(['usuario_id', 'nombre'])]
class UsuarioCv extends Model
{
    use HasFactory, HasPublicUlid, SoftDeletes;

    // Usado por trans_choice en mensajes con :modelo (el CV → "creado")
    public const ChoiceEnum CHOICE = ChoiceEnum::MASCULINO;

    /**
     * Ordena por id descendente por defecto en todas las queries del modelo (los CVs más recientes primero).
     */
    protected static function booted(): void
    {
        static::addGlobalScope('ordenPorId', fn (Builder $query) => $query->orderByDesc('id'));
    }

    /**
     * Relación con el usuario propietario del CV.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Relación con las secciones del CV, ordenadas por su posición.
     */
    public function secciones(): HasMany
    {
        return $this->hasMany(UsuarioCvSeccion::class)->orderBy('orden');
    }

    /**
     * Aplica ORDER BY encadenados respetando el orden de las claves del array (clave → dirección), para soportar multiordenación.
     */
    public function scopeByOrdenacion(Builder $query, array $ordenacion): Builder
    {
        // Recorremos las claves en su orden de inserción para preservar la prioridad de la ordenación
        foreach ($ordenacion as $clave => $direccion) {
            $caso = UsuarioCvOrdenacionEnum::tryFrom((string) $clave);
            $dir = OrdenacionColumnaEnum::tryFrom((string) $direccion);

            // Si la clave o la dirección no son válidas saltamos esa entrada (defensa adicional sobre el componente Livewire)
            if ($caso === null || $dir === null) {
                continue;
            }

            $query->orderBy($caso->getNombreColumna(), $dir->value);
        }

        return $query;
    }
}
