<?php

namespace App\Http\Middleware\Task;

use Closure;
use App\Models\Setting;
use App\Models\Task;

class IsTaskAssigned
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
        if (!$request->user()->can('assign', $task)) {
            abort(403, 'Only a company administrator or team leader can change task assignments.');
        }
        return $next($request);
    }
}
