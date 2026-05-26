<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder que crea el usuario administrador por defecto de la aplicación si no existe todavía y le asigna el rol de superadmin.
 */
class UsuarioSeeder extends Seeder
{
    /**
     * Crea el usuario administrador inicial buscando por email para evitar duplicados y le asigna el rol de superadmin.
     */
    public function run(): void
    {
        // Creamos el usuario admin si no existe uno con ese email, asegurando idempotencia entre ejecuciones
        $admin = Usuario::firstOrCreate(
            ['email' => 'admin@laravelspace.test'],
            [
                'nombre' => 'Admin',
                'primer_apellido' => 'Admin',
                'segundo_apellido' => null,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Le asignamos el rol de superadmin (assignRole es idempotente: no duplica si ya lo tiene)
        $admin->assignRole(RoleEnum::SUPERADMIN->value);
    }
}
