<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MagicLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        protected MagicLinkService $magicLinkService
    ) {}

    /**
     * Show the login form.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended(route('images.index'));
        }

        return view('auth.login');
    }

    /**
     * Email a magic sign-in link to the user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user) {
            $this->magicLinkService->send($user);
        }

        return redirect()->route('login')
            ->with('status', 'If an account exists for that email address, we have sent a sign-in link.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('images.index')
            ->with('success', 'You have been signed out.');
    }
}
