<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\MagicLinkNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class MagicLinkService
{
    /**
     * Number of minutes a magic link remains valid.
     */
    public const EXPIRES_IN_MINUTES = 15;

    /**
     * Generate a single-use, temporary signed sign-in URL and email it to the user.
     */
    public function send(User $user): void
    {
        $token = Str::random(40);

        $url = URL::temporarySignedRoute(
            'login.magic',
            now()->addMinutes(self::EXPIRES_IN_MINUTES),
            [
                'user' => $user->getKey(),
                'email' => $this->emailHash($user),
                'token' => $token,
            ],
        );

        Cache::put(
            $this->cacheKey($user),
            hash('sha256', $token),
            now()->addMinutes(self::EXPIRES_IN_MINUTES),
        );

        $user->notify(new MagicLinkNotification($url));
    }

    /**
     * Verify and consume the magic link for the given user.
     */
    public function consume(User $user, Request $request): bool
    {
        $expectedHash = Cache::pull($this->cacheKey($user));
        $token = $request->query('token');
        $emailHash = $request->query('email');

        if (! is_string($expectedHash) || ! is_string($token) || $token === '' || ! is_string($emailHash)) {
            return false;
        }

        if (! hash_equals($expectedHash, hash('sha256', $token))) {
            return false;
        }

        return hash_equals($this->emailHash($user), $emailHash);
    }

    /**
     * Hash the user's email so a link stops working if the address changes.
     */
    private function emailHash(User $user): string
    {
        return hash('sha256', $user->email);
    }

    /**
     * Build the cache key that stores the active magic link hash.
     */
    private function cacheKey(User $user): string
    {
        return 'magic-link:'.$user->getKey();
    }
}
