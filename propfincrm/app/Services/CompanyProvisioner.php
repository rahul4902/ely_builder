<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompanyProvisioner
{
    /**
     * Create the central tenant record, its company scope, default settings,
     * and the first company administrator as one all-or-nothing operation.
     */
    public function provision(array $attributes, ?User $administrator = null): Company
    {
        return DB::transaction(function () use ($attributes, $administrator) {
            $company = Company::create([
                'tenant_id' => (string) Str::uuid(),
                'name' => $attributes['name'],
                'slug' => $this->uniqueSlug($attributes['name']),
                'is_active' => true,
                'plan_id' => $attributes['plan_id'] ?? null,
                'plan_expires_at' => $attributes['plan_expires_at'] ?? now()->addDays(14),
                'subscription_status' => $attributes['subscription_status'] ?? 'active',
                'max_users' => $attributes['max_users'] ?? null,
            ]);

            Tenant::create([
                'id' => $company->tenant_id,
                // Tenant virtual attributes are persisted in its JSON data
                // column. Passing a literal `data` attribute would be removed
                // by the package serializer and leave it empty.
                'company_name' => $company->name,
            ]);

            Setting::withoutGlobalScopes()->create([
                'company_id' => $company->id,
                'company' => $company->name,
                'timezone' => $attributes['timezone'],
                'task_complete_allowed' => 2,
                'task_assign_allowed' => 2,
                'lead_complete_allowed' => 2,
                'lead_assign_allowed' => 2,
                'time_change_allowed' => 2,
                'comment_allowed' => 2,
            ]);

            if ($administrator) {
                $company->users()->syncWithoutDetaching([
                    $administrator->id => ['role' => 'company_admin', 'is_active' => true],
                ]);
                $this->ensureLegacyAdministratorRole($administrator);
                app(TenantUserSynchronizer::class)->syncUser($company, $administrator);
            }

            return $company;
        });
    }

    public function registerAndProvision(array $attributes): array
    {
        return DB::transaction(function () use ($attributes) {
            $user = User::create([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'password' => Hash::make($attributes['password']),
            ]);

            $trialPlan = \App\Models\Plan::where('slug', 'trial')->first()
                ?? \App\Models\Plan::where('slug', 'starter')->first();

            $company = $this->provision([
                'name' => $attributes['company_name'],
                'timezone' => $attributes['timezone'],
                'plan_id' => $trialPlan?->id,
                'plan_expires_at' => now()->addDays(14),
                'subscription_status' => 'trial',
                'max_users' => $trialPlan?->max_users ?? 5,
            ], $user);

            if ($trialPlan) {
                \App\Models\CompanySubscription::create([
                    'company_id' => $company->id,
                    'plan_id' => $trialPlan->id,
                    'starts_at' => now(),
                    'expires_at' => now()->addDays(14),
                    'status' => 'trial',
                    'billing_cycle' => $trialPlan->billing_cycle ?? 'monthly',
                    'price_paid' => 0.00,
                    'notes' => '14-Day Free Trial assigned upon company registration',
                ]);
            }

            return [$company, $user];
        });
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'company';
        $slug = $base;
        $suffix = 2;

        while (Company::withoutGlobalScopes()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }

    private function ensureLegacyAdministratorRole(User $user): void
    {
        // A few legacy screens still consult Entrust. Keeping this bridge makes
        // a newly provisioned administrator usable immediately while company_user
        // remains the source of truth for tenant membership.
        $role = Role::where('name', 'administrator')->first();
        if ($role && !$user->hasRole('administrator')) {
            $user->attachRole($role);
        }
    }
}
