<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Orden importante: permisos y roles primero, porque UsuarioSeeder
        // necesita que el rol SUPERADMIN exista para asignárselo al admin.
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UsuarioSeeder::class,
        ]);
    }
}
