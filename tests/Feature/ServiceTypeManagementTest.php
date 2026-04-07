<?php

use App\Models\Service;
use App\Models\ServiceType;
use App\Models\User;

test('authenticated users can visit the service types index', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('serviceTypes.index'));

    $response->assertOk();
});

test('an authenticated user can create a service type', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('serviceTypes.store'), [
            'name' => 'Cloud Hosting',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('serviceTypes.index'));

    $serviceType = ServiceType::query()->where('name', 'Cloud Hosting')->first();

    expect($serviceType)->not->toBeNull();
});

test('an authenticated user can update a service type', function () {
    $user = User::factory()->create();
    $serviceType = ServiceType::factory()->create([
        'name' => 'Email Basico',
    ]);

    $response = $this
        ->actingAs($user)
        ->put(route('serviceTypes.update', $serviceType), [
            'name' => 'Email Empresarial',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('serviceTypes.index'));

    $serviceType->refresh();

    expect($serviceType->name)->toBe('Email Empresarial');
});

test('a service type with related services cannot be deleted', function () {
    $user = User::factory()->create();
    $serviceType = ServiceType::factory()->create();

    Service::factory()->create([
        'service_type_id' => $serviceType->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->delete(route('serviceTypes.destroy', $serviceType));

    $response
        ->assertRedirect(route('serviceTypes.index'))
        ->assertSessionHas('error');

    expect($serviceType->fresh())->not->toBeNull();
});
