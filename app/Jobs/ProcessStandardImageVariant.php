<?php

namespace App\Jobs;

use App\Models\Image as ImageModel;
use App\Services\ImageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessStandardImageVariant implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ImageModel $imageRecord
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ImageService $imageService): void
    {
        $imageService->generateStandardVariant($this->imageRecord);
    }
}
