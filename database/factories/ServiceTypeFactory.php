<?php

namespace Database\Factories;

use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceType>
 */
class ServiceTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Dominio',
                'Hosting',
                'Mantenimiento',
                'Consultoria',
                'Correo corporativo',
                'SSL',
            ]),
        ];
    }
}
