<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalAccess
{
    public function handle(Request $request, Closure $next, string $portal): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route($portal.'.login');
        }

        if ($user->role === 'master_admin') {
            return $next($request);
        }

        if ($user->role === 'employee' && $user->portal === $portal) {
            return $next($request);
        }

        abort(403);
    }
}