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

test('authenticated users can access the upload page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('images.create'))
        ->assertOk();
});

test('guests cannot delete images', function () {
    $image = Image::factory()->create();

    $this->delete(route('images.destroy', $image->slug))
        ->assertRedirect(route('login'));

    $this->assertModelExists($image);
});

test('authenticated users can delete images', function () {
    Storage::fake('public');

    $image = Image::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->delete(route('images.destroy', $image->slug))
        ->assertRedirect(route('images.index'));

    $this->assertModelMissing($image);
});
