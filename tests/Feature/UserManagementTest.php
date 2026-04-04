<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('authenticated users can visit the users index', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('users.index'));

    $response->assertOk();
});

test('an authenticated user can create a managed user', function () {
    $admin = User::factory()->create();

    $response = $this
        ->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo.usuario@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('users.index'));

    $createdUser = User::query()
        ->where('email', 'nuevo.usuario@example.com')
        ->first();

    expect($createdUser)->not->toBeNull();
    expect($createdUser?->name)->toBe('Nuevo Usuario');
    expect(Hash::check('password', (string) $createdUser?->password))->toBeTrue();
});

test('an authenticated user can update another managed user', function () {
    $admin = User::factory()->create();
    $managedUser = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this
        ->actingAs($admin)
        ->put(route('users.update', $managedUser), [
            'name' => 'Usuario Actualizado',
            'email' => 'actualizado@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('users.index'));

    $managedUser->refresh();

    expect($managedUser->name)->toBe('Usuario Actualizado');
    expect($managedUser->email)->toBe('actualizado@example.com');
    expect($managedUser->email_verified_at)->toBeNull();
    expect(Hash::check('new-password', $managedUser->password))->toBeTrue();
});

test('updating a user without password keeps the existing password', function () {
    $admin = User::factory()->create();
    $managedUser = User::factory()->create([
        'password' => 'original-password',
    ]);

    $response = $this
        ->actingAs($admin)
        ->put(route('users.update', $managedUser), [
            'name' => 'Usuario Sin Cambio De Clave',
            'email' => $managedUser->email,
            'password' => '',
            'password_confirmation' => '',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('users.index'));

    $managedUser->refresh();

    expect($managedUser->name)->toBe('Usuario Sin Cambio De Clave');
    expect(Hash::check('original-password', $managedUser->password))->toBeTrue();
});

test('an authenticated user cannot delete their own managed account from the users module', function () {
    $admin = User::factory()->create();
    User::factory()->create();

    $response = $this
        ->actingAs($admin)
        ->from(route('users.index'))
        ->delete(route('users.destroy', $admin));

    $response
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('error');

    expect($admin->fresh())->not->toBeNull();
});
