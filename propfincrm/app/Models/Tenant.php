<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;

    public function company()
    {
        return $this->hasOne(Company::class, 'tenant_id');
    }

    public function getInternal(string $key)
    {
        $value = parent::getInternal($key);

        if ($key === 'db_name' && empty($value)) {
            $company = Company::where('tenant_id', $this->getTenantKey())->first();
            if ($company && $company->id === 1) {
                return config('database.connections.mysql.database', 'propfin_crm');
            }
        }

        if ($key === 'db_connection' && empty($value)) {
            $company = Company::where('tenant_id', $this->getTenantKey())->first();
            if ($company && $company->id === 1) {
                return 'mysql';
            }
        }

        return $value;
    }
}
