<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonalTasks\StorePersonalTaskRequest;
use App\Http\Requests\PersonalTasks\UpdatePersonalTaskRequest;
use App\Models\PersonalTask;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PersonalTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->trim()->toString();
        $priority = $request->string('priority')->trim()->toString();

        $baseQuery = PersonalTask::query()
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('priority', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn (Builder $query) => $query->where('status', $status))
            ->when($priority !== '', fn (Builder $query) => $query->where('priority', $priority));

        $tasks = (clone $baseQuery)
            ->latest('created_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (PersonalTask $task) => $this->taskData($task));

        return Inertia::render('personalTasks/Index', [
            'tasks' => $tasks,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'priority' => $priority,
            ],
            'overview' => [
                'total_tasks' => (clone $baseQuery)->count(),
                'pending' => (clone $baseQuery)->where('status', 'pendiente')->count(),
                'in_progress' => (clone $baseQuery)->where('status', 'en_progreso')->count(),
                'completed' => (clone $baseQuery)->where('status', 'completada')->count(),
            ],
            'status_options' => $this->statusOptions(),
            'priority_options' => $this->priorityOptions(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('personalTasks/Create', [
            'status_options' => $this->statusOptions(),
            'priority_options' => $this->priorityOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonalTaskRequest $request): RedirectResponse
    {
        PersonalTask::create($request->validated());

        return to_route('personal-tasks.index')->with('success', 'Tarea personal creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PersonalTask $personalTask): Response
    {
        return Inertia::render('personalTasks/Show', [
            'task' => $this->taskData($personalTask),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PersonalTask $personalTask): Response
    {
        return Inertia::render('personalTasks/Edit', [
            'task' => $this->taskData($personalTask),
            'status_options' => $this->statusOptions(),
            'priority_options' => $this->priorityOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdatePersonalTaskRequest $request,
        PersonalTask $personalTask
    ): RedirectResponse {
        $personalTask->update($request->validated());

        return to_route('personal-tasks.index')->with('success', 'Tarea personal actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PersonalTask $personalTask): RedirectResponse
    {
        $personalTask->delete();

        return to_route('personal-tasks.index')->with('success', 'Tarea personal eliminada correctamente.');
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
     *     title: string,
     *     description: string|null,
     *     status: string,
     *     priority: string|null,
     *     order: int,
     *     created_at: string|null,
     *     updated_at: string|null
     * }
     */
    private function taskData(PersonalTask $task): array
    {
        return [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => (string) $task->status,
            'priority' => $task->priority,
            'order' => (int) $task->order,
            'created_at' => $task->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $task->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
