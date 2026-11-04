<?php

namespace Database\Factories;

use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Image>
 */
class ImageFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		$uuid = fake()->uuid();

		return [
			'uuid' => $uuid,
			'slug' => fake()->unique()->slug(),
			'title' => fake()->sentence(3),
			'description' => fake()->optional()->paragraph(),
			'filename' => Str::random(40) . '.webp',
			'mime_type' => 'image/jpeg',
			'width' => fake()->numberBetween(800, 4000),
			'height' => fake()->numberBetween(600, 3000),
			'taken_at' => fake()->dateTimeBetween('-5 years'),
			'camera_make' => fake()
				->optional()
				->randomElement(['Canon', 'Nikon', 'Sony']),
			'camera_model' => fake()->optional()->word(),
			'lens_model' => fake()->optional()->word(),
			'exposure_time' => fake()
				->optional()
				->randomElement(['1/125', '1/250', '1/500']),
			'aperture' => fake()
				->optional()
				->randomElement(['f/1.8', 'f/2.8', 'f/4']),
			'iso' => fake()->optional()->randomElement([100, 200, 400, 800]),
			'focal_length' => fake()
				->optional()
				->randomElement(['35mm', '50mm', '85mm']),
			'gps_latitude' => null,
			'gps_longitude' => null,
		];
	}
}
