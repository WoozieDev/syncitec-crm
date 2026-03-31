<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectPayment>
 */
class ProjectPaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'payment_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'payment_method' => fake()->randomElement(['transferencia', 'tarjeta', 'efectivo', 'paypal']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
