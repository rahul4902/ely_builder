<?php

namespace App\Http\Middleware\Task;

use Closure;
use App\Models\Setting;
use App\Models\Task;

class CanTaskUpdateStatus
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
        $task = Task::findOrFail($request->id);
        if (!$request->user()->can('complete', $task)) {
            abort(403, 'Only the assigned user or a company administrator can close this task.');
        }
        return $next($request);
    }
}
