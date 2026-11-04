<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>@yield('title', config('app.name', 'Bingkai'))</title>
	<link rel="alternate" type="application/rss+xml" title="{{ config('app.name', 'Bingkai') }} RSS Feed"
		href="{{ route('feed') }}">

	<!-- Theme initialization script to prevent flash of wrong theme -->
	<script>
		if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
			document.documentElement.classList.add('dark');
		} else {
			document.documentElement.classList.remove('dark');
		}
	</script>

	<link rel="preconnect" href="https://fonts.bunny.net">
	<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

	@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
	class="min-h-full flex flex-col bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased selection:bg-zinc-900 selection:text-white dark:selection:bg-zinc-100 dark:selection:text-zinc-900 transition-colors duration-200">
	<!-- Navigation Header -->
	<header
		class="sticky top-0 z-40 w-full backdrop-blur border-b border-zinc-200/80 dark:border-zinc-800/80 bg-zinc-50/80 dark:bg-zinc-950/80">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
			<div class="flex items-center gap-6">
				<a href="{{ route('images.index') }}"
					class="group flex items-center gap-1.5 text-zinc-900 dark:text-white font-semibold tracking-tight text-lg">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
						class="bi bi-camera2" viewBox="0 0 16 16">
						<path d="M5 8c0-1.657 2.343-3 4-3V4a4 4 0 0 0-4 4" />
						<path
							d="M12.318 3h2.015C15.253 3 16 3.746 16 4.667v6.666c0 .92-.746 1.667-1.667 1.667h-2.015A5.97 5.97 0 0 1 9 14a5.97 5.97 0 0 1-3.318-1H1.667C.747 13 0 12.254 0 11.333V4.667C0 3.747.746 3 1.667 3H2a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1h.682A5.97 5.97 0 0 1 9 2c1.227 0 2.367.368 3.318 1M2 4.5a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0M14 8A5 5 0 1 0 4 8a5 5 0 0 0 10 0" />
					</svg>
					<span class="tracking-wider uppercase text-sm font-bold">{{ config('app.name') }}</span>
				</a>
			</div>

			<div class="flex items-center gap-3">
				<!-- Theme Toggle Button -->
				<button type="button" id="theme-toggle" aria-label="Toggle dark/light theme"
					class="p-2 rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-200/70 dark:hover:bg-zinc-800/70 transition-colors">
					<!-- Sun Icon (shown in dark mode) -->
					<svg id="theme-toggle-light-icon" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24"
						stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
							d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
					</svg>
					<!-- Moon Icon (shown in light mode) -->
					<svg id="theme-toggle-dark-icon" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24"
						stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
							d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
					</svg>
				</button>

				<!-- Publish / Upload Button -->
				@auth
					<a href="{{ route('images.create') }}"
						class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-sm font-medium bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white shadow-sm transition-all hover:scale-[1.02] active:scale-[0.98]">
						<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 4.5v15m7.5-7.5h-15" />
						</svg>
						<span>Upload</span>
					</a>
				@endauth

				<!-- User Auth Status -->
				@auth
					<div class="flex items-center gap-2 pl-2 border-l border-zinc-200 dark:border-zinc-800">
						<a href="{{ route('profile.edit') }}"
							class="px-2.5 py-1.5 text-xs font-semibold rounded-lg text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors {{ request()->routeIs('profile.edit') ? 'bg-zinc-100 dark:bg-zinc-800' : '' }}"
							title="Signed in as {{ auth()->user()->name }}">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
								class="bi bi-person-circle" viewBox="0 0 16 16">
								<path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
								<path fill-rule="evenodd"
									d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
							</svg>
						</a>
						<form action="{{ route('logout') }}" method="POST" class="inline">
							@csrf
							<button type="submit"
								class="p-1.5 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
								title="Sign Out">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
									class="bi bi-box-arrow-right" viewBox="0 0 16 16">
									<path fill-rule="evenodd"
										d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
									<path fill-rule="evenodd"
										d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
								</svg>
							</button>
						</form>
					</div>
				@else
					<a href="{{ route('login') }}"
						class="px-3 py-1.5 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
						Sign In
					</a>
				@endauth
			</div>
		</div>
	</header>

	<!-- Notification banners -->
	@if (session('success'))
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
			<div
				class="flex items-center justify-between p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm">
				<div class="flex items-center gap-2">
					<svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24"
						stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					<span>{{ session('success') }}</span>
				</div>
			</div>
		</div>
	@endif

	<!-- Main Content Area -->
	<main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
		@yield('content')
	</main>

	<!-- Clean Minimal Footer -->
	<footer
		class="border-t border-zinc-200/80 dark:border-zinc-800/80 py-8 text-center text-xs text-zinc-500 dark:text-zinc-400">
		<div
			class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
			<p>&copy; {{ date('Y') }} {{ config('app.name') }}.</p>
			<div class="flex items-center gap-4">
				<a href="https://github.com/ronsen/bingkai" target="_blank">Source code</a>
				<a href="{{ route('feed') }}" class="hover:underline">RSS feed</a>
			</div>
		</div>
	</footer>

	<!-- Theme toggle script -->
	<script>
		const themeToggleBtn = document.getElementById('theme-toggle');
		const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
		const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

		function updateIcons() {
			if (document.documentElement.classList.contains('dark')) {
				themeToggleLightIcon.classList.remove('hidden');
				themeToggleDarkIcon.classList.add('hidden');
			} else {
				themeToggleDarkIcon.classList.remove('hidden');
				themeToggleLightIcon.classList.add('hidden');
			}
		}

		updateIcons();

		themeToggleBtn.addEventListener('click', function () {
			if (document.documentElement.classList.contains('dark')) {
				document.documentElement.classList.remove('dark');
				localStorage.theme = 'light';
			} else {
				document.documentElement.classList.add('dark');
				localStorage.theme = 'dark';
			}
			updateIcons();
		});
	</script>
	@stack('scripts')
</body>

</html>
