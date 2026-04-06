<?php

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectModule;
use App\Models\Task;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('an authenticated user can view a project kanban grouped by project modules', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $project = Project::factory()->for($client)->create();
    $planning = ProjectModule::factory()->for($project)->create([
        'name' => 'Planning',
        'order' => 1,
    ]);
    $execution = ProjectModule::factory()->for($project)->create([
        'name' => 'Execution',
        'order' => 2,
    ]);

    Task::factory()->for($project)->create([
        'module_id' => $planning->id,
        'title' => 'Legacy todo',
        'status' => 'todo',
        'order' => 1,
    ]);

    Task::factory()->for($project)->create([
        'module_id' => $planning->id,
        'title' => 'Legacy done',
        'status' => 'done',
        'order' => 2,
    ]);

    Task::factory()->for($project)->create([
        'module_id' => null,
        'title' => 'Task without column',
        'status' => 'pendiente',
        'order' => 3,
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('projects.kanban', [
            'project' => $project,
            'view' => 'board',
        ]));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('projects/Kanban')
            ->where('view', 'board')
            ->where('project.id', $project->id)
            ->where('project.overview.modules_total', 2)
            ->where('project.overview.unassigned_tasks', 1)
            ->has('board', 3)
            ->where('board.0.id', $planning->id)
            ->where('board.0.label', 'Planning')
            ->where('board.0.count', 2)
            ->where('board.1.id', $execution->id)
            ->where('board.1.label', 'Execution')
            ->where('board.1.count', 0)
            ->where('board.2.key', 'unassigned')
            ->where('board.2.count', 1)
            ->where('project.tasks.0.status', 'pendiente')
            ->where('project.tasks.1.status', 'completada')
            ->where('project.tasks.2.status', 'pendiente'));
});

test('storing a project task from kanban requires a column and returns to the kanban view', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $project = Project::factory()->for($client)->create();
    $module = ProjectModule::factory()->for($project)->create();

    $invalidResponse = $this
        ->actingAs($user)
        ->from(route('projects.kanban', ['project' => $project]))
        ->post(route('project-tasks.store', [
            'return_to' => 'kanban',
            'return_view' => 'list',
        ]), [
            'project_id' => $project->id,
            'title' => 'Task without column',
            'description' => 'Must fail because no module_id was sent',
            'status' => 'pendiente',
            'priority' => 'alta',
            'order' => 1,
        ]);

    $invalidResponse
        ->assertSessionHasErrors('module_id')
        ->assertRedirect(route('projects.kanban', ['project' => $project]));

    $validResponse = $this
        ->actingAs($user)
        ->post(route('project-tasks.store', [
            'return_to' => 'kanban',
            'return_view' => 'list',
        ]), [
            'project_id' => $project->id,
            'module_id' => $module->id,
            'title' => 'Nueva tarea kanban',
            'description' => 'Creada desde el flujo del kanban',
            'status' => 'done',
            'priority' => 'alta',
            'order' => 1,
        ]);

    $validResponse
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('projects.kanban', [
            'project' => $project,
            'view' => 'list',
        ]));

    $task = Task::query()->where('title', 'Nueva tarea kanban')->first();

    expect($task)->not->toBeNull();
    expect($task?->status)->toBe('completada');
});

test('a project module with tasks cannot be deleted', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for(Client::factory())->create();
    $module = ProjectModule::factory()->for($project)->create();

    Task::factory()->for($project)->create([
        'module_id' => $module->id,
        'status' => 'pendiente',
    ]);

    $response = $this
        ->actingAs($user)
        ->delete(route('projects.modules.destroy', [
            'project' => $project,
            'projectModule' => $module,
            'view' => 'board',
        ]));

    $response
        ->assertRedirect(route('projects.kanban', [
            'project' => $project,
            'view' => 'board',
        ]))
        ->assertSessionHas('error');

    expect($module->fresh())->not->toBeNull();
});

test('an empty project module can be deleted from kanban', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for(Client::factory())->create();
    $module = ProjectModule::factory()->for($project)->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('projects.modules.destroy', [
            'project' => $project,
            'projectModule' => $module,
            'view' => 'list',
        ]));

    $response
        ->assertRedirect(route('projects.kanban', [
            'project' => $project,
            'view' => 'list',
        ]));

    expect($module->fresh())->toBeNull();
});
