<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreImageRequest;
use App\Http\Requests\Api\UpdateImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ImageController extends Controller
{
    public function __construct(
        protected ImageService $imageService,
    ) {}

    /**
     * List photographs in reverse chronological order.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $page = max(1, $request->integer('page', 1));
        $perPage = min(100, max(1, $request->integer('per_page', 12)));

        $images = $this->imageService->getCachedTimeline(
            page: $page,
            perPage: $perPage,
        );

        return ImageResource::collection($images);
    }

    /**
     * Store a newly uploaded photograph.
     */
    public function store(StoreImageRequest $request): JsonResponse
    {
        $image = $this->imageService->storeImage(
            file: $request->file('image'),
            title: $request->input('title'),
            description: $request->input('description'),
        );

        $this->imageService->invalidateImageCache($image->slug);

        return (new ImageResource($image))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display a single photograph by slug.
     */
    public function show(string $slug): ImageResource|JsonResponse
    {
        $image = $this->imageService->getCachedImageBySlug($slug);

        if (! $image) {
            return response()->json([
                'message' => 'Image not found.',
            ], 404);
        }

        return new ImageResource($image);
    }

    /**
     * Update a photograph's title and description.
     */
    public function update(
        UpdateImageRequest $request,
        string $slug,
    ): ImageResource {
        $image = Image::where('slug', $slug)->firstOrFail();

        $this->imageService->updateImage(
            $image,
            $request->input('title'),
            $request->input('description'),
        );

        return new ImageResource($image);
    }

    /**
     * Delete a photograph and its stored preview.
     */
    public function destroy(string $slug): JsonResponse
    {
        $image = Image::where('slug', $slug)->firstOrFail();

        $this->imageService->deleteImage($image);

        return response()->json(null, 204);
    }
}
