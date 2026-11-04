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
        'original_path',
        'standard_path',
        'mime_type',
        'file_size',
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
            'file_size' => 'integer',
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
     * Get the URL for the Standard Definition variant.
     * Falls back to a neutral placeholder if the standard variant is unavailable.
     */
    public function getStandardUrlAttribute(): string
    {
        if (! $this->standard_path) {
            return 'data:image/svg+xml;charset=UTF-8,'.rawurlencode(
                '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="800"><rect width="100%" height="100%" fill="#e4e4e7"/><text x="50%" y="50%" fill="#71717a" font-family="sans-serif" font-size="32" text-anchor="middle" dominant-baseline="middle">Preview unavailable</text></svg>'
            );
        }

        $disk = config('images.disk', 'public');

        return Storage::disk($disk)->url($this->standard_path);
    }

    /**
     * Get the authenticated download URL for the original full-resolution image.
     */
    public function getOriginalUrlAttribute(): string
    {
        return route('images.download', $this->slug);
    }

    /**
     * Human-readable formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
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
        return $this->camera_make !== null
            || $this->camera_model !== null
            || $this->lens_model !== null
            || $this->exposure_time !== null
            || $this->aperture !== null
            || $this->iso !== null
            || $this->focal_length !== null
            || ($this->width !== null && $this->height !== null);
    }

    /**
     * Check if GPS coordinates are available.
     */
    public function hasGps(): bool
    {
        return $this->gps_latitude !== null && $this->gps_longitude !== null;
    }

    /**
     * Scope for chronological timeline ordering (newest photograph first).
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeChronological($query)
    {
        return $query->orderBy('taken_at', 'desc')->orderBy('created_at', 'desc');
    }
}
