<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasPublicUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Table('configuraciones')]
#[Fillable(['clave', 'valor'])]
class Configuracion extends Model
{
    use HasPublicUlid;

    // Clave de caché donde se guarda el mapa completo clave => valor de los ajustes
    private const string CACHE_KEY = 'configuraciones';

    /**
     * Registra el evento de guardado para invalidar la caché cada vez que cambia un ajuste.
     */
    protected static function booted(): void
    {
        // Cualquier alta, cambio o borrado invalida el mapa cacheado
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /**
     * Devuelve el mapa cacheado de todos los ajustes indexado por clave (clave => valor).
     */
    public static function valores(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn (): array => self::query()->pluck('valor', 'clave')->all());
    }

    /**
     * Devuelve el valor de un ajuste por su clave, o el valor por defecto recibido si no existe.
     */
    public static function valor(string $clave, ?string $defecto = null): ?string
    {
        return self::valores()[$clave] ?? $defecto;
    }

    /**
     * Persiste el conjunto de ajustes recibido (clave => valor), creando o actualizando cada fila.
     */
    public static function establecer(array $valores): void
    {
        // Recorremos cada ajuste recibido creándolo o actualizándolo por su clave
        foreach ($valores as $clave => $valor) {
            self::query()->updateOrCreate(['clave' => $clave], ['valor' => $valor]);
        }
    }
}
