<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Provider;
use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        if (Service::query()->exists()) {
            return;
        }

        if (! Client::query()->exists()) {
            $this->call(ClientSeeder::class);
        }

        if (! ServiceType::query()->exists()) {
            $this->call(ServiceTypeSeeder::class);
        }

        if (! Provider::query()->exists()) {
            $this->call(ProviderSeeder::class);
        }

        $serviceTypeIds = ServiceType::query()->pluck('id')->all();
        $providerIds = Provider::query()->pluck('id')->all();

        Client::query()
            ->get()
            ->each(function (Client $client) use ($providerIds, $serviceTypeIds): void {
                Service::factory(fake()->numberBetween(1, 4))
                    ->for($client)
                    ->create([
                        'service_type_id' => fake()->randomElement($serviceTypeIds),
                        'provider_id' => fake()->boolean(75) ? fake()->randomElement($providerIds) : null,
                    ]);
            });
    }
}
