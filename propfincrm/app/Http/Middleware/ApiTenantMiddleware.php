<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\Tenant\TenantHandler;
use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiTenantMiddleware
{
    use ApiResponseTrait;

    protected TenantHandler $tenantHandler;

    public function __construct(TenantHandler $tenantHandler)
    {
        $this->tenantHandler = $tenantHandler;
    }

    /**
     * Handle an incoming API request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Extract token from Bearer header or fallback parameter
        $token = $request->bearerToken();
        if (empty($token)) {
            $token = $request->input('key') ?? $request->input('token') ?? $request->header('X-API-Key');
        }

        if (empty($token)) {
            return $this->errorResponse('Authentication token is required.', 401);
        }

        // 2. Resolve User by token
        $user = User::where('user_token', $token)->first();
        if (!$user) {
            $tokenRow = DB::table('user_token')
                ->where('token', $token)
                ->where('status', 1)
                ->first();

            if ($tokenRow) {
                $user = User::find($tokenRow->user_id);
            }
        }

        if (!$user) {
            return $this->errorResponse('Invalid or expired authentication token.', 401);
        }

        // Bind authenticated user to Laravel request & auth guard
        $request->setUserResolver(fn () => $user);
        auth()->setUser($user);

        // 3. Resolve Company / Tenant
        $requestedCompanyId = (int) ($request->header('X-Company-Id') ?? $request->header('X-Tenant-Id') ?? $request->input('company_id'));
        $company = $this->tenantHandler->resolveForUser($user, $requestedCompanyId ?: null);

        if (!$company && !$user->isPlatformSuperAdmin()) {
            return $this->errorResponse('Your account has no active company workspace assigned.', 403);
        }

        if ($company) {
            if ($company->isExpired() && !$user->isPlatformSuperAdmin()) {
                return $this->errorResponse(
                    'Company subscription expired on ' . optional($company->plan_expires_at)->format('d M Y') . '. Please renew your plan.',
                    403,
                    ['subscription_expired' => true]
                );
            }

            // Activate tenant execution context
            $this->tenantHandler->activate($company);
            $request->attributes->set('active_company', $company);
        }

        $response = $next($request);

        if ($company && method_exists($response, 'header')) {
            $response->header('X-Company-Id', (string) $company->id);
        }

        return $response;
    }
}
