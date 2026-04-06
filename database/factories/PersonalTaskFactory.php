<?php

namespace Database\Factories;

use App\Models\PersonalTask;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PersonalTask>
 */
class PersonalTaskFactory extends Factory
{
    public function definition(): array
    {
        $dueDate = fake()->optional(0.8)->dateTimeBetween('-2 days', '+10 days');
        $isCompleted = fake()->boolean(20);

        return [
            'title' => fake()->sentence(fake()->numberBetween(3, 6)),
            'description' => fake()->optional()->randomElement([
                '<p>Definir el siguiente paso operativo.</p>',
                '<p><strong>Revisar</strong> pendientes del dia y bloquear distractores.</p>',
                '<ul><li>Confirmar avance</li><li>Actualizar contexto</li></ul>',
            ]),
            'status' => $isCompleted ? 'completada' : fake()->randomElement(['pendiente', 'en_progreso']),
            'priority' => fake()->optional(0.9)->randomElement(PersonalTask::PRIORITIES),
            'due_date' => $dueDate?->format('Y-m-d'),
            'completed_at' => $isCompleted ? Carbon::instance(fake()->dateTimeBetween('-5 days', 'now')) : null,
            'order' => fake()->numberBetween(10, 90),
        ];
    }
}
