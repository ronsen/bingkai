<?php

namespace App\Services;

use App\Models\Image as ImageModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ImageService
{
    protected ImageManager $imageManager;

    public function __construct(
        protected ExifReaderService $exifReader,
    ) {
        $driver = extension_loaded('imagick') ? new ImagickDriver : new GdDriver;
        $this->imageManager = new ImageManager($driver);
    }

    /**
     * Store and process an uploaded image.
     */
    public function storeImage(UploadedFile $file, ?string $title = null, ?string $description = null): ImageModel
    {
        $disk = config('images.disk', 'public');
        $uuid = (string) Str::uuid();

        // Generate unique slug
        $slug = $this->generateSlug($title, $uuid);

        // Determine filenames and paths
        $extension = strtolower($file->getClientOriginalExtension());
        if (empty($extension)) {
            $extension = 'jpg';
        }

        $folder = 'images/'.$uuid;
        $originalFilename = 'original.'.$extension;
        $standardFilename = 'standard.webp';

        $originalPath = $folder.'/'.$originalFilename;
        $standardPath = $folder.'/'.$standardFilename;

        // 1. Store original full-resolution image untouched
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType() ?: 'image/jpeg';
        $realPath = $file->getRealPath();

        // Extract EXIF metadata from original file before moving/processing
        $exifData = $this->exifReader->extract($realPath);

        // Read dimensions using getimagesize
        $imageSize = @getimagesize($realPath);
        $width = $imageSize ? (int) $imageSize[0] : null;
        $height = $imageSize ? (int) $imageSize[1] : null;

        // Store original to disk
        Storage::disk($disk)->putFileAs($folder, $file, $originalFilename);

        // Fall back taken_at to current timestamp if not available in EXIF
        $takenAt = $exifData['taken_at'] ?? now();

        // 2. Create the Database Record
        $imageRecord = ImageModel::create([
            'uuid' => $uuid,
            'slug' => $slug,
            'title' => $title ? trim($title) : null,
            'description' => $description ? trim($description) : null,
            'original_path' => $originalPath,
            'standard_path' => null, // Set after SD generation succeeds
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'width' => $width,
            'height' => $height,
            'taken_at' => $takenAt,
            'camera_make' => $exifData['camera_make'],
            'camera_model' => $exifData['camera_model'],
            'lens_model' => $exifData['lens_model'],
            'exposure_time' => $exifData['exposure_time'],
            'aperture' => $exifData['aperture'],
            'iso' => $exifData['iso'],
            'focal_length' => $exifData['focal_length'],
            'gps_latitude' => $exifData['gps_latitude'],
            'gps_longitude' => $exifData['gps_longitude'],
        ]);

        // 3. Generate SD variant
        // Ensure that failure in SD generation does not corrupt or lose the original
        $this->generateStandardVariant($imageRecord, $standardPath);

        return $imageRecord;
    }

    /**
     * Generate the optimized Standard Definition (SD) image variant.
     */
    public function generateStandardVariant(ImageModel $imageRecord, ?string $targetPath = null): bool
    {
        $disk = config('images.disk', 'public');
        $targetPath = $targetPath ?: 'images/'.$imageRecord->uuid.'/standard.webp';

        try {
            $originalContent = Storage::disk($disk)->get($imageRecord->original_path);
            if (! $originalContent) {
                return false;
            }

            $maxDimension = (int) config('images.max_dimension', 1920);
            $quality = (int) config('images.quality', 85);

            // Read image from memory
            $image = $this->imageManager->decode($originalContent);

            // Auto-orient according to EXIF orientation
            $image->orient();

            // Resize down without upscaling, preserving aspect ratio
            $image->scaleDown(width: $maxDimension, height: $maxDimension);

            // Encode to webp
            $encoded = $image->encode(new WebpEncoder(quality: $quality));

            // Save to disk
            Storage::disk($disk)->put($targetPath, (string) $encoded);

            // Update model
            $imageRecord->update([
                'standard_path' => $targetPath,
            ]);

            $this->invalidateImageCache($imageRecord->slug);

            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }

    /**
     * Generate a unique slug based on title (or fallback) and uuid prefix.
     */
    public function generateSlug(?string $title, string $uuid): string
    {
        $shortId = substr(str_replace('-', '', $uuid), 0, 8);
        $base = ! empty($title) ? Str::slug($title) : '';

        $slug = ! empty($base) ? "{$base}-{$shortId}" : $shortId;

        // Ensure uniqueness just in case
        $counter = 1;
        while (ImageModel::where('slug', $slug)->exists()) {
            $slug = ! empty($base) ? "{$base}-{$shortId}-{$counter}" : "{$shortId}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Delete an image record and its associated physical files from storage.
     */
    public function deleteImage(ImageModel $image): bool
    {
        $disk = config('images.disk', 'public');
        $folder = 'images/'.$image->uuid;

        // Delete all stored variants and folder
        Storage::disk($disk)->deleteDirectory($folder);

        $this->invalidateImageCache($image->slug);

        return (bool) $image->delete();
    }

    /**
     * Invalidate cached timeline pages and individual photo cache.
     */
    public function invalidateImageCache(?string $slug = null): void
    {
        if ($slug) {
            Cache::forget('image_slug_'.$slug);
        }

        // Invalidate timeline pagination by bumping the version timestamp
        Cache::forever('images_timeline_version', (string) microtime(true));
    }

    /**
     * Get cached image model by slug or fetch from database.
     */
    public function getCachedImageBySlug(string $slug): ?ImageModel
    {
        $ttl = (int) config('images.cache_ttl', 3600);
        $key = 'image_slug_'.$slug;

        $cachedAttributes = Cache::remember($key, $ttl, function () use ($slug) {
            $image = ImageModel::where('slug', $slug)->first();

            return $image ? $image->getRawOriginal() : null;
        });

        if (! $cachedAttributes) {
            return null;
        }

        return (new ImageModel)->newFromBuilder($cachedAttributes);
    }

    /**
     * Get cached timeline paginator.
     */
    public function getCachedTimeline(int $page = 1, int $perPage = 12): LengthAwarePaginator
    {
        $ttl = (int) config('images.cache_ttl', 3600);
        $version = Cache::get('images_timeline_version', '1');
        $key = "images_timeline_v{$version}_p{$page}_pp{$perPage}";

        $cachedData = Cache::remember($key, $ttl, function () use ($perPage) {
            $paginator = ImageModel::query()->chronological()->paginate($perPage);

            return [
                'items' => $paginator->map(fn ($item) => $item->getRawOriginal())->all(),
                'total' => $paginator->total(),
                'perPage' => $paginator->perPage(),
                'currentPage' => $paginator->currentPage(),
                'options' => $paginator->getOptions(),
            ];
        });

        $models = collect($cachedData['items'])->map(fn ($attributes) => (new ImageModel)->newFromBuilder($attributes));

        return new LengthAwarePaginator(
            $models,
            $cachedData['total'],
            $cachedData['perPage'],
            $cachedData['currentPage'],
            $cachedData['options']
        );
    }
}
