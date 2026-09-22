<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantUserSynchronizer
{
    /**
     * Copy the non-authoritative member record required by the legacy tenant
     * schema's foreign keys. Authentication and memberships remain central.
     */
    public function syncUser(Company $company, User $user): void
    {
        $tenant = $company->tenant;

        if (!$tenant) {
            return;
        }

        $tenant->run(function () use ($user): void {
            if (!Schema::hasTable('users')) {
                return;
            }

            $columns = Schema::getColumnListing('users');
            $attributes = Arr::only($user->getAttributes(), $columns);
            $attributes['id'] = $user->getKey();

            DB::table('users')->updateOrInsert(
                ['id' => $user->getKey()],
                $attributes
            );
        });
    }

    public function syncCompanyUsers(Company $company): void
    {
        foreach ($company->activeUsers()->get() as $user) {
            $this->syncUser($company, $user);
        }
    }
}
