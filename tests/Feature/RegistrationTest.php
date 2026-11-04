<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the registration page is not available', function () {
    $this->get('/register')
        ->assertNotFound();
});

test('registration cannot be submitted', function () {
    $this->post('/register', [
        'name' => 'New User',
        'email' => 'new@example.com',
    ])->assertMethodNotAllowed();

    expect(User::where('email', 'new@example.com')->exists())->toBeFalse();
});
