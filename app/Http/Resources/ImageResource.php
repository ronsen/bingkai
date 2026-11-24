<?php

namespace App\Http\Resources;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Image
 */
class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'slug' => $this->slug,
            'title' => $this->title,
            'display_title' => $this->display_title,
            'description' => $this->description,
            'image_url' => $this->standard_url,
            'mime_type' => $this->mime_type,
            'width' => $this->width,
            'height' => $this->height,
            'taken_at' => $this->taken_at?->toIso8601String(),
            'camera' => [
                'make' => $this->camera_make,
                'model' => $this->camera_model,
                'display' => $this->camera_display,
                'lens' => $this->lens_model,
            ],
            'exposure' => [
                'time' => $this->exposure_time,
                'aperture' => $this->aperture,
                'iso' => $this->iso,
                'focal_length' => $this->focal_length,
            ],
            'gps' => $this->hasGps()
                ? [
                    'latitude' => $this->gps_latitude,
                    'longitude' => $this->gps_longitude,
                ]
                : null,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
