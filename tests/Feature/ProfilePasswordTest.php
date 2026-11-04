<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('a passwordless user can set a password without providing a current password', function () {
    $user = User::factory()->create(['password' => null]);

    $this->actingAs($user)
        ->put(route('profile.password.update'), [
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHasNoErrors();

    expect(Hash::check('new-password-123', $user->fresh()->password))->toBeTrue();
});

test('a user with an existing password must provide the current password', function () {
    $user = User::factory()->create(['password' => 'old-password-123']);

    $this->actingAs($user)
        ->put(route('profile.password.update'), [
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertSessionHasErrors('current_password');
});
