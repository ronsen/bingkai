<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowedRoles = array_filter(array_map(Role::tryFrom(...), $roles));
        $currentRole = $request->user()?->role ?? Role::Guest;

        if (! in_array($currentRole, $allowedRoles, true)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
