<?php

namespace Database\Factories;

use App\Models\UsuarioCv;
use App\Models\UsuarioCvSeccion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UsuarioCvSeccion>
 */
class UsuarioCvSeccionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'usuario_cv_id' => UsuarioCv::factory(),
            'titulo' => fake()->sentence(3),
            'descripcion' => fake()->paragraph(),
            // Único junto a usuario_cv_id: valor único por defecto para evitar choques al crear varias secciones en tests
            'orden' => fake()->unique()->numberBetween(1, 60000),
        ];
    }
}
