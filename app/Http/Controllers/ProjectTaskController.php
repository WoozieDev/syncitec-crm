<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectTasks\StoreProjectTaskRequest;
use App\Http\Requests\ProjectTasks\UpdateProjectTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
    public function store(StoreProjectTaskRequest $request): RedirectResponse
    {
        $task = Task::create($request->validated());

        return $this->redirectAfterMutation($request, $task, 'Tarea creada correctamente.');
    }

    public function update(UpdateProjectTaskRequest $request, Task $projectTask): RedirectResponse
    {
        $projectTask->update($request->validated());

        return $this->redirectAfterMutation($request, $projectTask, 'Tarea actualizada correctamente.');
    }

    public function destroy(Request $request, Task $projectTask): RedirectResponse
    {
        $projectTask->delete();

        return $this->redirectAfterMutation($request, $projectTask, 'Tarea eliminada correctamente.');
    }

    private function normalizeReturnTo(string $returnTo): ?string
    {
        return $returnTo === 'kanban' ? 'kanban' : null;
    }

    private function normalizeReturnView(string $returnView): string
    {
        return in_array($returnView, ['board', 'list'], true) ? $returnView : 'board';
    }

    private function resolveTaskReturnHref(?int $projectId, ?string $returnTo, string $returnView): string
    {
        if ($returnTo === 'kanban' && $projectId) {
            return route('projects.kanban', [
                'project' => $projectId,
                'view' => $returnView,
            ]);
        }

        if ($projectId) {
            return route('projects.show', ['project' => $projectId]);
        }

        return route('projects.index');
    }

    private function redirectAfterMutation(Request $request, Task $task, string $message): RedirectResponse
    {
        return redirect()
            ->to($this->resolveTaskReturnHref(
                $task->project_id,
                $this->normalizeReturnTo($request->string('return_to')->trim()->toString()),
                $this->normalizeReturnView($request->string('return_view')->trim()->toString()),
            ))
            ->with('success', $message);
    }
}
