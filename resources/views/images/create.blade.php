@extends('layouts.app')

@section('title', 'Upload Photograph — Bingkai')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('images.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to gallery
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Upload Photograph</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
            Original images are stored without quality loss. EXIF metadata will be automatically detected and extracted.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 text-sm">
            <div class="font-semibold mb-1">Please fix the following issues:</div>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('images.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- File Upload Zone -->
        <div>
            <label class="block text-sm font-medium text-zinc-800 dark:text-zinc-200 mb-2">
                Image File <span class="text-red-500">*</span>
            </label>
            <div
                id="drop-zone"
                class="relative border-2 border-dashed border-zinc-300 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-600 rounded-2xl p-8 text-center bg-white dark:bg-zinc-900/50 transition-colors cursor-pointer group"
            >
                <input
                    type="file"
                    name="image"
                    id="image-input"
                    accept="image/jpeg,image/png,image/webp"
                    required
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                />

                <div id="drop-zone-idle" class="space-y-3">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-500 dark:text-zinc-400 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Click to upload</span>
                        <span class="text-sm text-zinc-500 dark:text-zinc-400"> or drag and drop</span>
                    </div>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">
                        JPEG, PNG, or WebP up to 25MB. Original EXIF metadata will be preserved.
                    </p>
                </div>

                <!-- Preview state -->
                <div id="drop-zone-preview" class="hidden space-y-3">
                    <img id="preview-image" src="" alt="Upload preview" class="max-h-64 mx-auto rounded-xl object-contain shadow-sm" />
                    <p id="file-name" class="text-xs font-mono text-zinc-600 dark:text-zinc-300"></p>
                    <span class="text-xs text-zinc-400 underline">Click to choose a different image</span>
                </div>
            </div>
        </div>

        <!-- Optional Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200 mb-1">
                Title <span class="text-xs text-zinc-400 font-normal">(optional)</span>
            </label>
            <input
                type="text"
                name="title"
                id="title"
                value="{{ old('title') }}"
                placeholder="e.g. Sunrise over Mount Fuji"
                class="w-full px-4 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-sm transition-shadow"
            />
        </div>

        <!-- Optional Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200 mb-1">
                Description / Story <span class="text-xs text-zinc-400 font-normal">(optional)</span>
            </label>
            <textarea
                name="description"
                id="description"
                rows="4"
                placeholder="Share the context, shooting conditions, location, or thoughts behind this shot..."
                class="w-full px-4 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-sm transition-shadow"
            >{{ old('description') }}</textarea>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
            <a href="{{ route('images.index') }}" class="px-4 py-2 text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">
                Cancel
            </a>
            <button
                type="submit"
                id="submit-btn"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white shadow transition-all hover:scale-[1.02] active:scale-[0.98]"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Publish Photograph
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const input = document.getElementById('image-input');
    const idleView = document.getElementById('drop-zone-idle');
    const previewView = document.getElementById('drop-zone-preview');
    const previewImage = document.getElementById('preview-image');
    const fileName = document.getElementById('file-name');

    input.addEventListener('change', function(e) {
        const file = this.files[0];
        if (file) {
            fileName.textContent = `${file.name} (${(file.size / (1024 * 1024)).toFixed(2)} MB)`;
            const reader = new FileReader();
            reader.onload = function(event) {
                previewImage.src = event.target.result;
                idleView.classList.add('hidden');
                previewView.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection
