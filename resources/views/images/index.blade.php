@extends('layouts.app')

@section('title', 'Bingkai')

@section('content')
<div class="space-y-8">
    @if($images->isEmpty())
        <!-- Empty State -->
        <div class="py-24 text-center border border-dashed border-zinc-300 dark:border-zinc-800 rounded-2xl p-8 bg-zinc-50/50 dark:bg-zinc-900/30">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-200">No photographs published yet</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 max-w-sm mx-auto">
                Start sharing high-resolution photographs with automatic optimization and metadata extraction.
            </p>
            @auth
                <div class="mt-6">
                    <a href="{{ route('images.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white shadow transition-all">
                        Upload Your First Photo
                    </a>
                </div>
            @endauth
        </div>
    @else
        <!-- Grid / Masonry gallery layout -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @foreach($images as $image)
                <article class="group flex flex-col bg-white dark:bg-zinc-900/80 rounded-2xl overflow-hidden border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs hover:shadow-md transition-all duration-300">
                    <a href="{{ route('images.show', $image->slug) }}" class="relative block aspect-4/3 overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                        <img
                            src="{{ $image->standard_url }}"
                            alt="{{ $image->display_title }}"
                            loading="lazy"
                            class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out"
                        />
                        <!-- Subtle gradient hover overlay -->
                        <div class="absolute inset-0 bg-linear-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                            <span class="text-xs text-white/90 font-medium">View photograph &rarr;</span>
                        </div>
                    </a>

                    <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 text-xs text-zinc-400 dark:text-zinc-500 mb-1.5 font-mono">
                                <span>
                                    {{ $image->taken_at ? $image->taken_at->format('M d, Y') : 'Unknown Date' }}
                                </span>
                                @if($image->camera_display)
                                    <span class="truncate max-w-32.5" title="{{ $image->camera_display }}">
                                        {{ $image->camera_display }}
                                    </span>
                                @endif
                            </div>
                            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 line-clamp-1 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors">
                                <a href="{{ route('images.show', $image->slug) }}">
                                    {{ $image->display_title }}
                                </a>
                            </h2>
                            @if($image->description)
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 line-clamp-2 leading-relaxed">
                                    {{ $image->description }}
                                </p>
                            @endif
                        </div>

                        <!-- Technical chips -->
                        <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400 font-mono">
                            <div class="flex items-center gap-2">
                                @if($image->aperture)
                                    <span class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800">{{ $image->aperture }}</span>
                                @endif
                                @if($image->exposure_time)
                                    <span class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800">{{ $image->exposure_time }}</span>
                                @endif
                                @if($image->iso)
                                    <span class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800">ISO {{ $image->iso }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pt-6">
            {{ $images->links() }}
        </div>
    @endif
</div>
@endsection
