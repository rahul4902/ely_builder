<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function view(User $user, Company $company): bool
    {
        return $user->belongsToCompany($company);
    }

    public function manage(User $user, Company $company): bool
    {
        return $user->isCompanyAdmin($company);
    }
}
