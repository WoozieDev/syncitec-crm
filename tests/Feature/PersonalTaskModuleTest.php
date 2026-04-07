<?php

use App\Models\PersonalTask;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('personal task index groups tasks into vertical sections', function () {
    $user = User::factory()->create();

    PersonalTask::factory()->create([
        'title' => 'Hoy',
        'due_date' => now()->toDateString(),
        'completed_at' => null,
        'status' => 'pendiente',
        'order' => 10,
    ]);

    PersonalTask::factory()->create([
        'title' => 'Semana',
        'due_date' => now()->addDays(2)->toDateString(),
        'completed_at' => null,
        'status' => 'pendiente',
        'order' => 20,
    ]);

    PersonalTask::factory()->create([
        'title' => 'Proxima',
        'due_date' => now()->addWeek()->startOfWeek()->toDateString(),
        'completed_at' => null,
        'status' => 'pendiente',
        'order' => 30,
    ]);

    PersonalTask::factory()->create([
        'title' => 'Sin fecha',
        'due_date' => null,
        'completed_at' => null,
        'status' => 'pendiente',
        'order' => 40,
    ]);

    PersonalTask::factory()->create([
        'title' => 'Terminada',
        'due_date' => now()->subDay()->toDateString(),
        'completed_at' => now(),
        'status' => 'completada',
        'order' => 50,
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('personal-tasks.index'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('personalTasks/Index')
            ->has('grouped_tasks', 5)
            ->where('grouped_tasks.0.key', 'overdue')
            ->where('grouped_tasks.0.count', 0)
            ->where('grouped_tasks.1.key', 'today')
            ->where('grouped_tasks.1.count', 1)
            ->where('grouped_tasks.2.key', 'this_week')
            ->where('grouped_tasks.2.count', 1)
            ->where('grouped_tasks.3.key', 'next_week')
            ->where('grouped_tasks.3.count', 1)
            ->where('grouped_tasks.4.key', 'backlog')
            ->where('grouped_tasks.4.count', 1)
            ->where('overview.total_tasks', 5)
            ->where('overview.open_tasks', 4)
            ->where('overview.completed', 1)
            ->where('overview.overdue', 0));
});

test('storing a personal task sanitizes rich text and marks it complete when requested', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('personal-tasks.store'), [
            'title' => 'Cerrar pendientes',
            'description' => '<script>alert(1)</script><p><strong>Checklist</strong></p>',
            'status' => 'pendiente',
            'priority' => 'alta',
            'due_date' => now()->toDateString(),
            'is_completed' => true,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('personal-tasks.index'));

    $task = PersonalTask::query()->where('title', 'Cerrar pendientes')->first();

    expect($task)->not->toBeNull();
    expect($task?->status)->toBe('completada');
    expect($task?->completed_at)->not->toBeNull();
    expect($task?->description)->toBe('<p><strong>Checklist</strong></p>');
});

test('a personal task can be moved to another planner bucket', function () {
    $user = User::factory()->create();
    $task = PersonalTask::factory()->create([
        'due_date' => now()->toDateString(),
        'completed_at' => null,
        'status' => 'pendiente',
        'order' => 10,
    ]);

    $response = $this
        ->actingAs($user)
        ->patch(route('personal-tasks.move', $task), [
            'bucket' => 'next_week',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $task->refresh();

    expect($task->due_date?->isSameDay(now()->addWeek()->startOfWeek()))->toBeTrue();
});
