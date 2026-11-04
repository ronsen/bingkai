<?php

use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('original image downloads are no longer available', function () {
    $image = Image::factory()->create();

    $this->get('/'.$image->slug.'/download')->assertNotFound();
});
