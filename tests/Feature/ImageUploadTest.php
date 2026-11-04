<?php

use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('uploaded originals are stored with an unguessable filename', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $file = UploadedFile::fake()->image('photo.jpg', 100, 100);

    $this->actingAs($admin)
        ->post(route('images.store'), [
            'image' => $file,
            'title' => 'Test Photo',
        ])
        ->assertRedirect();

    $image = Image::first();

    expect($image)->not->toBeNull()
        ->and(basename($image->original_path))->toMatch('/^[0-9a-f-]{36}\.jpg$/')
        ->and(basename($image->original_path))->not->toBe('original.jpg');

    Storage::disk('public')->assertExists($image->original_path);
});

test('the command renames legacy original filenames', function () {
    Storage::fake('public');

    $image = Image::factory()->create([
        'original_path' => 'images/legacy/original.jpg',
    ]);

    Storage::disk('public')->put($image->original_path, 'original-image-bytes');

    $this->artisan('images:randomize-original-filenames')
        ->assertSuccessful();

    $image->refresh();

    expect(basename($image->original_path))->toMatch('/^[0-9a-f-]{36}\.jpg$/');

    Storage::disk('public')->assertMissing('images/legacy/original.jpg');
    Storage::disk('public')->assertExists($image->original_path);
});
