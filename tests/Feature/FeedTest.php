<?php

use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

test('the rss feed lists images newest first', function () {
    Image::factory()->create(['title' => 'First Photo']);
    Image::factory()->create(['title' => 'Second Photo']);

    $this->get(route('feed'))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
        ->assertSee('<rss version="2.0"', false)
        ->assertSeeInOrder(['Second Photo', 'First Photo']);
});

test('the gallery lists images newest first by id', function () {
    Cache::flush();

    Image::factory()->create([
        'title' => 'First Photo',
        'taken_at' => now()->addDay(),
    ]);
    Image::factory()->create([
        'title' => 'Second Photo',
        'taken_at' => now()->subDay(),
    ]);

    $this->get(route('images.index'))
        ->assertOk()
        ->assertSeeInOrder(['Second Photo', 'First Photo']);
});
