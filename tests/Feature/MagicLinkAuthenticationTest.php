<?php

use App\Models\User;
use App\Notifications\MagicLinkNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('the login page displays the magic link form', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Email Me a Sign-In Link')
        ->assertSee('Create one');
});

test('a magic link is sent to an existing user', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('login.store'), ['email' => $user->email])
        ->assertRedirect(route('login'))
        ->assertSessionHas('status');

    Notification::assertSentTo($user, MagicLinkNotification::class);
});

test('a magic link is not sent to an unknown email address', function () {
    Notification::fake();

    $this->post(route('login.store'), ['email' => 'nobody@example.com'])
        ->assertRedirect(route('login'))
        ->assertSessionHas('status');

    Notification::assertNothingSent();
});

test('a valid magic link signs the user in and can only be used once', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();

    $this->post(route('login.store'), ['email' => $user->email]);

    $url = null;

    Notification::assertSentTo(
        $user,
        MagicLinkNotification::class,
        function (MagicLinkNotification $notification) use (&$url): bool {
            $url = $notification->url;

            return true;
        },
    );

    $this->get($url)
        ->assertRedirect(route('images.index'))
        ->assertSessionHas('success');

    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->email_verified_at)->not->toBeNull();

    $this->get($url)->assertForbidden();
});

test('a magic link with an invalid signature is rejected', function () {
    $user = User::factory()->create();

    $this->get(route('login.magic', [
        'user' => $user,
        'email' => hash('sha256', $user->email),
    ]))->assertForbidden();
});
