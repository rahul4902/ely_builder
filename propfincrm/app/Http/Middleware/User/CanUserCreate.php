<?php

namespace App\Http\Middleware\User;

use Closure;

class CanUserCreate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        abort_unless($request->user()->can('create', \App\Models\User::class), 403, 'Not allowed to create users.');
        return $next($request);
    }
}
