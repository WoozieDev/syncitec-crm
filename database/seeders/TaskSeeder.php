<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectModule;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        if (Task::query()->exists()) {
            return;
        }

        if (! Project::query()->exists()) {
            $this->call(ProjectSeeder::class);
        }

        if (! ProjectModule::query()->exists()) {
            $this->call(ProjectModuleSeeder::class);
        }

        Project::query()
            ->with('modules')
            ->get()
            ->each(function (Project $project): void {
                foreach ($project->modules as $index => $module) {
                    $tasksCount = fake()->numberBetween(2, 4);

                    for ($taskOrder = 1; $taskOrder <= $tasksCount; $taskOrder++) {
                        Task::factory()
                            ->create([
                                'project_id' => $project->id,
                                'module_id' => $module->id,
                                'order' => ($index * 10) + $taskOrder,
                            ]);
                    }
                }

                $generalTasksCount = fake()->numberBetween(1, 3);

                for ($taskOrder = 1; $taskOrder <= $generalTasksCount; $taskOrder++) {
                    Task::factory()
                        ->create([
                            'project_id' => $project->id,
                            'module_id' => null,
                            'order' => (count($project->modules) * 10) + $taskOrder,
                        ]);
                }
            });
    }
}
