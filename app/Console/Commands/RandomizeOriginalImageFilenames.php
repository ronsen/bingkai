<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RandomizeOriginalImageFilenames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:randomize-original-filenames {--dry-run : Show the files that would be renamed without changing anything}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rename legacy original image files to unguessable names and update their database paths';

    /**
     * Execute the console command.
     */
    public function handle(ImageService $imageService): int
    {
        $disk = config('images.disk', 'public');
        $storage = Storage::disk($disk);
        $renamed = 0;
        $skipped = 0;

        Image::query()->chunkById(100, function ($images) use ($storage, $imageService, &$renamed, &$skipped): void {
            foreach ($images as $image) {
                $originalPath = $image->original_path;

                if (! preg_match('/^original\.[^.]+$/', basename($originalPath))) {
                    $skipped++;

                    continue;
                }

                if (! $storage->exists($originalPath)) {
                    $this->warn("Missing original file for image {$image->id}: {$originalPath}");
                    $skipped++;

                    continue;
                }

                $extension = pathinfo($originalPath, PATHINFO_EXTENSION) ?: 'jpg';
                $newPath = dirname($originalPath).'/'.$imageService->generateOriginalFilename($extension);

                if ($this->option('dry-run')) {
                    $this->line("Would rename {$originalPath} -> {$newPath}");
                    $renamed++;

                    continue;
                }

                if (! $storage->move($originalPath, $newPath)) {
                    $this->error("Failed to rename {$originalPath}");
                    $skipped++;

                    continue;
                }

                $image->update(['original_path' => $newPath]);
                $imageService->invalidateImageCache($image->slug);

                $this->line("Renamed {$originalPath} -> {$newPath}");
                $renamed++;
            }
        });

        $this->info("Renamed {$renamed} original file(s). Skipped {$skipped}.");

        return self::SUCCESS;
    }
}
