<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'slug',
        'title',
        'description',
        'filename',
        'mime_type',
        'width',
        'height',
        'taken_at',
        'camera_make',
        'camera_model',
        'lens_model',
        'exposure_time',
        'aperture',
        'iso',
        'focal_length',
        'gps_latitude',
        'gps_longitude',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'taken_at' => 'datetime',
            'width' => 'integer',
            'height' => 'integer',
            'iso' => 'integer',
            'gps_latitude' => 'float',
            'gps_longitude' => 'float',
        ];
    }

    /**
     * Get the display title.
     */
    public function getDisplayTitleAttribute(): string
    {
        return ! empty($this->title) ? $this->title : 'Untitled';
    }

    /**
     * Get the storage path for the optimized preview.
     */
    public function getPreviewPathAttribute(): ?string
    {
        return $this->filename ? 'images/'.$this->filename : null;
    }

    /**
     * Get the URL for the optimized preview.
     * Falls back to a neutral placeholder if the preview is unavailable.
     */
    public function getStandardUrlAttribute(): string
    {
        if (! $this->preview_path) {
            return 'data:image/svg+xml;charset=UTF-8,'
            .rawurlencode(
                '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="800"><rect width="100%" height="100%" fill="#e4e4e7"/><text x="50%" y="50%" fill="#71717a" font-family="sans-serif" font-size="32" text-anchor="middle" dominant-baseline="middle">Preview unavailable</text></svg>',
            );
        }

        $disk = config('images.disk', 'public');

        return Storage::disk($disk)->url($this->preview_path);
    }

    /**
     * Human-readable camera information.
     */
    public function getCameraDisplayAttribute(): ?string
    {
        $make = $this->camera_make;
        $model = $this->camera_model;

        if (! $make && ! $model) {
            return null;
        }

        if ($make && $model) {
            // Avoid duplicate make if model already includes make
            if (str_starts_with(strtolower($model), strtolower($make))) {
                return $model;
            }

            return "{$make} {$model}";
        }

        return $model ?: $make;
    }

    /**
     * Check if camera/photo EXIF metadata is present.
     */
    public function hasMetadata(): bool
    {
        return
            $this->camera_make !== null
            || $this->camera_model !== null
            || $this->lens_model !== null
            || $this->exposure_time !== null
            || $this->aperture !== null
            || $this->iso !== null
            || $this->focal_length !== null
            || $this->width !== null
            && $this->height !== null;
    }

    /**
     * Check if GPS coordinates are available.
     */
    public function hasGps(): bool
    {
        return $this->gps_latitude !== null && $this->gps_longitude !== null;
    }

    /**
     * Scope for timeline ordering (newest photograph first).
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeChronological($query)
    {
        return $query->orderByDesc('taken_at');
    }
}
