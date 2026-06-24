<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Seeder;

/**
 * Seeder que sincroniza las claves de ajustes del sitio con la base de datos: crea las que falten y elimina las que ya no estén declaradas.
 */
class ConfiguracionSeeder extends Seeder
{
    /**
     * Sincroniza los ajustes: crea cada clave declarada si no existe (sin pisar el valor ya guardado) y elimina las claves obsoletas.
     */
    public function run(): void
    {
        // Claves de ajuste editables del sitio que deben existir en la base de datos
        $claves = [
            'sitio_nombre',
            'email_contacto',
            'telefono_contacto',
            'red_github',
            'red_linkedin',
            'red_x',
            'red_instagram',
        ];

        // Creamos cada clave que falte respetando el valor existente para no sobrescribir lo configurado
        foreach ($claves as $clave) {
            Configuracion::query()->firstOrCreate(['clave' => $clave], ['valor' => null]);
        }
    }
}
