<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonalTasks\StorePersonalTaskRequest;
use App\Http\Requests\PersonalTasks\UpdatePersonalTaskRequest;
use App\Models\PersonalTask;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
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
        $priority = $request->string('priority')->trim()->toString();
        $completion = $request->string('completion')->trim()->toString();

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
            ->when($priority !== '', fn (Builder $query) => $query->where('priority', PersonalTask::normalizePriority($priority)))
            ->when($completion === 'open', fn (Builder $query) => $query->whereNull('completed_at'))
            ->when($completion === 'completed', fn (Builder $query) => $query->whereNotNull('completed_at'));

        $tasks = (clone $baseQuery)
            ->orderByRaw('CASE WHEN completed_at IS NULL THEN 0 ELSE 1 END')
            ->orderBy('due_date')
            ->orderBy('order')
            ->orderByDesc('id')
            ->get();

        $today = now()->startOfDay();
        $endOfWeek = $today->copy()->endOfWeek();
        $nextWeekStart = $endOfWeek->copy()->addDay()->startOfDay();
        $nextWeekEnd = $nextWeekStart->copy()->endOfWeek();

        $openTasks = $tasks->whereNull('completed_at')->values();
        $completedTasks = $tasks
            ->whereNotNull('completed_at')
            ->sortByDesc(fn (PersonalTask $task) => $task->completed_at?->timestamp ?? 0)
            ->values();

        return Inertia::render('personalTasks/Index', [
            'filters' => [
                'search' => $search,
                'priority' => $priority,
                'completion' => $completion,
            ],
            'overview' => [
                'total_tasks' => $tasks->count(),
                'open_tasks' => $openTasks->count(),
                'completed' => $completedTasks->count(),
                'today' => $this->bucketTasks($openTasks, 'today', $today, $endOfWeek, $nextWeekStart, $nextWeekEnd)->count(),
                'this_week' => $this->bucketTasks($openTasks, 'this_week', $today, $endOfWeek, $nextWeekStart, $nextWeekEnd)->count(),
                'next_week' => $this->bucketTasks($openTasks, 'next_week', $today, $endOfWeek, $nextWeekStart, $nextWeekEnd)->count(),
                'backlog' => $this->bucketTasks($openTasks, 'backlog', $today, $endOfWeek, $nextWeekStart, $nextWeekEnd)->count(),
            ],
            'priority_options' => $this->priorityOptions(),
            'board' => [
                $this->boardColumn('today', 'Tareas hoy', $openTasks, $today, $endOfWeek, $nextWeekStart, $nextWeekEnd),
                $this->boardColumn('this_week', 'Esta semana', $openTasks, $today, $endOfWeek, $nextWeekStart, $nextWeekEnd),
                $this->boardColumn('next_week', 'Proxima semana', $openTasks, $today, $endOfWeek, $nextWeekStart, $nextWeekEnd),
            ],
            'backlog_tasks' => $this
                ->bucketTasks($openTasks, 'backlog', $today, $endOfWeek, $nextWeekStart, $nextWeekEnd)
                ->map(fn (PersonalTask $task) => $this->taskData($task))
                ->values()
                ->all(),
            'completed_tasks' => $completedTasks
                ->take(8)
                ->map(fn (PersonalTask $task) => $this->taskData($task))
                ->values()
                ->all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('personalTasks/Create', [
            'priority_options' => $this->priorityOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonalTaskRequest $request): RedirectResponse
    {
        $validated = $request->safe()->except(['is_completed']);
        $validated['order'] = $validated['order'] ?? $this->nextOrderForDueDate($validated['due_date'] ?? null);

        PersonalTask::create($validated);

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
        $validated = $request->safe()->except(['is_completed']);
        $validated['order'] = $validated['order']
            ?? (
                ($personalTask->due_date?->format('Y-m-d') === ($validated['due_date'] ?? null))
                    ? $personalTask->order
                    : $this->nextOrderForDueDate($validated['due_date'] ?? null, $personalTask->id)
            );

        $personalTask->update($validated);

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
     * Toggle completion status for the specified personal task.
     */
    public function completion(Request $request, PersonalTask $personalTask): RedirectResponse
    {
        $validated = $request->validate([
            'is_completed' => ['required', 'boolean'],
        ]);

        if ($validated['is_completed']) {
            $personalTask->markCompleted();
        } else {
            $personalTask->markOpen($personalTask->status);
        }

        return back()->with('success', 'Estado de la tarea actualizado.');
    }

    /**
     * Move a task between planner buckets.
     */
    public function move(Request $request, PersonalTask $personalTask): RedirectResponse
    {
        $validated = $request->validate([
            'bucket' => ['required', 'string', Rule::in(['today', 'this_week', 'next_week', 'backlog'])],
        ]);

        $bucket = $validated['bucket'];
        $today = now()->startOfDay();
        $endOfWeek = $today->copy()->endOfWeek();
        $nextWeekStart = $endOfWeek->copy()->addDay()->startOfDay();
        $thisWeekAnchor = $today->copy()->addDay();

        $dueDate = match ($bucket) {
            'today' => $today->toDateString(),
            'this_week' => ($thisWeekAnchor->lessThanOrEqualTo($endOfWeek) ? $thisWeekAnchor : $endOfWeek)->toDateString(),
            'next_week' => $nextWeekStart->toDateString(),
            default => null,
        };

        $personalTask->update([
            'due_date' => $dueDate,
            'order' => $this->nextOrderForDueDate($dueDate, $personalTask->id),
        ]);

        return back()->with('success', 'La tarea fue movida al panel seleccionado.');
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
     *     due_date: string|null,
     *     completed_at: string|null,
     *     is_completed: bool,
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
            'status' => PersonalTask::normalizeStatus($task->status),
            'priority' => PersonalTask::normalizePriority($task->priority),
            'due_date' => $task->due_date?->format('Y-m-d'),
            'completed_at' => $task->completed_at?->format('Y-m-d H:i:s'),
            'is_completed' => $task->completed_at !== null,
            'order' => (int) $task->order,
            'created_at' => $task->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $task->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function boardColumn(
        string $key,
        string $label,
        Collection $tasks,
        CarbonInterface $today,
        CarbonInterface $endOfWeek,
        CarbonInterface $nextWeekStart,
        CarbonInterface $nextWeekEnd,
    ): array {
        $bucketTasks = $this->bucketTasks($tasks, $key, $today, $endOfWeek, $nextWeekStart, $nextWeekEnd);

        return [
            'key' => $key,
            'label' => $label,
            'count' => $bucketTasks->count(),
            'tasks' => $bucketTasks->map(fn (PersonalTask $task) => $this->taskData($task))->values()->all(),
        ];
    }

    private function bucketTasks(
        Collection $tasks,
        string $bucket,
        CarbonInterface $today,
        CarbonInterface $endOfWeek,
        CarbonInterface $nextWeekStart,
        CarbonInterface $nextWeekEnd,
    ): Collection {
        return $tasks
            ->filter(function (PersonalTask $task) use ($bucket, $today, $endOfWeek, $nextWeekStart, $nextWeekEnd) {
                if ($task->due_date === null) {
                    return $bucket === 'backlog';
                }

                $dueDate = $task->due_date->copy()->startOfDay();

                return match ($bucket) {
                    'today' => $dueDate->equalTo($today),
                    'this_week' => $dueDate->greaterThan($today) && $dueDate->lessThanOrEqualTo($endOfWeek),
                    'next_week' => $dueDate->greaterThanOrEqualTo($nextWeekStart) && $dueDate->lessThanOrEqualTo($nextWeekEnd),
                    'backlog' => $dueDate->greaterThan($nextWeekEnd),
                    default => false,
                };
            })
            ->sortBy(fn (PersonalTask $task) => sprintf(
                '%011d-%011d-%011d',
                $task->order,
                $task->due_date?->timestamp ?? PHP_INT_MAX,
                $task->id,
            ))
            ->values();
    }

    private function nextOrderForDueDate(?string $dueDate, ?int $ignoreId = null): int
    {
        $maxOrder = PersonalTask::query()
            ->whereNull('completed_at')
            ->when($ignoreId !== null, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->when(
                $dueDate === null,
                fn (Builder $query) => $query->whereNull('due_date'),
                fn (Builder $query) => $query->whereDate('due_date', $dueDate),
            )
            ->max('order');

        return ((int) $maxOrder) + 10;
    }
}
