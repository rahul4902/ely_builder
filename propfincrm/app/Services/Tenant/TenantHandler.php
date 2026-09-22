<?php

namespace App\Services\Tenant;

use App\Models\Company;
use App\Models\User;
use App\Support\CurrentCompany;
use Stancl\Tenancy\Tenancy;

class TenantHandler
{
    /**
     * Resolve the active company for the given user.
     *
     * @param User $user
     * @param int|null $requestedCompanyId
     * @return Company|null
     */
    public function resolveForUser(User $user, ?int $requestedCompanyId = null): ?Company
    {
        // 1. If an explicit company_id is provided, verify user's active membership
        if ($requestedCompanyId) {
            $company = $user->companies()
                ->whereKey($requestedCompanyId)
                ->wherePivot('is_active', true)
                ->where('companies.is_active', true)
                ->first();

            if ($company) {
                return $company;
            }
        }

        // 2. Default to user's first active company membership
        return $user->companies()
            ->wherePivot('is_active', true)
            ->where('companies.is_active', true)
            ->orderBy('companies.id')
            ->first();
    }

    /**
     * Activate the company and its tenant execution context.
     *
     * @param Company $company
     * @return void
     */
    public function activate(Company $company): void
    {
        // Bind CurrentCompany singleton
        app(CurrentCompany::class)->set($company);

        // Configure company timezone if configured
        $companyTimezone = $company->settings?->timezone;
        if ($companyTimezone && in_array($companyTimezone, timezone_identifiers_list(), true)) {
            config(['app.timezone' => $companyTimezone]);
            date_default_timezone_set($companyTimezone);
        }

        // Initialize Stancl Tenancy context if tenant record exists
        if ($company->tenant && class_exists(Tenancy::class)) {
            try {
                app(Tenancy::class)->initialize($company->tenant);
            } catch (\Throwable $e) {
                \Log::warning("Could not initialize tenant database for company {$company->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Get all active companies accessible by the user.
     *
     * @param User $user
     * @return \Illuminate\Support\Collection
     */
    public function getAvailableCompaniesForUser(User $user)
    {
        return $user->companies()
            ->wherePivot('is_active', true)
            ->where('companies.is_active', true)
            ->get()
            ->map(function ($comp) {
                return [
                    'id'                  => (int) $comp->id,
                    'tenant_id'           => $comp->tenant_id,
                    'name'                => $comp->name,
                    'slug'                => $comp->slug,
                    'role'                => $comp->pivot->role ?? 'employee',
                    'is_expired'          => $comp->isExpired(),
                    'subscription_status' => $comp->subscription_status,
                ];
            });
    }
}
