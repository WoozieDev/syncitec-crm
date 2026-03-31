<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectPayment;
use Illuminate\Database\Seeder;

class ProjectPaymentSeeder extends Seeder
{
    public function run(): void
    {
        if (ProjectPayment::query()->exists()) {
            return;
        }

        if (! Project::query()->exists()) {
            $this->call(ProjectSeeder::class);
        }

        Project::query()
            ->get()
            ->each(function (Project $project): void {
                ProjectPayment::factory(fake()->numberBetween(1, 3))
                    ->for($project)
                    ->create([
                        'amount' => fake()->randomFloat(2, 100, max((float) $project->price, 100)),
                    ]);
            });
    }
}
