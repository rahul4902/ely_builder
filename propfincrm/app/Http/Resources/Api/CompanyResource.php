<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $user = $request->user();

        return [
            'id'                  => (int) $this->id,
            'tenant_id'           => $this->tenant_id,
            'name'                => $this->name,
            'slug'                => $this->slug,
            'is_active'           => (bool) $this->is_active,
            'is_expired'          => $this->isExpired(),
            'subscription_status' => $this->subscription_status,
            'plan_name'           => $this->planName(),
            'plan_expires_at'     => optional($this->plan_expires_at)->toIso8601String(),
            'days_remaining'      => $this->daysRemaining(),
            'user_role'           => $user ? $user->companyRole($this->resource) : null,
        ];
    }
}
