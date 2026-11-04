<?php

use App\Enums\Role;
use App\Models\User;
use App\Notifications\MagicLinkNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('the registration page displays', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee('Create your account')
        ->assertSee('Sign in');
});

test('a passwordless user can register and receives a magic link', function () {
    Notification::fake();

    $this->post(route('register.store'), [
        'name' => 'New User',
        'email' => 'new@example.com',
    ])
        ->assertRedirect(route('login'))
        ->assertSessionHas('status');

    $user = User::where('email', 'new@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->password)->toBeNull()
        ->and($user->role)->toBe(Role::User);

    Notification::assertSentTo($user, MagicLinkNotification::class);
});

test('registration requires a unique email address', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $this->post(route('register.store'), [
        'name' => 'Another User',
        'email' => 'existing@example.com',
    ])->assertSessionHasErrors('email');
});
