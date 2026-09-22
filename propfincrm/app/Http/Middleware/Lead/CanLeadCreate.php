<?php

namespace App\Http\Middleware\Lead;

use Closure;

class CanLeadCreate
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
        abort_unless($request->user()->can('create', \App\Models\Lead::class), 403, 'Not allowed to create a lead.');
        return $next($request);
    }
}
