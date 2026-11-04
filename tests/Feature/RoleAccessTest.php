<?php

use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('guests are redirected from the upload page', function () {
    $this->get(route('images.create'))
        ->assertRedirect(route('login'));
});

test('guest role users cannot access the upload page', function () {
    $user = User::factory()->guest()->create();

    $this->actingAs($user)
        ->get(route('images.create'))
        ->assertForbidden();
});

test('standard users cannot access the upload page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('images.create'))
        ->assertForbidden();
});

test('admins can access the upload page', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->get(route('images.create'))
        ->assertOk();
});

test('standard users cannot delete images', function () {
    $image = Image::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->delete(route('images.destroy', $image->slug))
        ->assertForbidden();

    $this->assertModelExists($image);
});

test('admins can delete images', function () {
    Storage::fake('public');

    $image = Image::factory()->create();
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->delete(route('images.destroy', $image->slug))
        ->assertRedirect(route('images.index'));

    $this->assertModelMissing($image);
});
