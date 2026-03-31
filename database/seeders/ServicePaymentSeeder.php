<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServicePayment;
use Illuminate\Database\Seeder;

class ServicePaymentSeeder extends Seeder
{
    public function run(): void
    {
        if (ServicePayment::query()->exists()) {
            return;
        }

        if (! Service::query()->exists()) {
            $this->call(ServiceSeeder::class);
        }

        Service::query()
            ->get()
            ->each(function (Service $service): void {
                ServicePayment::factory(fake()->numberBetween(1, 3))
                    ->for($service)
                    ->create([
                        'amount' => fake()->randomFloat(2, 25, max((float) $service->price, 25)),
                    ]);
            });
    }
}
