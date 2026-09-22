<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $activeCompany = $request->attributes->get('active_company') ?? $this->activeCompany();

        return [
            'id'              => (int) $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'work_number'     => $this->work_number,
            'personal_number' => $this->personal_number,
            'address'         => $this->address,
            'role'            => $this->companyRole($activeCompany) ?? ($this->hasRole('administrator') ? 'company_admin' : 'employee'),
            'is_admin'        => $this->isCompanyAdmin($activeCompany),
            'image_path'      => $this->image_path,
            'created_at'      => optional($this->created_at)->toIso8601String(),
        ];
    }
}
