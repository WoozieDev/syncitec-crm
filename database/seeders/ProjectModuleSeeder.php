<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectModule;
use Illuminate\Database\Seeder;

class ProjectModuleSeeder extends Seeder
{
    public function run(): void
    {
        if (ProjectModule::query()->exists()) {
            return;
        }

        if (! Project::query()->exists()) {
            $this->call(ProjectSeeder::class);
        }

        Project::query()
            ->get()
            ->each(function (Project $project): void {
                $modules = [
                    'Descubrimiento',
                    'Diseno',
                    'Desarrollo',
                    'QA',
                ];

                foreach ($modules as $index => $name) {
                    ProjectModule::factory()
                        ->for($project)
                        ->create([
                            'name' => $name,
                            'order' => $index + 1,
                        ]);
                }
            });
    }
}
