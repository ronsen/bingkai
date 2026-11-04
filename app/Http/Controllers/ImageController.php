<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImageController extends Controller
{
    public function __construct(
        protected ImageService $imageService
    ) {}

    /**
     * Display the chronological image gallery/timeline.
     */
    public function index(Request $request): View
    {
        $page = (int) $request->input('page', 1);
        $images = $this->imageService->getCachedTimeline(page: $page, perPage: 12);
        $images->withPath($request->url())->withQueryString();

        return view('images.index', compact('images'));
    }

    /**
     * Show the upload image form.
     */
    public function create(): View
    {
        return view('images.create');
    }

    /**
     * Store a newly uploaded photograph.
     */
    public function store(StoreImageRequest $request): RedirectResponse
    {
        try {
            $image = $this->imageService->storeImage(
                file: $request->file('image'),
                title: $request->input('title'),
                description: $request->input('description')
            );

            // Invalidate timeline cache
            $this->imageService->invalidateImageCache($image->slug);

            return redirect()
                ->route('images.show', $image->slug)
                ->with('success', 'Photograph uploaded successfully!');
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['image' => 'Failed to process and store image: '.$e->getMessage()]);
        }
    }

    /**
     * Display the individual image viewing page.
     */
    public function show(string $slug, Request $request): View
    {
        $image = $this->imageService->getCachedImageBySlug($slug);

        if (! $image) {
            abort(404);
        }

        // Check if GPS display is requested/configured
        $showGpsConfig = config('images.show_gps_default', false);
        $showGps = $request->boolean('show_gps', $showGpsConfig);

        return view('images.show', compact('image', 'showGps'));
    }

    /**
     * Show the form for editing an existing photograph's title and description.
     */
    public function edit(string $slug): View
    {
        $image = Image::where('slug', $slug)->firstOrFail();

        return view('images.edit', compact('image'));
    }

    /**
     * Update an image's title and description.
     */
    public function update(UpdateImageRequest $request, string $slug): RedirectResponse
    {
        $image = Image::where('slug', $slug)->firstOrFail();

        $image->update([
            'title' => $request->filled('title') ? trim($request->input('title')) : null,
            'description' => $request->filled('description') ? trim($request->input('description')) : null,
        ]);

        // Invalidate both the individual image cache and timeline cache
        $this->imageService->invalidateImageCache($image->slug);

        return redirect()
            ->route('images.show', $image->slug)
            ->with('success', 'Photograph details updated successfully.');
    }

    /**
     * Delete an existing photograph and its storage files.
     */
    public function destroy(string $slug): RedirectResponse
    {
        $image = Image::where('slug', $slug)->firstOrFail();

        $this->imageService->deleteImage($image);

        return redirect()
            ->route('images.index')
            ->with('success', 'Photograph has been permanently deleted.');
    }

    /**
     * Download the original, untouched, full-resolution image.
     */
    public function download(string $slug): StreamedResponse
    {
        $image = $this->imageService->getCachedImageBySlug($slug);

        if (! $image) {
            abort(404);
        }

        $disk = config('images.disk', 'public');

        if (! Storage::disk($disk)->exists($image->original_path)) {
            abort(404, 'Original image file not found.');
        }

        $extension = pathinfo($image->original_path, PATHINFO_EXTENSION) ?: 'jpg';
        $downloadFilename = ($image->slug ?: 'photo').'.'.$extension;

        return Storage::disk($disk)->download($image->original_path, $downloadFilename, [
            'Content-Type' => $image->mime_type,
        ]);
    }
}
