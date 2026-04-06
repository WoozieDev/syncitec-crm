<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PersonalTask;
use App\Models\Project;
use App\Models\ProjectPayment;
use App\Models\Service;
use App\Models\ServicePayment;
use App\Models\Task;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $today = Carbon::today();
        $projectAlertLimit = $today->copy()->addDays(7);
        $serviceAlertLimit = $today->copy()->addDays(15);

        $projectBilledTotal = (float) Project::query()->sum('price');
        $projectCollectedTotal = (float) ProjectPayment::query()->sum('amount');
        $serviceBilledTotal = (float) Service::query()->sum('price');
        $serviceCollectedTotal = (float) ServicePayment::query()->sum('amount');

        return Inertia::render('Dashboard', [
            'summary' => [
                'total_clients' => Client::query()->count(),
                'total_projects' => Project::query()->count(),
                'projects_active' => Project::query()->where('status', 'active')->count(),
                'projects_in_review' => Project::query()->where('status', 'paused')->count(),
                'projects_completed' => Project::query()->where('status', 'completed')->count(),
                'total_services' => Service::query()->count(),
                'services_active' => Service::query()->where('status', 'activo')->count(),
            ],
            'financial' => [
                'projects_billed' => $projectBilledTotal,
                'projects_collected' => $projectCollectedTotal,
                'projects_pending' => max(0, $projectBilledTotal - $projectCollectedTotal),
                'services_billed' => $serviceBilledTotal,
                'services_collected' => $serviceCollectedTotal,
                'services_pending' => max(0, $serviceBilledTotal - $serviceCollectedTotal),
            ],
            'projects' => Project::query()
                ->with('client:id,name,company')
                ->latest('created_at')
                ->limit(6)
                ->get(['id', 'client_id', 'name', 'status', 'price', 'due_date'])
                ->map(fn (Project $project) => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'status' => (string) $project->status,
                    'status_label' => $this->projectStatusLabel((string) $project->status),
                    'price' => (float) $project->price,
                    'due_date' => $project->due_date?->format('Y-m-d'),
                    'client' => $project->client?->company ?: $project->client?->name ?: 'Sin cliente',
                ])
                ->values()
                ->all(),
            'services' => Service::query()
                ->with('client:id,name,company')
                ->whereNotNull('next_renewal_date')
                ->orderBy('next_renewal_date')
                ->limit(6)
                ->get(['id', 'client_id', 'name', 'status', 'next_renewal_date'])
                ->map(fn (Service $service) => [
                    'id' => $service->id,
                    'name' => $service->name,
                    'status' => (string) $service->status,
                    'status_label' => $this->serviceStatusLabel((string) $service->status),
                    'next_renewal_date' => $service->next_renewal_date?->format('Y-m-d'),
                    'client' => $service->client?->company ?: $service->client?->name ?: 'Sin cliente',
                ])
                ->values()
                ->all(),
            'tasks' => [
                'pending' => PersonalTask::query()->where('status', 'pendiente')->count()
                    + Task::query()->whereIn('status', Task::pendingDatabaseStatuses())->count(),
                'in_progress' => PersonalTask::query()->where('status', 'en_progreso')->count(),
                'completed' => PersonalTask::query()->where('status', 'completada')->count()
                    + Task::query()->whereIn('status', Task::completedDatabaseStatuses())->count(),
                'priority_items' => $this->priorityTasks(),
            ],
            'alerts' => [
                'projects_due_soon' => Project::query()
                    ->with('client:id,name,company')
                    ->whereIn('status', ['draft', 'active', 'paused'])
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '>=', $today->toDateString())
                    ->whereDate('due_date', '<=', $projectAlertLimit->toDateString())
                    ->orderBy('due_date')
                    ->limit(5)
                    ->get(['id', 'client_id', 'name', 'status', 'due_date'])
                    ->map(fn (Project $project) => [
                        'id' => $project->id,
                        'name' => $project->name,
                        'date' => $project->due_date?->format('Y-m-d'),
                        'client' => $project->client?->company ?: $project->client?->name ?: 'Sin cliente',
                    ])
                    ->values()
                    ->all(),
                'services_renewing_soon' => Service::query()
                    ->with('client:id,name,company')
                    ->whereIn('status', ['activo', 'pendiente'])
                    ->whereNotNull('next_renewal_date')
                    ->whereDate('next_renewal_date', '>=', $today->toDateString())
                    ->whereDate('next_renewal_date', '<=', $serviceAlertLimit->toDateString())
                    ->orderBy('next_renewal_date')
                    ->limit(5)
                    ->get(['id', 'client_id', 'name', 'status', 'next_renewal_date'])
                    ->map(fn (Service $service) => [
                        'id' => $service->id,
                        'name' => $service->name,
                        'date' => $service->next_renewal_date?->format('Y-m-d'),
                        'client' => $service->client?->company ?: $service->client?->name ?: 'Sin cliente',
                    ])
                    ->values()
                    ->all(),
            ],
        ]);
    }

    /**
     * @return array<int, array{id: int, title: string, priority: string|null, source: string, project: string|null, created_at: string|null}>
     */
    private function priorityTasks(): array
    {
        $personalTasks = PersonalTask::query()
            ->where('priority', 'alta')
            ->where('status', '!=', 'completada')
            ->latest('created_at')
            ->limit(5)
            ->get(['id', 'title', 'priority', 'created_at'])
            ->map(fn (PersonalTask $task) => [
                'id' => $task->id,
                'title' => $task->title,
                'priority' => $task->priority,
                'source' => 'personal',
                'project' => null,
                'created_at' => $task->created_at?->format('Y-m-d H:i:s'),
            ]);

        $projectTasks = Task::query()
            ->with('project:id,name')
            ->whereIn('priority', Task::databasePrioritiesForPriority('alta'))
            ->whereNotIn('status', Task::completedDatabaseStatuses())
            ->latest('created_at')
            ->limit(5)
            ->get(['id', 'project_id', 'title', 'priority', 'created_at'])
            ->map(fn (Task $task) => [
                'id' => $task->id,
                'title' => $task->title,
                'priority' => $task->priority,
                'source' => 'proyecto',
                'project' => $task->project?->name,
                'created_at' => $task->created_at?->format('Y-m-d H:i:s'),
            ]);

        return collect($personalTasks->all())
            ->merge($projectTasks->all())
            ->sortByDesc('created_at')
            ->take(6)
            ->values()
            ->all();
    }

    private function projectStatusLabel(string $status): string
    {
        return match ($status) {
            'draft' => 'Planeacion',
            'active' => 'Activo',
            'paused' => 'En revision',
            'completed' => 'Finalizado',
            'canceled' => 'Cancelado',
            default => ucfirst($status),
        };
    }

    private function serviceStatusLabel(string $status): string
    {
        return match ($status) {
            'activo' => 'Activo',
            'pendiente' => 'Pendiente',
            'suspendido' => 'Suspendido',
            'cancelado' => 'Cancelado',
            default => ucfirst($status),
        };
    }
}
