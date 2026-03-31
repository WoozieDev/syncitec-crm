<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectModule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectModule>
 */
class ProjectModuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->randomElement([
                'Descubrimiento',
                'Diseno',
                'Desarrollo',
                'Integraciones',
                'QA',
                'Despliegue',
            ]),
            'order' => fake()->numberBetween(1, 10),
        ];
    }
}
