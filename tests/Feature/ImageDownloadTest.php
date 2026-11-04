<?php

use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('guests are redirected to sign in before downloading originals', function () {
    $image = Image::factory()->create();

    $this->get(route('images.download', $image->slug))
        ->assertRedirect(route('login'));
});

test('authenticated users can download original images', function () {
    Storage::fake('public');

    $image = Image::factory()->create([
        'original_path' => 'images/test/'.fake()->uuid().'.jpg',
        'mime_type' => 'image/jpeg',
    ]);

    Storage::disk('public')->put($image->original_path, 'original-image-bytes');

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('images.download', $image->slug))
        ->assertOk()
        ->assertHeader('content-type', 'image/jpeg');
});
