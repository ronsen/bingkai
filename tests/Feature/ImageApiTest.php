<?php

use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('the public api lists images newest first without authentication', function () {
    Cache::flush();

    Image::factory()->create([
        'title' => 'Newest Photo',
        'taken_at' => now()->addDay(),
    ]);
    Image::factory()->create([
        'title' => 'Oldest Photo',
        'taken_at' => now()->subDay(),
    ]);

    $this->getJson(route('api.v1.images.index'))
        ->assertOk()
        ->assertJsonPath('data.0.title', 'Newest Photo')
        ->assertJsonPath('data.1.title', 'Oldest Photo');
});

test('the public api shows a single image by slug', function () {
    $image = Image::factory()->create([
        'title' => 'A Single Photo',
        'camera_make' => 'Canon',
        'camera_model' => 'EOS R5',
    ]);

    $this->getJson(route('api.v1.images.show', $image->slug))
        ->assertOk()
        ->assertJsonPath('data.slug', $image->slug)
        ->assertJsonPath('data.title', 'A Single Photo')
        ->assertJsonPath('data.camera.display', 'Canon EOS R5');
});

test('the public api returns not found for an unknown slug', function () {
    $this->getJson(route('api.v1.images.show', 'missing-image'))
        ->assertNotFound();
});

test('the public api can upload an image without authentication', function () {
    Storage::fake('public');

    $this->postJson(route('api.v1.images.store'), [
        'image' => UploadedFile::fake()->image('photo.jpg', 100, 100),
        'title' => 'Public Upload',
        'description' => 'Uploaded through the API.',
    ])
        ->assertCreated()
        ->assertJsonPath('data.title', 'Public Upload')
        ->assertJsonPath('data.description', 'Uploaded through the API.');

    $image = Image::firstOrFail();

    expect($image->filename)->toMatch('/^[A-Za-z0-9]{40}\.webp$/');

    Storage::disk('public')->assertExists($image->preview_path);
});

test('the public api validates uploads', function () {
    $this->postJson(route('api.v1.images.store'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('image');
});

test('the public api can update an image without authentication', function () {
    $image = Image::factory()->create([
        'title' => 'Old API Title',
        'slug' => 'old-api-title-abc12345',
    ]);

    $this->patchJson(route('api.v1.images.update', $image->slug), [
        'title' => 'New API Title',
        'description' => 'Updated through the API.',
    ])
        ->assertOk()
        ->assertJsonPath('data.title', 'New API Title')
        ->assertJsonPath('data.description', 'Updated through the API.');

    $image->refresh();

    expect($image->slug)->toStartWith('new-api-title-')
        ->and($image->slug)->not->toBe('old-api-title-abc12345');
});

test('the public api can delete an image without authentication', function () {
    Storage::fake('public');

    $image = Image::factory()->create();

    $this->deleteJson(route('api.v1.images.destroy', $image->slug))
        ->assertNoContent();

    $this->assertModelMissing($image);
});
