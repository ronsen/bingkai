<?php

use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

test('changing the title updates the image slug', function () {
    Cache::flush();

    $image = Image::factory()->create([
        'title' => 'Old Title',
        'slug' => 'old-title-abc12345',
    ]);
    $user = User::factory()->create();
    $oldSlug = $image->slug;

    $this->actingAs($user)
        ->patch(route('images.update', $oldSlug), [
            'title' => 'New Title',
            'description' => 'Updated description',
        ])
        ->assertRedirect(route('images.show', $image->fresh()->slug));

    $image->refresh();

    expect($image->title)->toBe('New Title')
        ->and($image->slug)->toStartWith('new-title-')
        ->and($image->slug)->not->toBe($oldSlug);

    $this->get(route('images.show', $oldSlug))->assertNotFound();
});

test('changing only the description keeps the image slug', function () {
    $image = Image::factory()->create([
        'title' => 'Stable Title',
        'slug' => 'stable-title-abc12345',
    ]);
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('images.update', $image->slug), [
            'title' => 'Stable Title',
            'description' => 'New description',
        ])
        ->assertRedirect(route('images.show', $image->slug));

    expect($image->fresh()->slug)->toBe('stable-title-abc12345');
});
