<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectModule;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class ProjectKanbanController extends Controller
{
    private const VIEWS = [
        'board' => 'Board',
        'list' => 'Lista',
    ];

    public function show(Request $request, Project $project): Response
    {
        $view = $this->normalizeView($request->string('view')->trim()->toString());

        $project->load([
            'client:id,name,company,email,phone,country',
            'modules' => fn ($query) => $query
                ->orderBy('order')
                ->orderBy('id'),
            'tasks' => fn ($query) => $query
                ->with('module:id,project_id,name,order')
                ->orderBy('order')
                ->orderBy('id'),
        ]);

        $tasks = $project->tasks
            ->map(fn (Task $task) => $this->taskData($task))
            ->values();

        $board = $project->modules
            ->map(function (ProjectModule $module) use ($tasks) {
                $moduleTasks = $tasks
                    ->where('module_id', $module->id)
                    ->values();

                return [
                    'id' => $module->id,
                    'key' => "module-{$module->id}",
                    'name' => $module->name,
                    'label' => $module->name,
                    'order' => (int) $module->order,
                    'count' => $moduleTasks->count(),
                    'tasks' => $moduleTasks->all(),
                    'is_unassigned' => false,
                    'can_delete' => $moduleTasks->isEmpty(),
                ];
            })
            ->values();

        $unassignedTasks = $tasks
            ->where('module_id', null)
            ->values();

        if ($unassignedTasks->isNotEmpty()) {
            $board->push([
                'id' => null,
                'key' => 'unassigned',
                'name' => 'Sin columna',
                'label' => 'Sin columna',
                'order' => null,
                'count' => $unassignedTasks->count(),
                'tasks' => $unassignedTasks->all(),
                'is_unassigned' => true,
                'can_delete' => false,
            ]);
        }

        return Inertia::render('projects/Kanban', [
            'project' => $this->projectData($project, $tasks),
            'view' => $view,
            'views' => collect(self::VIEWS)
                ->map(fn (string $label, string $key) => [
                    'key' => $key,
                    'label' => $label,
                ])
                ->values()
                ->all(),
            'board' => $board->all(),
            'status_options' => $this->statusOptions(),
            'priority_options' => $this->priorityOptions(),
        ]);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $tasks
     * @return array<string, mixed>
     */
    private function projectData(Project $project, $tasks): array
    {
        return [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'status' => (string) $project->status,
            'status_label' => $this->projectStatusLabel((string) $project->status),
            'status_tone' => $this->projectStatusTone((string) $project->status),
            'start_date' => $project->start_date?->format('Y-m-d'),
            'due_date' => $project->due_date?->format('Y-m-d'),
            'client' => [
                'id' => $project->client?->id,
                'name' => $project->client?->name,
                'company' => $project->client?->company,
                'email' => $project->client?->email,
                'phone' => $project->client?->phone,
                'country' => $project->client?->country,
                'display_name' => $project->client?->company ?: $project->client?->name ?: 'Sin cliente',
            ],
            'modules' => $project->modules
                ->map(fn (ProjectModule $module) => [
                    'id' => $module->id,
                    'name' => $module->name,
                    'order' => (int) $module->order,
                ])
                ->values()
                ->all(),
            'overview' => [
                'tasks_total' => $tasks->count(),
                'modules_total' => $project->modules->count(),
                'completed_tasks' => $tasks->where('status', 'completada')->count(),
                'active_tasks' => $tasks->where('status', '!=', 'completada')->count(),
                'unassigned_tasks' => $tasks->where('module_id', null)->count(),
            ],
            'tasks' => $tasks->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function taskData(Task $task): array
    {
        return [
            'id' => $task->id,
            'project_id' => $task->project_id,
            'module_id' => $task->module_id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => Task::normalizeStatus((string) $task->status),
            'status_label' => Task::statusLabel((string) $task->status),
            'is_completed' => Task::normalizeStatus((string) $task->status) === 'completada',
            'priority' => Task::normalizePriority($task->priority),
            'order' => (int) $task->order,
            'created_at' => $task->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $task->updated_at?->format('Y-m-d H:i:s'),
            'module' => $task->module
                ? [
                    'id' => $task->module->id,
                    'project_id' => $task->module->project_id,
                    'name' => $task->module->name,
                    'order' => (int) $task->module->order,
                ]
                : null,
        ];
    }

    private function normalizeView(string $view): string
    {
        return array_key_exists($view, self::VIEWS) ? $view : 'board';
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function statusOptions(): array
    {
        return [
            ['value' => 'pendiente', 'label' => 'Pendiente'],
            ['value' => 'completada', 'label' => 'Completada'],
        ];
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function priorityOptions(): array
    {
        return [
            ['value' => 'alta', 'label' => 'Alta'],
            ['value' => 'media', 'label' => 'Media'],
            ['value' => 'baja', 'label' => 'Baja'],
        ];
    }

    private function projectStatusLabel(string $status): string
    {
        return match ($status) {
            'draft' => 'Planeacion',
            'active' => 'En curso',
            'paused' => 'En revision',
            'completed' => 'Finalizado',
            'canceled' => 'Cancelado',
            default => ucfirst($status),
        };
    }

    private function projectStatusTone(string $status): string
    {
        return match ($status) {
            'active' => 'in_progress',
            'paused' => 'in_review',
            'completed' => 'done',
            'canceled' => 'canceled',
            default => 'planning',
        };
    }
}
