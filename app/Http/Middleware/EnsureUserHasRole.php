<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role gate for route groups (agent.md Sections 8 & 11 — hard requirement).
 *
 * Usage: ->middleware('role:admin') on /admin/* routes,
 *        ->middleware('role:specialist') on /specialist/* routes.
 * Accepts multiple roles (role:admin,specialist) for shared routes if ever
 * needed, though none currently are.
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        abort_unless($user->hasRole(...$roles), 403);

        return $next($request);
    }
}
