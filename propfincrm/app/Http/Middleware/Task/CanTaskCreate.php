<?php

namespace App\Http\Middleware\Task;

use Closure;

class CanTaskCreate
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
        abort_unless($request->user()->can('create', \App\Models\Task::class), 403, 'Not allowed to create a task.');
        return $next($request);
    }
}
