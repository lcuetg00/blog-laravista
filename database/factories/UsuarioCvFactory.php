<?php

namespace Database\Factories;

use App\Models\Usuario;
use App\Models\UsuarioCv;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UsuarioCv>
 */
class UsuarioCvFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'usuario_id' => Usuario::factory(),
            'nombre' => fake()->jobTitle() . ' CV',
        ];
    }
}
