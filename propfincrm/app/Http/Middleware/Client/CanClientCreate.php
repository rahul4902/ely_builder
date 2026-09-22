<?php

namespace App\Http\Middleware\Client;

use Closure;

class CanClientCreate
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
        abort_unless($request->user()->can('create', \App\Models\Client::class), 403, 'Not allowed to create a client.');

        return $next($request);
    }
}
