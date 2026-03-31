<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ServiceTypeSeeder::class,
            ProviderSeeder::class,
            ClientSeeder::class,
            ProjectSeeder::class,
            ProjectModuleSeeder::class,
            TaskSeeder::class,
            ProjectPaymentSeeder::class,
            ServiceSeeder::class,
            ServicePaymentSeeder::class,
            PersonalTaskSeeder::class,
        ]);
    }
}
