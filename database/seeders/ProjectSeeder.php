<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        if (Project::query()->exists()) {
            return;
        }

        if (! Client::query()->exists()) {
            $this->call(ClientSeeder::class);
        }

        Client::query()
            ->get()
            ->each(fn (Client $client) => Project::factory(fake()->numberBetween(1, 3))
                ->for($client)
                ->create());
    }
}
