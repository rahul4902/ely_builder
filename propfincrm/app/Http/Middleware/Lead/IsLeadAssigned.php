<?php

namespace App\Http\Middleware\Lead;

use Closure;
use App\Models\Setting;
use App\Models\Lead;

class IsLeadAssigned
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
        $lead = Lead::findOrFail($request->id);
        if (!$request->user()->can('assign', $lead)) {
            abort(403, 'Only a company administrator or team leader can assign this lead.');
        }
        return $next($request);
    }
}
