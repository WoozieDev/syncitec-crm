<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'module_id' => null,
            'title' => fake()->sentence(fake()->numberBetween(3, 6)),
            'description' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(Task::STATUSES),
            'priority' => fake()->optional(0.85)->randomElement(Task::PRIORITIES),
            'order' => fake()->numberBetween(1, 20),
        ];
    }
}
