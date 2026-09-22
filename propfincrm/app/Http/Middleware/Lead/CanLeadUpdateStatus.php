<?php

namespace App\Http\Middleware\Lead;

use Closure;
use App\Models\Setting;
use App\Models\Lead;

class CanLeadUpdateStatus
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
        if (!$request->user()->can('complete', $lead)) {
            abort(403, 'Only the assigned user or a company administrator can close this lead.');
        }
        return $next($request);
    }
}
