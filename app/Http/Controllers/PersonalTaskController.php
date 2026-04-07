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

        $tasks = PersonalTask::query()
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
            ->when($completion === 'completed', fn (Builder $query) => $query->whereNotNull('completed_at'))
            ->orderByRaw('CASE WHEN completed_at IS NULL THEN 0 ELSE 1 END')
            ->orderBy('due_date')
            ->orderBy('order')
            ->orderByDesc('id')
            ->get();

        $today = now()->startOfDay();
        $endOfWeek = $today->copy()->endOfWeek();
        $nextWeekStart = $endOfWeek->copy()->addDay()->startOfDay();
        $nextWeekEnd = $nextWeekStart->copy()->endOfWeek();

        $groupedSections = [
            $this->groupSection('overdue', 'Atrasadas', $tasks, $today, $endOfWeek, $nextWeekStart, $nextWeekEnd),
            $this->groupSection('today', 'Hoy', $tasks, $today, $endOfWeek, $nextWeekStart, $nextWeekEnd),
            $this->groupSection('this_week', 'Esta semana', $tasks, $today, $endOfWeek, $nextWeekStart, $nextWeekEnd),
            $this->groupSection('next_week', 'Próxima semana', $tasks, $today, $endOfWeek, $nextWeekStart, $nextWeekEnd),
            $this->groupSection('backlog', 'Backlog / Sin fecha', $tasks, $today, $endOfWeek, $nextWeekStart, $nextWeekEnd),
        ];

        $openTasks = $tasks->whereNull('completed_at')->values();
        $completedTasks = $tasks->whereNotNull('completed_at')->values();

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
                'overdue' => $this->groupTasks($tasks, 'overdue', $today, $endOfWeek, $nextWeekStart, $nextWeekEnd)->count(),
                'today' => $this->groupTasks($tasks, 'today', $today, $endOfWeek, $nextWeekStart, $nextWeekEnd)->count(),
                'this_week' => $this->groupTasks($tasks, 'this_week', $today, $endOfWeek, $nextWeekStart, $nextWeekEnd)->count(),
                'next_week' => $this->groupTasks($tasks, 'next_week', $today, $endOfWeek, $nextWeekStart, $nextWeekEnd)->count(),
                'backlog' => $this->groupTasks($tasks, 'backlog', $today, $endOfWeek, $nextWeekStart, $nextWeekEnd)->count(),
            ],
            'priority_options' => $this->priorityOptions(),
            'grouped_tasks' => $groupedSections,
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

    private function groupSection(
        string $key,
        string $label,
        Collection $tasks,
        CarbonInterface $today,
        CarbonInterface $endOfWeek,
        CarbonInterface $nextWeekStart,
        CarbonInterface $nextWeekEnd,
    ): array {
        $groupTasks = $this->groupTasks($tasks, $key, $today, $endOfWeek, $nextWeekStart, $nextWeekEnd);

        return [
            'key' => $key,
            'label' => $label,
            'count' => $groupTasks->count(),
            'tasks' => $groupTasks->map(fn (PersonalTask $task) => $this->taskData($task))->values()->all(),
        ];
    }

    private function groupTasks(
        Collection $tasks,
        string $group,
        CarbonInterface $today,
        CarbonInterface $endOfWeek,
        CarbonInterface $nextWeekStart,
        CarbonInterface $nextWeekEnd,
    ): Collection {
        return $tasks
            ->filter(function (PersonalTask $task) use ($group, $today, $endOfWeek, $nextWeekStart, $nextWeekEnd) {
                if ($group === 'backlog') {
                    return $task->due_date === null;
                }

                if ($task->due_date === null) {
                    return false;
                }

                $dueDate = $task->due_date->copy()->startOfDay();

                return match ($group) {
                    'overdue' => $dueDate->lessThan($today) && $task->completed_at === null,
                    'today' => $dueDate->equalTo($today),
                    'this_week' => $dueDate->greaterThan($today)
                        && $dueDate->lessThanOrEqualTo($endOfWeek)
                        && $task->completed_at === null,
                    'next_week' => $dueDate->greaterThanOrEqualTo($nextWeekStart) && $dueDate->lessThanOrEqualTo($nextWeekEnd),
                    default => false,
                };
            })
            ->sortBy(fn (PersonalTask $task) => sprintf(
                '%d-%d-%011d-%011d',
                $task->completed_at === null ? 0 : 1,
                $this->priorityRank($task->priority),
                $task->due_date?->timestamp ?? PHP_INT_MAX,
                $task->id,
            ))
            ->values();
    }

    private function priorityRank(?string $priority): int
    {
        return match (PersonalTask::normalizePriority($priority)) {
            'alta' => 0,
            'media' => 1,
            'baja' => 2,
            default => 3,
        };
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

        return ((int) $maxOrder) + 1;
    }
}
