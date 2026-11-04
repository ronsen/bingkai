@extends('layouts.app')

@section('title', $image->display_title . ' — Bingkai Photography')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- Top Bar: Back & Actions -->
    <div class="flex items-center justify-between gap-4">
        <a href="{{ route('images.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to gallery
        </a>

        <div class="flex items-center gap-2">
            @auth
                <!-- Edit Photograph -->
                <a
                    href="{{ route('images.edit', $image->slug) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 shadow-xs transition-all"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    <span>Edit</span>
                </a>

                <!-- Delete Photograph -->
                <form action="{{ route('images.destroy', $image->slug) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this photograph? This action cannot be undone.');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800/80 hover:bg-red-100 dark:hover:bg-red-900/60 shadow-xs transition-all"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        <span>Delete</span>
                    </button>
                </form>
            @endauth

            <!-- Download Action -->
            <a
                href="{{ route('images.download', $image->slug) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white shadow-sm transition-all hover:scale-[1.02] active:scale-[0.98]"
                title="Download full resolution file ({{ $image->formatted_file_size }})"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                <span>Download Original</span>
                <span class="opacity-75 font-mono">({{ $image->formatted_file_size }})</span>
            </a>
        </div>
    </div>

    <!-- Main Photograph Display (Standard Definition Optimized Version) -->
    <section class="relative bg-zinc-950 rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl ring-1 ring-zinc-900/10 dark:ring-zinc-800 flex items-center justify-center min-h-[350px] sm:min-h-[500px]">
        <img
            src="{{ $image->standard_url }}"
            alt="{{ $image->display_title }}"
            class="max-h-[82vh] w-auto max-w-full object-contain mx-auto select-none"
        />
    </section>

    <!-- Details and Metadata Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-4">
        <!-- Left 2 Cols: Title, Description, Story -->
        <div class="lg:col-span-2 space-y-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                    {{ $image->display_title }}
                </h1>
                <div class="flex flex-wrap items-center gap-4 text-xs text-zinc-500 dark:text-zinc-400 mt-2 font-mono">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5" />
                        </svg>
                        Taken on {{ $image->taken_at ? $image->taken_at->format('F d, Y \a\t H:i') : 'Unknown' }}
                    </span>
                    @if($image->camera_display)
                        <span>&bull;</span>
                        <span class="font-sans font-medium text-zinc-700 dark:text-zinc-300">
                            {{ $image->camera_display }}
                        </span>
                    @endif
                </div>
            </div>

            @if($image->description)
                <div class="prose dark:prose-invert max-w-none text-zinc-700 dark:text-zinc-300 text-sm leading-relaxed whitespace-pre-line border-t border-zinc-200 dark:border-zinc-800 pt-6">
                    {{ $image->description }}
                </div>
            @endif

            <!-- Download callout block -->
            <div class="p-4 rounded-2xl bg-zinc-100/80 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Original Photograph File</h3>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">
                        Preserves untouched camera dimensions, full color depth, and complete EXIF headers.
                    </p>
                </div>
                <a
                    href="{{ route('images.download', $image->slug) }}"
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Download ({{ $image->formatted_file_size }})
                </a>
            </div>
        </div>

        <!-- Right Col: Technical EXIF Metadata Box -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900/90 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 p-5 shadow-xs">
                <h2 class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                    Camera & Technical Details
                </h2>

                <dl class="space-y-3.5 text-xs">
                    @if($image->camera_make)
                        <div class="flex items-center justify-between">
                            <dt class="text-zinc-500 dark:text-zinc-400">Camera Make</dt>
                            <dd class="font-medium text-zinc-900 dark:text-zinc-100">{{ $image->camera_make }}</dd>
                        </div>
                    @endif

                    @if($image->camera_model)
                        <div class="flex items-center justify-between">
                            <dt class="text-zinc-500 dark:text-zinc-400">Model</dt>
                            <dd class="font-medium text-zinc-900 dark:text-zinc-100">{{ $image->camera_model }}</dd>
                        </div>
                    @endif

                    @if($image->lens_model)
                        <div class="flex items-center justify-between">
                            <dt class="text-zinc-500 dark:text-zinc-400">Lens</dt>
                            <dd class="font-medium text-zinc-900 dark:text-zinc-100 text-right">{{ $image->lens_model }}</dd>
                        </div>
                    @endif

                    @if($image->focal_length)
                        <div class="flex items-center justify-between font-mono">
                            <dt class="text-zinc-500 dark:text-zinc-400 font-sans">Focal Length</dt>
                            <dd class="font-medium text-zinc-900 dark:text-zinc-100">{{ $image->focal_length }}</dd>
                        </div>
                    @endif

                    @if($image->aperture)
                        <div class="flex items-center justify-between font-mono">
                            <dt class="text-zinc-500 dark:text-zinc-400 font-sans">Aperture</dt>
                            <dd class="font-medium text-zinc-900 dark:text-zinc-100">{{ $image->aperture }}</dd>
                        </div>
                    @endif

                    @if($image->exposure_time)
                        <div class="flex items-center justify-between font-mono">
                            <dt class="text-zinc-500 dark:text-zinc-400 font-sans">Shutter Speed</dt>
                            <dd class="font-medium text-zinc-900 dark:text-zinc-100">{{ $image->exposure_time }}</dd>
                        </div>
                    @endif

                    @if($image->iso)
                        <div class="flex items-center justify-between font-mono">
                            <dt class="text-zinc-500 dark:text-zinc-400 font-sans">ISO</dt>
                            <dd class="font-medium text-zinc-900 dark:text-zinc-100">{{ $image->iso }}</dd>
                        </div>
                    @endif

                    @if($image->width && $image->height)
                        <div class="flex items-center justify-between font-mono">
                            <dt class="text-zinc-500 dark:text-zinc-400 font-sans">Original Dimensions</dt>
                            <dd class="font-medium text-zinc-900 dark:text-zinc-100">{{ $image->width }} &times; {{ $image->height }} px</dd>
                        </div>
                    @endif

                    <div class="flex items-center justify-between font-mono">
                        <dt class="text-zinc-500 dark:text-zinc-400 font-sans">File Size</dt>
                        <dd class="font-medium text-zinc-900 dark:text-zinc-100">{{ $image->formatted_file_size }}</dd>
                    </div>

                    <div class="flex items-center justify-between font-mono">
                        <dt class="text-zinc-500 dark:text-zinc-400 font-sans">Format</dt>
                        <dd class="font-medium text-zinc-900 dark:text-zinc-100 uppercase">{{ explode('/', $image->mime_type)[1] ?? $image->mime_type }}</dd>
                    </div>

                    @if(!$image->hasMetadata())
                        <div class="pt-2 text-zinc-400 italic text-center">
                            No camera EXIF metadata found in this image.
                        </div>
                    @endif
                </dl>

                <!-- GPS Location Handling (Sensitive by default) -->
                @if($image->hasGps())
                    <div class="mt-5 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Geographic Location</span>
                            @if(!$showGps)
                                <a
                                    href="{{ route('images.show', ['slug' => $image->slug, 'show_gps' => 1]) }}"
                                    class="text-[11px] text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 underline"
                                >
                                    Reveal GPS
                                </a>
                            @else
                                <a
                                    href="{{ route('images.show', ['slug' => $image->slug, 'show_gps' => 0]) }}"
                                    class="text-[11px] text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 underline"
                                >
                                    Hide GPS
                                </a>
                            @endif
                        </div>

                        @if($showGps)
                            <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 font-mono text-xs space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-zinc-400 font-sans">Latitude:</span>
                                    <span>{{ $image->gps_latitude }}&deg;</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-zinc-400 font-sans">Longitude:</span>
                                    <span>{{ $image->gps_longitude }}&deg;</span>
                                </div>
                                <div class="pt-2 text-right">
                                    <a
                                        href="https://www.google.com/maps/search/?api=1&query={{ $image->gps_latitude }},{{ $image->gps_longitude }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-1 text-[11px] text-zinc-600 dark:text-zinc-300 hover:underline font-sans"
                                    >
                                        <span>Open on Map</span>
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @else
                            <p class="text-[11px] text-zinc-400">
                                Exact GPS coordinates are hidden by default to protect location privacy.
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
