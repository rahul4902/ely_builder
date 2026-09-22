<?php

namespace App\Support;

use App\Models\Company;

class CurrentCompany
{
    private $company;

    public function set(Company $company): void
    {
        $this->company = $company;
    }

    public function get(): ?Company
    {
        return $this->company;
    }

    public function id(): ?int
    {
        return $this->company ? (int) $this->company->id : null;
    }

    public function clear(): void
    {
        $this->company = null;
    }
}
