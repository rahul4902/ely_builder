<?php

namespace App\Services\LeadProviders;

use App\Models\CompanyIntegration;

interface LeadProviderContract
{
    /** @return array<int, array{name:string,contact_no:string,email?:string,project?:string,provider_lead_id?:string,payload?:array}> */
    public function fetch(CompanyIntegration $integration): array;
}
