<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
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
