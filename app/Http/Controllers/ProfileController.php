<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
	/**
	 * Show the profile edit form.
	 */
	public function edit(Request $request): View
	{
		return view('profile.edit', [
			'user' => $request->user(),
		]);
	}

	/**
	 * Update user information (name and email).
	 */
	public function update(Request $request): RedirectResponse
	{
		$user = $request->user();

		$validated = $request->validate([
			'name' => ['required', 'string', 'max:255'],
			'email' => [
				'required',
				'string',
				'email',
				'max:255',
				Rule::unique('users')->ignore($user->id),
			],
		]);

		$user->fill($validated);
		$user->save();

		return redirect()
			->route('profile.edit')
			->with('status', 'profile-updated')
			->with('success', 'Profile information updated successfully.');
	}

	/**
	 * Set or update the user password.
	 */
	public function updatePassword(Request $request): RedirectResponse
	{
		$user = $request->user();

		$rules = [
			'password' => ['required', 'confirmed', Password::defaults()],
		];

		if ($user->password !== null) {
			$rules['current_password'] = ['required', 'current_password'];
		}

		$validated = $request->validate($rules);

		$user->update([
			'password' => $validated['password'],
		]);

		return redirect()
			->route('profile.edit')
			->with('status', 'password-updated')
			->with('success', 'Password updated successfully.');
	}
}
