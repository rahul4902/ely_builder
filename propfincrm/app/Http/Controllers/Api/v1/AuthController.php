<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\Api\CompanyResource;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use App\Services\Tenant\TenantHandler;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected TenantHandler $tenantHandler;

    public function __construct(TenantHandler $tenantHandler)
    {
        $this->tenantHandler = $tenantHandler;
    }

    /**
     * Authenticate user, resolve tenant workspace, and generate API credentials.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $mobile = $request->input('mobile');
        $email = $request->input('email');
        $password = $request->input('password');
        $pushtoken = $request->input('pushtoken');
        $requestedCompanyId = $request->input('company_id') ?: ($request->header('X-Company-Id') ?: null);

        // 1. Locate user by mobile (work/personal) or email
        $user = User::query()
            ->when($mobile, function ($query, $mobile) {
                return $query->where(function ($q) use ($mobile) {
                    $q->where('work_number', $mobile)
                      ->orWhere('personal_number', $mobile);
                });
            })
            ->when($email && !$mobile, function ($query, $email) {
                return $query->where('email', $email);
            })
            ->first();

        if (!$user) {
            return $this->errorResponse('Account not found with the provided credentials.', 401, null, [
                'record' => [['msg' => 'Fail..!', 'status' => 0]],
            ]);
        }

        // 2. Validate password if provided
        if (!empty($password) && !Hash::check($password, $user->password)) {
            return $this->errorResponse('Invalid password.', 401, null, [
                'record' => [['msg' => 'Fail..!', 'status' => 0]],
            ]);
        }

        // 3. Resolve Company & Tenant Context
        $company = $this->tenantHandler->resolveForUser($user, $requestedCompanyId ? (int) $requestedCompanyId : null);

        if (!$company && !$user->isPlatformSuperAdmin()) {
            return $this->errorResponse('User does not have an active company workspace.', 403, null, [
                'record' => [['msg' => 'No active workspace..!', 'status' => 0]],
            ]);
        }

        if ($company && $company->isExpired() && !$user->isPlatformSuperAdmin()) {
            return $this->errorResponse(
                'Your company workspace subscription has expired on ' . optional($company->plan_expires_at)->format('d M Y') . '.',
                403,
                ['subscription_expired' => true],
                ['record' => [['msg' => 'Subscription expired..!', 'status' => 0]]]
            );
        }

        // 4. Activate tenant context
        if ($company) {
            $this->tenantHandler->activate($company);
            $request->attributes->set('active_company', $company);
        }

        // 5. Generate / maintain secure user token
        $token = (!empty($user->user_token) && strlen($user->user_token) >= 10)
            ? $user->user_token
            : md5(uniqid(mt_rand(), true));

        $user->user_token = $token;
        if (!empty($pushtoken)) {
            $user->pushtoken = $pushtoken;
        }
        $user->save();

        // Register token in user_token session tracker
        DB::table('user_token')->updateOrInsert(
            ['user_id' => $user->id, 'token' => $token],
            ['status' => 1]
        );

        // 6. Assemble best-practice response + legacy compatibility layer
        $availableCompanies = $this->tenantHandler->getAvailableCompaniesForUser($user);

        $responseData = [
            'token'               => $token,
            'token_type'          => 'Bearer',
            'user'                => new UserResource($user),
            'company'             => $company ? new CompanyResource($company) : null,
            'available_companies' => $availableCompanies,
        ];

        $legacyRecord = [
            [
                'msg'    => 'Success..!',
                'status' => 1,
                'Name'   => $user->name,
                'key'    => $token,
            ],
        ];

        $response = $this->successResponse($responseData, 'Login successful.', 200, [
            'record' => $legacyRecord,
        ]);

        if ($company) {
            $response->header('X-Company-Id', (string) $company->id);
        }

        return $response;
    }

    /**
     * Get authenticated user profile and active workspace information.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $company = $request->attributes->get('active_company') ?? $user->activeCompany();
        $availableCompanies = $this->tenantHandler->getAvailableCompaniesForUser($user);

        return $this->successResponse([
            'user'                => new UserResource($user),
            'company'             => $company ? new CompanyResource($company) : null,
            'available_companies' => $availableCompanies,
        ], 'Profile retrieved successfully.');
    }

    /**
     * Switch active company workspace for the authenticated multi-tenant user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function switchCompany(Request $request): JsonResponse
    {
        $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        $user = $request->user();
        $companyId = (int) $request->input('company_id');

        $company = $user->companies()
            ->whereKey($companyId)
            ->wherePivot('is_active', true)
            ->where('companies.is_active', true)
            ->first();

        if (!$company) {
            return $this->errorResponse('You do not have access to this company workspace.', 403);
        }

        if ($company->isExpired() && !$user->isPlatformSuperAdmin()) {
            return $this->errorResponse(
                'This company workspace subscription has expired.',
                403,
                ['subscription_expired' => true]
            );
        }

        // Activate new tenant context
        $this->tenantHandler->activate($company);
        $request->attributes->set('active_company', $company);

        return $this->successResponse([
            'company' => new CompanyResource($company),
        ], 'Switched active company workspace successfully.');
    }

    /**
     * Log out user and invalidate authentication token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken() ?? $request->input('key') ?? $request->input('token');

        if ($token) {
            DB::table('user_token')->where('token', $token)->update(['status' => 0]);
        }

        return $this->successResponse(null, 'Successfully logged out.');
    }
}
