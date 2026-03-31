<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
		$statuses = ['draft', 'active', 'paused', 'completed', 'canceled'];

        $projectNames = [
            'Sistema de gestión interna',
            'Landing page corporativa',
            'Panel administrativo',
            'CRM personalizado',
            'Sistema de reservas',
            'Web institucional',
            'Sistema de ventas',
            'Dashboard de métricas',
        ];

		$startDate = fake()->optional(0.8)->dateTimeBetween('-6 months', '+1 month');
        $price = fake()->randomFloat(2, 500, 15000);

        return [
            'client_id' => Client::factory(),
            'name' => fake()->randomElement($projectNames),
            'description' => fake()->paragraphs(fake()->numberBetween(1, 3), true),
            'status' => fake()->randomElement($statuses),
            'price' => $price,
            'start_date' => $startDate?->format('Y-m-d'),
            'due_date' => $startDate?->modify(sprintf('+%d days', fake()->numberBetween(15, 120)))->format('Y-m-d'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
