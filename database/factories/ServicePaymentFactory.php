<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServicePayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServicePayment>
 */
class ServicePaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'amount' => fake()->randomFloat(2, 25, 2500),
            'payment_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'payment_method' => fake()->randomElement(['transferencia', 'tarjeta', 'efectivo', 'paypal']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
