<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchedulerToken
{
    public function handle($request, Closure $next)
    {
        $expected = config('services.scheduler.token');
        $provided = $request->header('X-Scheduler-Token', $request->bearerToken());

        if (!$expected || !$provided || !hash_equals($expected, $provided)) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
