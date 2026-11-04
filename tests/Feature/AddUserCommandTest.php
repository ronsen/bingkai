<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user can be added from the command line', function () {
    $this->artisan('user:add')
        ->expectsQuestion('Name', 'Jane Doe')
        ->expectsQuestion('Email address', 'jane@example.com')
        ->expectsOutput('User jane@example.com created successfully.')
        ->assertSuccessful();

    $user = User::where('email', 'jane@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Jane Doe')
        ->and($user->password)->toBeNull();
});

test('the command rejects a duplicate email address', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $this->artisan('user:add')
        ->expectsQuestion('Name', 'Jane Doe')
        ->expectsQuestion('Email address', 'existing@example.com')
        ->expectsOutput('The email has already been taken.')
        ->assertExitCode(1);

    expect(User::where('email', 'existing@example.com')->count())->toBe(1);
});
