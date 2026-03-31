<?php

namespace Database\Factories;

use App\Models\PersonalTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PersonalTask>
 */
class PersonalTaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(fake()->numberBetween(3, 6)),
            'description' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['pendiente', 'en_progreso', 'completada']),
            'priority' => fake()->optional(0.9)->randomElement(['baja', 'media', 'alta']),
            'order' => fake()->numberBetween(1, 15),
        ];
    }
}
