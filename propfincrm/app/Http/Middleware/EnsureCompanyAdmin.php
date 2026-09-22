<?php

namespace App\Http\Middleware;

use Closure;

class EnsureCompanyAdmin
{
    public function handle($request, Closure $next)
    {
        if (!$request->user() || !$request->user()->isCompanyAdmin()) {
            abort(403, 'Company administrator access is required.');
        }

        return $next($request);
    }
}
