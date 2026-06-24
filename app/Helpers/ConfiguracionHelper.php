<?php

declare(strict_types=1);

namespace App\Helpers;

use Composer\InstalledVersions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

/**
 * Helper que recopila la información de diagnóstico del sistema (Laravel, PHP, base de datos, almacenamiento, caché y salud).
 */
class ConfiguracionHelper
{
    /**
     * Paquetes destacados a mostrar en el card de Laravel
     */
    private const array PAQUETES_DESTACADOS = [
        'livewire/livewire',
        'laravel/fortify',
        'spatie/laravel-permission',
        'spatie/laravel-medialibrary',
        'spatie/laravel-translatable',
        'mcamara/laravel-localization',
        'maatwebsite/excel',
        'diglactic/laravel-breadcrumbs',
    ];

    /**
     * Directivas de PHP relevantes para la subida y procesado de imágenes
     */
    private const array DIRECTIVAS_PHP = [
        'upload_max_filesize',
        'post_max_size',
        'memory_limit',
        'max_execution_time',
        'max_file_uploads',
        'max_input_vars',
    ];

    /**
     * Devuelve la versión de Laravel y las versiones de los paquetes destacados instalados
     */
    public static function informacionLaravel(): array
    {
        $paquetes = [];

        // Recorremos los paquetes destacados resolviendo su versión instalada desde Composer
        foreach (self::PAQUETES_DESTACADOS as $paquete) {
            if (InstalledVersions::isInstalled($paquete)) {
                $paquetes[$paquete] = InstalledVersions::getPrettyVersion($paquete);
            } else {
                $paquetes[$paquete] = '-';
            }
        }

        return [
            'version' => app()->version(),
            'paquetes' => $paquetes,
        ];
    }

    /**
     * Devuelve la versión de PHP y las directivas de configuración relevantes (tamaño de subida, memoria...).
     */
    public static function informacionPhp(): array
    {
        $directivas = [];

        // Leemos cada directiva del php.ini activo
        foreach (self::DIRECTIVAS_PHP as $directiva) {
            $directivas[$directiva] = ini_get($directiva) ?: '-';
        }

        return [
            'version' => PHP_VERSION,
            'directivas' => $directivas,
        ];
    }

    /**
     * Devuelve los datos de la conexión a la base de datos (motor, nombre, host y atributos del PDO: versiones, estado y persistencia)
     */
    public static function informacionBaseDatos(): array
    {
        $conexion = DB::connection();

        // Atributos del PDO a leer; cada uno se protege por separado porque no todos los drivers los soportan
        $atributosPdo = [
            'version' => \PDO::ATTR_SERVER_VERSION,
            'version_cliente' => \PDO::ATTR_CLIENT_VERSION,
            'estado_conexion' => \PDO::ATTR_CONNECTION_STATUS,
            'persistente' => \PDO::ATTR_PERSISTENT,
        ];

        // Abrir el PDO y leer cada atributo puede fallar si la BD no está accesible o el driver no lo admite
        $valoresPdo = [];
        foreach ($atributosPdo as $clave => $atributo) {
            try {
                $valor = $conexion->getPdo()->getAttribute($atributo);
                $valoresPdo[$clave] = ($valor === false || $valor === null || $valor === '') ? '-' : (string) $valor;
            } catch (Throwable) {
                $valoresPdo[$clave] = '-';
            }
        }

        return [
            'driver' => $conexion->getDriverName(),
            'nombre' => $conexion->getDatabaseName(),
            'host' => $conexion->getConfig('host') ?: '-',
            ...$valoresPdo,
        ];
    }

    /**
     * Devuelve el disco por defecto, el espacio libre/total del sistema y el recuento y peso de la biblioteca de medios
     */
    public static function informacionAlmacenamiento(): array
    {
        // El espacio en disco se mide sobre la raíz del proyecto
        $libre = @disk_free_space(base_path()) ?: 0;
        $total = @disk_total_space(base_path()) ?: 0;

        return [
            'disco' => config('filesystems.default'),
            'espacio_libre' => self::formatearBytes((int) $libre),
            'espacio_total' => self::formatearBytes((int) $total),
            'media_total' => Media::query()->count(),
            'media_peso' => self::formatearBytes((int) Media::query()->sum('size')),
        ];
    }

    /**
     * Devuelve el estado de las cachés compiladas, los drivers de caché y cola y el número de trabajos fallidos
     */
    public static function informacionCache(): array
    {
        // El número de trabajos fallidos se lee de la tabla de framework
        try {
            $jobsFallidos = DB::table('failed_jobs')->count();
        } catch (Throwable) {
            $jobsFallidos = 0;
        }

        return [
            'config' => app()->configurationIsCached(),
            'rutas' => app()->routesAreCached(),
            'eventos' => app()->eventsAreCached(),
            'vistas' => count(glob(storage_path('framework/views/*.php')) ?: []),
            'driver_cache' => config('cache.default'),
            'driver_cola' => config('queue.default'),
            'jobs_fallidos' => $jobsFallidos,
        ];
    }

    /**
     * Boolean por cada comprobación de salud del sistema
     */
    public static function informacionSalud(): array
    {
        return [
            'bd' => self::compruebaBaseDatos(),
            'storage' => is_writable(storage_path()),
            'bootstrap' => is_writable(base_path('bootstrap/cache')),
            'cache' => self::compruebaCache(),
        ];
    }

    /**
     * Comprueba que la conexión a la base de datos responde abriendo el PDO
     */
    private static function compruebaBaseDatos(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Comprueba que la caché es funcional escribiendo y leyendo un valor temporal
     */
    private static function compruebaCache(): bool
    {
        try {
            Cache::put('configuracion_health_check', true, 5);
            $escrito = Cache::get('configuracion_health_check') === true;

            // Limpiamos el valor temporal tras la comprobación para no dejar rastro en la caché
            Cache::forget('configuracion_health_check');

            return $escrito;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Formatea un número de bytes a una cadena legible con la unidad adecuada (B, KB, MB, GB, TB)
     */
    private static function formatearBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '-';
        }

        $unidades = ['B', 'KB', 'MB', 'GB', 'TB'];

        // Subimos de unidad mientras quepan al menos 1024 bytes, sin pasarnos de la última (TB)
        $index = 0;
        $valor = $bytes;
        while ($valor >= 1024 && $index < count($unidades) - 1) {
            $valor /= 1024;
            $index++;
        }

        return round($valor, 2) . ' ' . $unidades[$index];
    }
}
