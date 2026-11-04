<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MagicLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration form.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended(route('images.index'));
        }

        return view('auth.register');
    }

    /**
     * Create a passwordless account and email a magic sign-in link.
     */
    public function store(Request $request, MagicLinkService $magicLinkService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => null,
            'role' => Role::User,
        ]);

        $magicLinkService->send($user);

        return redirect()->route('login')
            ->with('status', 'Your account has been created. We have emailed you a sign-in link.');
    }
}
