<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\PersonalTaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectPaymentController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServicePaymentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::resource('clients', ClientController::class);
    Route::resource('personal-tasks', PersonalTaskController::class)->parameters([
        'personal-tasks' => 'personalTask',
    ]);
    Route::resource('projects', ProjectController::class);
    Route::resource('project-payments', ProjectPaymentController::class);
    Route::resource('project-tasks', ProjectTaskController::class)->parameters([
        'project-tasks' => 'projectTask',
    ]);
    Route::resource('services', ServiceController::class);
    Route::resource('service-payments', ServicePaymentController::class);
    Route::resource('users', UserController::class);
});

require __DIR__.'/settings.php';
