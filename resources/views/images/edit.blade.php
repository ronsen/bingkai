@extends('layouts.app')

@section('title', 'Edit ' . $image->display_title . ' — Bingkai Photography')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('images.show', $image->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to photograph
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Edit Photograph</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
            Update the title and story description. Technical camera metadata and slug URLs remain unchanged.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 text-sm">
            <div class="font-semibold mb-1">Please fix the following:</div>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-zinc-900/90 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 p-6 sm:p-8 shadow-xs space-y-6">
        <!-- Photo Thumbnail Preview -->
        <div class="flex items-center gap-4 p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/60 dark:border-zinc-800/60">
            <img
                src="{{ $image->standard_url }}"
                alt="{{ $image->display_title }}"
                class="w-16 h-16 object-cover rounded-lg shrink-0"
            />
            <div class="text-xs space-y-0.5 min-w-0">
                <p class="font-semibold text-zinc-900 dark:text-zinc-100 truncate">{{ $image->display_title }}</p>
                <p class="text-zinc-400 font-mono">Taken: {{ $image->taken_at ? $image->taken_at->format('M d, Y') : 'Unknown' }} &bull; {{ $image->formatted_file_size }}</p>
                <p class="text-zinc-400 font-mono truncate">Slug: {{ $image->slug }}</p>
            </div>
        </div>

        <form action="{{ route('images.update', $image->slug) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200 mb-1">
                    Title <span class="text-xs text-zinc-400 font-normal">(optional)</span>
                </label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    value="{{ old('title', $image->title) }}"
                    placeholder="e.g. Sunrise over Mount Fuji"
                    class="w-full px-4 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-sm transition-shadow"
                />
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200 mb-1">
                    Description / Story <span class="text-xs text-zinc-400 font-normal">(optional)</span>
                </label>
                <textarea
                    name="description"
                    id="description"
                    rows="5"
                    placeholder="Share the context, shooting conditions, location, or thoughts behind this shot..."
                    class="w-full px-4 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-sm transition-shadow"
                >{{ old('description', $image->description) }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <a href="{{ route('images.show', $image->slug) }}" class="px-4 py-2 text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">
                    Cancel
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white shadow transition-all hover:scale-[1.02] active:scale-[0.98]"
                >
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
