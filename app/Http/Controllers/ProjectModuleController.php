<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectModules\StoreProjectModuleRequest;
use App\Http\Requests\ProjectModules\UpdateProjectModuleRequest;
use App\Models\Project;
use App\Models\ProjectModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectModuleController extends Controller
{
    public function store(StoreProjectModuleRequest $request, Project $project): RedirectResponse
    {
        $validated = $request->validated();

        ProjectModule::create([
            'project_id' => $project->id,
            'name' => $validated['name'],
            'order' => $validated['order'] ?? $this->nextOrder($project),
        ]);

        return $this->redirectToKanban($project, $request)
            ->with('success', 'Columna creada correctamente.');
    }

    public function update(
        UpdateProjectModuleRequest $request,
        Project $project,
        ProjectModule $projectModule,
    ): RedirectResponse {
        $this->ensureBelongsToProject($project, $projectModule);

        $validated = $request->validated();

        $projectModule->update([
            'name' => $validated['name'],
            'order' => $validated['order'] ?? $projectModule->order,
        ]);

        return $this->redirectToKanban($project, $request)
            ->with('success', 'Columna actualizada correctamente.');
    }

    public function destroy(
        Request $request,
        Project $project,
        ProjectModule $projectModule,
    ): RedirectResponse {
        $this->ensureBelongsToProject($project, $projectModule);

        if ($projectModule->tasks()->exists()) {
            return $this->redirectToKanban($project, $request)
                ->with('error', 'No puedes eliminar una columna que aun tiene tareas.');
        }

        $projectModule->delete();

        return $this->redirectToKanban($project, $request)
            ->with('success', 'Columna eliminada correctamente.');
    }

    private function ensureBelongsToProject(Project $project, ProjectModule $projectModule): void
    {
        abort_unless($projectModule->project_id === $project->id, 404);
    }

    private function nextOrder(Project $project): int
    {
        return (int) $project->modules()->max('order') + 1;
    }

    private function redirectToKanban(Project $project, Request $request): RedirectResponse
    {
        $view = $this->normalizeView($request->string('view')->trim()->toString());

        return redirect()->route('projects.kanban', [
            'project' => $project,
            'view' => $view,
        ]);
    }

    private function normalizeView(string $view): string
    {
        return in_array($view, ['board', 'list'], true) ? $view : 'board';
    }
}
