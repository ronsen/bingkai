<?php

use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('uploads store only an optimized preview', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg', 100, 100);

    $this->actingAs($user)
        ->post(route('images.store'), [
            'image' => $file,
            'title' => 'Test Photo',
        ])
        ->assertRedirect();

    $image = Image::first();

    expect($image)->not->toBeNull()
        ->and($image->filename)->toMatch('/^[A-Za-z0-9]{40}\.webp$/')
        ->and($image->filename)->not->toBe($image->id.'.webp')
        ->and($image->preview_path)->toBe('images/'.$image->filename);

    Storage::disk('public')->assertExists($image->preview_path);
    expect(Storage::disk('public')->allFiles('images'))->toBe([$image->preview_path])
        ->and(Storage::disk('public')->allDirectories('images'))->toBe([]);
});

test('the images table stores only the filename', function () {
    expect(Schema::hasColumn('images', 'filename'))->toBeTrue()
        ->and(Schema::hasColumn('images', 'file_size'))->toBeFalse()
        ->and(Schema::hasColumn('images', 'original_path'))->toBeFalse()
        ->and(Schema::hasColumn('images', 'standard_path'))->toBeFalse();
});
