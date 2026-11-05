@extends('layouts.app')

@section('title', 'Sign In — ' . config('app.name'))

@section('content')
	<div class="max-w-md mx-auto pt-8 pb-16">
		<div class="text-center mb-8">
			<div class="flex items-center justify-center">
				<x-logo />
			</div>
			<h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Sign in to your account</h1>
			<p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
				Enter your email and we'll send you a secure sign-in link. No password needed.
			</p>
		</div>

		@if (session('status'))
			<div
				class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm">
				{{ session('status') }}
			</div>
		@endif

		@if ($errors->any())
			<div
				class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 text-sm">
				<ul class="list-disc list-inside space-y-0.5 text-xs">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<div
			class="bg-white dark:bg-zinc-900/90 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 p-6 sm:p-8 shadow-xs">
			<form action="{{ route('login.store') }}" method="POST" class="space-y-5">
				@csrf

				<!-- Email Address -->
				<div>
					<label for="email"
						class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5">
						Email Address
					</label>
					<input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
						autocomplete="username"
						class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-sm transition-shadow"
						placeholder="you@example.com" />
				</div>

				<!-- Submit Button -->
				<button type="submit"
					class="w-full py-2.5 px-4 rounded-xl text-sm font-semibold bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white shadow transition-all hover:scale-[1.01] active:scale-[0.99]">
					Email Me a Sign-In Link
				</button>
			</form>
		</div>
	</div>
@endsection
