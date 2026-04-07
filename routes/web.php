<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PersonalTaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectKanbanController;
use App\Http\Controllers\ProjectModuleController;
use App\Http\Controllers\ProjectPaymentController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\ServicePaymentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('clients', ClientController::class);
    Route::patch('personal-tasks/{personalTask}/completion', [PersonalTaskController::class, 'completion'])
        ->name('personal-tasks.completion');
    Route::patch('personal-tasks/{personalTask}/move', [PersonalTaskController::class, 'move'])
        ->name('personal-tasks.move');
    Route::resource('personal-tasks', PersonalTaskController::class)->parameters([
        'personal-tasks' => 'personalTask',
    ]);
    Route::resource('projects', ProjectController::class);
    Route::get('projects/{project}/kanban', [ProjectKanbanController::class, 'show'])
        ->name('projects.kanban');
    Route::post('projects/{project}/modules', [ProjectModuleController::class, 'store'])
        ->name('projects.modules.store');
    Route::put('projects/{project}/modules/{projectModule}', [ProjectModuleController::class, 'update'])
        ->name('projects.modules.update');
    Route::delete('projects/{project}/modules/{projectModule}', [ProjectModuleController::class, 'destroy'])
        ->name('projects.modules.destroy');
    Route::resource('project-payments', ProjectPaymentController::class);
    Route::post('project-tasks', [ProjectTaskController::class, 'store'])
        ->name('project-tasks.store');
    Route::put('project-tasks/{projectTask}', [ProjectTaskController::class, 'update'])
        ->name('project-tasks.update');
    Route::patch('project-tasks/{projectTask}', [ProjectTaskController::class, 'update']);
    Route::delete('project-tasks/{projectTask}', [ProjectTaskController::class, 'destroy'])
        ->name('project-tasks.destroy');
    Route::resource('services', ServiceController::class);
    Route::resource('serviceTypes', ServiceTypeController::class);
    Route::resource('service-payments', ServicePaymentController::class);
    Route::resource('users', UserController::class);
});

require __DIR__.'/settings.php';
