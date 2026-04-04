<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectTasks\StoreProjectTaskRequest;
use App\Http\Requests\ProjectTasks\UpdateProjectTaskRequest;
use App\Models\Project;
use App\Models\ProjectModule;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();
        $projectId = $request->integer('project_id');
        $status = $request->string('status')->trim()->toString();
        $priority = $request->string('priority')->trim()->toString();

        $baseQuery = Task::query()
            ->with([
                'project:id,client_id,name,status,start_date,due_date',
                'project.client:id,name,company',
                'module:id,project_id,name,order',
            ])
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('priority', 'like', "%{$search}%")
                        ->orWhereHas('project', function (Builder $projectQuery) use ($search) {
                            $projectQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhereHas('client', function (Builder $clientQuery) use ($search) {
                                    $clientQuery
                                        ->where('name', 'like', "%{$search}%")
                                        ->orWhere('company', 'like', "%{$search}%");
                                });
                        })
                        ->orWhereHas('module', fn (Builder $moduleQuery) => $moduleQuery
                            ->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($projectId > 0, fn (Builder $query) => $query->where('project_id', $projectId))
            ->when($status !== '', fn (Builder $query) => $query->where('status', $status))
            ->when($priority !== '', fn (Builder $query) => $query->where('priority', $priority));

        $tasks = (clone $baseQuery)
            ->latest('created_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Task $task) => $this->taskListData($task));

        return Inertia::render('projectTasks/Index', [
            'tasks' => $tasks,
            'filters' => [
                'search' => $search,
                'project_id' => $projectId > 0 ? (string) $projectId : '',
                'status' => $status,
                'priority' => $priority,
            ],
            'overview' => [
                'total_tasks' => (clone $baseQuery)->count(),
                'pending' => (clone $baseQuery)->where('status', 'pendiente')->count(),
                'in_progress' => (clone $baseQuery)->where('status', 'en_progreso')->count(),
                'completed' => (clone $baseQuery)->where('status', 'completada')->count(),
            ],
            'projects' => $this->projectOptions(),
            'status_options' => $this->statusOptions(),
            'priority_options' => $this->priorityOptions(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        $selectedProjectId = $request->integer('project');

        return Inertia::render('projectTasks/Create', [
            'projects' => $this->projectOptions(),
            'selected_project_id' => $selectedProjectId > 0 ? $selectedProjectId : null,
            'status_options' => $this->statusOptions(),
            'priority_options' => $this->priorityOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectTaskRequest $request): RedirectResponse
    {
        Task::create($request->validated());

        return to_route('project-tasks.index')->with('success', 'Tarea creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $projectTask): Response
    {
        $projectTask->load([
            'project:id,client_id,name,status,start_date,due_date',
            'project.client:id,name,company,email,phone,country',
            'module:id,project_id,name,order',
        ]);

        return Inertia::render('projectTasks/Show', [
            'task' => $this->taskDetailData($projectTask),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $projectTask): Response
    {
        $projectTask->load([
            'project:id,client_id,name,status,start_date,due_date',
            'project.client:id,name,company,email,phone,country',
            'module:id,project_id,name,order',
        ]);

        return Inertia::render('projectTasks/Edit', [
            'task' => $this->taskDetailData($projectTask),
            'projects' => $this->projectOptions(),
            'selected_project_id' => $projectTask->project_id,
            'status_options' => $this->statusOptions(),
            'priority_options' => $this->priorityOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectTaskRequest $request, Task $projectTask): RedirectResponse
    {
        $projectTask->update($request->validated());

        return to_route('project-tasks.index')->with('success', 'Tarea actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $projectTask): RedirectResponse
    {
        $projectTask->delete();

        return to_route('project-tasks.index')->with('success', 'Tarea eliminada correctamente.');
    }

    /**
     * @return array<int, array{
     *     id: int,
     *     name: string,
     *     status: string,
     *     start_date: string|null,
     *     due_date: string|null,
     *     client: array{
     *         id: int|null,
     *         name: string|null,
     *         company: string|null,
     *         display_name: string
     *     },
     *     modules: array<int, array{id: int, project_id: int, name: string, order: int}>,
     *     label: string
     * }>
     */
    private function projectOptions(): array
    {
        return Project::query()
            ->with([
                'client:id,name,company',
                'modules' => fn ($query) => $query
                    ->orderBy('order')
                    ->orderBy('id'),
            ])
            ->orderBy('name')
            ->get(['id', 'client_id', 'name', 'status', 'start_date', 'due_date'])
            ->map(function (Project $project) {
                $clientDisplayName = $project->client?->company ?: $project->client?->name ?: 'Sin cliente';

                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'status' => (string) $project->status,
                    'start_date' => $project->start_date?->format('Y-m-d'),
                    'due_date' => $project->due_date?->format('Y-m-d'),
                    'client' => [
                        'id' => $project->client?->id,
                        'name' => $project->client?->name,
                        'company' => $project->client?->company,
                        'display_name' => $clientDisplayName,
                    ],
                    'modules' => $project->modules
                        ->map(fn (ProjectModule $module) => [
                            'id' => $module->id,
                            'project_id' => $module->project_id,
                            'name' => $module->name,
                            'order' => (int) $module->order,
                        ])
                        ->values()
                        ->all(),
                    'label' => "{$project->name} - {$clientDisplayName}",
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function statusOptions(): array
    {
        return [
            ['value' => 'pendiente', 'label' => 'Pendiente'],
            ['value' => 'en_progreso', 'label' => 'En progreso'],
            ['value' => 'en_revision', 'label' => 'En revision'],
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

    /**
     * @return array{
     *     id: int,
     *     project_id: int,
     *     module_id: int|null,
     *     title: string,
     *     description: string|null,
     *     status: string,
     *     priority: string|null,
     *     order: int,
     *     created_at: string|null,
     *     updated_at: string|null,
     *     project: array{
     *         id: int,
     *         name: string,
     *         status: string,
     *         start_date: string|null,
     *         due_date: string|null,
     *         client: array{
     *             id: int|null,
     *             name: string|null,
     *             company: string|null,
     *             display_name: string
     *         }
     *     }|null,
     *     module: array{id: int, project_id: int, name: string, order: int}|null
     * }
     */
    private function taskListData(Task $task): array
    {
        return [
            'id' => $task->id,
            'project_id' => $task->project_id,
            'module_id' => $task->module_id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => (string) $task->status,
            'priority' => $task->priority,
            'order' => (int) $task->order,
            'created_at' => $task->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $task->updated_at?->format('Y-m-d H:i:s'),
            'project' => $task->project
                ? [
                    'id' => $task->project->id,
                    'name' => $task->project->name,
                    'status' => (string) $task->project->status,
                    'start_date' => $task->project->start_date?->format('Y-m-d'),
                    'due_date' => $task->project->due_date?->format('Y-m-d'),
                    'client' => [
                        'id' => $task->project->client?->id,
                        'name' => $task->project->client?->name,
                        'company' => $task->project->client?->company,
                        'display_name' => $task->project->client?->company
                            ?: $task->project->client?->name
                            ?: 'Sin cliente',
                    ],
                ]
                : null,
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

    /**
     * @return array{
     *     id: int,
     *     project_id: int,
     *     module_id: int|null,
     *     title: string,
     *     description: string|null,
     *     status: string,
     *     priority: string|null,
     *     order: int,
     *     created_at: string|null,
     *     updated_at: string|null,
     *     project: array{
     *         id: int,
     *         name: string,
     *         status: string,
     *         start_date: string|null,
     *         due_date: string|null,
     *         client: array{
     *             id: int|null,
     *             name: string|null,
     *             company: string|null,
     *             email: string|null,
     *             phone: string|null,
     *             country: string|null,
     *             display_name: string
     *         }
     *     }|null,
     *     module: array{id: int, project_id: int, name: string, order: int}|null
     * }
     */
    private function taskDetailData(Task $task): array
    {
        $taskData = $this->taskListData($task);

        return [
            ...$taskData,
            'project' => $task->project
                ? [
                    'id' => $task->project->id,
                    'name' => $task->project->name,
                    'status' => (string) $task->project->status,
                    'start_date' => $task->project->start_date?->format('Y-m-d'),
                    'due_date' => $task->project->due_date?->format('Y-m-d'),
                    'client' => [
                        'id' => $task->project->client?->id,
                        'name' => $task->project->client?->name,
                        'company' => $task->project->client?->company,
                        'email' => $task->project->client?->email,
                        'phone' => $task->project->client?->phone,
                        'country' => $task->project->client?->country,
                        'display_name' => $task->project->client?->company
                            ?: $task->project->client?->name
                            ?: 'Sin cliente',
                    ],
                ]
                : null,
        ];
    }
}
