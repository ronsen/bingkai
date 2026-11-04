@extends('layouts.app')

@section('title', 'Account Settings — Bingkai Photography')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Account Settings</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
            Manage your profile details and update your security credentials.
        </p>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 text-sm">
            <div class="font-semibold mb-1">Please correct the following:</div>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Profile Information Section -->
    <div class="bg-white dark:bg-zinc-900/90 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 p-6 sm:p-8 shadow-xs">
        <div class="mb-6 pb-4 border-b border-zinc-100 dark:border-zinc-800">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Profile Information</h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                Update your account's display name and primary email address.
            </p>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5">
                    Name
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $user->name) }}"
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-sm"
                />
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5">
                    Email Address
                </label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-sm"
                />
            </div>

            <div class="flex items-center justify-end pt-2">
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl text-xs font-semibold bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white shadow-xs transition-all hover:scale-[1.01] active:scale-[0.99]"
                >
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Update Password Section -->
    <div class="bg-white dark:bg-zinc-900/90 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 p-6 sm:p-8 shadow-xs">
        <div class="mb-6 pb-4 border-b border-zinc-100 dark:border-zinc-800">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $user->password ? 'Update Password' : 'Set a Password' }}
            </h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                @if ($user->password)
                    Your account signs in with magic links. You may update the password stored on your account here.
                @else
                    Your account is passwordless and signs in with magic links. You may optionally set a password here.
                @endif
            </p>
        </div>

        <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            @if ($user->password)
                <div>
                    <label for="current_password" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5">
                        Current Password
                    </label>
                    <input
                        type="password"
                        name="current_password"
                        id="current_password"
                        required
                        autocomplete="current-password"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-sm"
                        placeholder="••••••••"
                    />
                </div>
            @endif

            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5">
                    New Password
                </label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    autocomplete="new-password"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-sm"
                    placeholder="••••••••"
                />
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 mb-1.5">
                    Confirm New Password
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 text-sm"
                    placeholder="••••••••"
                />
            </div>

            <div class="flex items-center justify-end pt-2">
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl text-xs font-semibold bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white shadow-xs transition-all hover:scale-[1.01] active:scale-[0.99]"
                >
                    {{ $user->password ? 'Update Password' : 'Set Password' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
