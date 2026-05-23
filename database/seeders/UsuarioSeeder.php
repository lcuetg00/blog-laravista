<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder que crea el usuario administrador por defecto de la aplicación si no existe todavía.
 */
class UsuarioSeeder extends Seeder
{
    /**
     * Crea el usuario administrador inicial buscando por email para evitar duplicados.
     */
    public function run(): void
    {
        // Creamos el usuario admin si no existe uno con ese email, asegurando idempotencia entre ejecuciones
        Usuario::firstOrCreate(
            ['email' => 'admin@laravelspace.test'],
            [
                'nombre' => 'Admin',
                'primer_apellido' => 'Admin',
                'segundo_apellido' => null,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
