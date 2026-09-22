<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Support\CurrentCompany;
use Closure;
use Stancl\Tenancy\Tenancy;

class ResolveActiveCompany
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        $companyId = $request->session()->get('active_company_id');

        // A multi-company member must choose a workspace before this request
        // establishes a tenant database connection.
        if ($request->routeIs('companies.select') && !$companyId) {
            app(CurrentCompany::class)->clear();

            return $next($request);
        }

        $impersonatedCompanyId = $request->session()->get('impersonating_company_id');
        if ($impersonatedCompanyId && $user->isPlatformSuperAdmin()) {
            $company = Company::find($impersonatedCompanyId);
        } else {
            $company = $companyId
                ? $user->companies()->whereKey($companyId)->wherePivot('is_active', true)->first()
                : $user->companies()->wherePivot('is_active', true)->orderBy('companies.id')->first();

            // Session values are not an authority boundary. If a context belongs
            // to a previous login or has been removed, discard it and resolve a
            // valid active membership for the authenticated account instead.
            if (!$company && $companyId) {
                $request->session()->forget('active_company_id');
                $company = $user->companies()
                    ->wherePivot('is_active', true)
                    ->where('companies.is_active', true)
                    ->orderBy('companies.id')
                    ->first();
            }
        }

        // Platform operators can administer the tenant registry even when they
        // are not members of a customer company. They must not gain a tenant
        // context as a side effect of that access unless explicitly impersonating.
        if ((!$company || !$company->is_active) && $user->isPlatformSuperAdmin()) {
            if ($request->is('platform*') || $request->is('logout')) {
                app(CurrentCompany::class)->clear();

                return $next($request);
            }

            return redirect()->route('platform.companies.index');
        }

        if (!$company || !$company->is_active) {
            abort(403, 'Your account does not have access to an active company, or the company workspace has been suspended.');
        }

        // Subscription expiration check for tenant users (Superadmin exempt)
        if ($company->isExpired() && !$user->isPlatformSuperAdmin()) {
            $allowedRoutes = ['tickets.*', 'tenant.subscription*', 'logout'];
            $isAllowed = false;
            foreach ($allowedRoutes as $allowed) {
                if ($request->routeIs($allowed) || $request->is('logout') || $request->is('subscription*')) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                return redirect()->route('tenant.subscription')
                    ->with('error_message', 'Your subscription expired on ' . optional($company->plan_expires_at)->format('d M Y') . '. Please renew your plan below to continue.');
            }
        }

        $request->session()->put('active_company_id', $company->id);
        app(CurrentCompany::class)->set($company);
        $previousApplicationTimezone = config('app.timezone');
        $previousPhpTimezone = date_default_timezone_get();
        $companyTimezone = $company->settings?->timezone;

        if ($companyTimezone && in_array($companyTimezone, timezone_identifiers_list(), true)) {
            config(['app.timezone' => $companyTimezone]);
            date_default_timezone_set($companyTimezone);
        }

        $tenant = $company->tenant;
        if ($tenant) {
            app(Tenancy::class)->initialize($tenant);
        }

        try {
            return $next($request);
        } finally {
            if (app(Tenancy::class)->initialized) {
                app(Tenancy::class)->end();
            }
            config(['app.timezone' => $previousApplicationTimezone]);
            date_default_timezone_set($previousPhpTimezone);
            app(CurrentCompany::class)->clear();
        }
    }
}
