<?php

namespace Database\Seeders;

use App\Models\PersonalTask;
use Illuminate\Database\Seeder;

class PersonalTaskSeeder extends Seeder
{
    public function run(): void
    {
        if (PersonalTask::query()->exists()) {
            return;
        }

        $today = now()->toDateString();
        $thisWeek = now()->addDays(2)->toDateString();
        $nextWeek = now()->addWeek()->startOfWeek()->toDateString();

        collect([
            [
                'title' => 'Revisar pendientes criticos del CRM',
                'description' => '<p>Prioriza lo que desbloquea ventas y operacion.</p>',
                'status' => 'en_progreso',
                'priority' => 'alta',
                'due_date' => $today,
                'order' => 10,
            ],
            [
                'title' => 'Preparar seguimiento comercial',
                'description' => '<ul><li>Validar clientes calientes</li><li>Definir siguiente contacto</li></ul>',
                'status' => 'pendiente',
                'priority' => 'media',
                'due_date' => $today,
                'order' => 20,
            ],
            [
                'title' => 'Actualizar notas operativas semanales',
                'description' => '<p>Dejar contexto claro para no reabrir conversaciones.</p>',
                'status' => 'pendiente',
                'priority' => 'baja',
                'due_date' => $thisWeek,
                'order' => 30,
            ],
            [
                'title' => 'Planear bloque de enfoque de la proxima semana',
                'description' => '<p>Reserva tiempo para tareas de alto impacto.</p>',
                'status' => 'pendiente',
                'priority' => 'alta',
                'due_date' => $nextWeek,
                'order' => 40,
            ],
            [
                'title' => 'Vaciar backlog personal',
                'description' => '<p>Agrupa ideas y deja solo lo accionable.</p>',
                'status' => 'pendiente',
                'priority' => 'media',
                'due_date' => null,
                'order' => 50,
            ],
            [
                'title' => 'Cerrar reporte diario',
                'description' => '<p><strong>Completada</strong> con notas de seguimiento.</p>',
                'status' => 'completada',
                'priority' => 'baja',
                'due_date' => now()->subDay()->toDateString(),
                'completed_at' => now()->subHours(6),
                'order' => 60,
            ],
        ])->each(fn (array $task) => PersonalTask::query()->create($task));

        PersonalTask::factory(4)->create();
    }
}
