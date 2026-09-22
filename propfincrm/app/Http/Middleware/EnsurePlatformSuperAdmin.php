<?php

namespace App\Http\Middleware;

use Closure;

class EnsurePlatformSuperAdmin
{
    public function handle($request, Closure $next)
    {
        abort_unless($request->user()?->isPlatformSuperAdmin(), 403, 'Platform Super Admin access is required.');

        return $next($request);
    }
}
