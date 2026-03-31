<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Provider;
use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $billingType = fake()->randomElement(['unico', 'recurrente']);
        $billingCycle = $billingType === 'recurrente'
            ? fake()->randomElement(['mensual', 'trimestral', 'semestral', 'anual'])
            : null;
        $startDate = fake()->optional(0.85)->dateTimeBetween('-1 year', 'now');
        $price = fake()->randomFloat(2, 50, 5000);
        $cost = fake()->optional(0.8)->randomFloat(2, 10, max($price - 5, 10));

        return [
            'client_id' => Client::factory(),
            'service_type_id' => ServiceType::factory(),
            'provider_id' => fake()->boolean(70) ? Provider::factory() : null,
            'name' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'billing_type' => $billingType,
            'billing_cycle' => $billingCycle,
            'price' => $price,
            'cost' => $cost,
            'start_date' => $startDate?->format('Y-m-d'),
            'next_renewal_date' => $billingCycle && $startDate
                ? $this->renewalDateForCycle(clone $startDate, $billingCycle)
                : null,
            'status' => fake()->randomElement(['activo', 'pendiente', 'suspendido', 'cancelado']),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    private function renewalDateForCycle(\DateTime $startDate, string $billingCycle): string
    {
        $interval = match ($billingCycle) {
            'mensual' => '+1 month',
            'trimestral' => '+3 months',
            'semestral' => '+6 months',
            'anual' => '+1 year',
        };

        return $startDate->modify($interval)->format('Y-m-d');
    }
}
