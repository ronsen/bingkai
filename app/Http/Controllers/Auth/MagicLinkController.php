<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MagicLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MagicLinkController extends Controller
{
	/**
	 * Sign the user in using a valid magic link.
	 */
	public function __invoke(
		Request $request,
		string $user,
		MagicLinkService $magicLinkService,
	): RedirectResponse {
		$user = User::findOrFail($user);

		if (!$magicLinkService->consume($user, $request)) {
			abort(
				403,
				'This sign-in link has already been used or has expired.',
			);
		}

		if ($user->email_verified_at === null) {
			$user->forceFill(['email_verified_at' => now()])->save();
		}

		Auth::login($user, remember: true);

		$request->session()->regenerate();

		return redirect()
			->intended(route('images.index'))
			->with('success', 'Welcome back, ' . $user->name . '!');
	}
}
