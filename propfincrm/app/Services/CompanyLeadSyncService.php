<?php

namespace App\Services;

use App\Models\CompanyIntegration;
use App\Models\Lead;
use App\Support\LeadStatus;
use App\Services\LeadProviders\NinetyNineAcresLeadProvider;
use RuntimeException;

class CompanyLeadSyncService
{
    public function sync(CompanyIntegration $integration): array
    {
        if (!$integration->is_active) {
            throw new RuntimeException('This integration is disabled.');
        }
        $provider = match (strtolower($integration->provider)) {
            '99acres', '99_acres' => app(NinetyNineAcresLeadProvider::class),
            default => throw new RuntimeException("Unsupported lead provider [{$integration->provider}]."),
        };
        $result = ['received' => 0, 'created' => 0, 'duplicates' => 0];
        $assigneeId = $integration->company->activeUsers()
            ->wherePivotIn('role', ['company_admin', 'team_leader'])
            ->value('users.id')
            ?? $integration->company->activeUsers()->value('users.id')
            ?? $integration->company->users()->value('users.id');

        if (!$assigneeId) {
            throw new RuntimeException('The company needs at least one user before leads can be imported.');
        }

        try {
            foreach ($provider->fetch($integration) as $item) {
                $result['received']++;
                $lead = Lead::withoutGlobalScopes()->firstOrCreate(
                    [
                        'company_id' => $integration->company_id,
                        'provider' => $integration->provider,
                        'provider_lead_id' => $item['provider_lead_id'],
                    ],
                    [
                        'name' => $item['name'],
                        'contact_no' => $item['contact_no'],
                        'email' => $item['email'] ?? null,
                        'project' => $item['project'] ?? null,
                        'location' => $item['location'] ?? null,
                        'requirement' => $item['requirement'] ?? null,
                        'Budget' => $item['budget'] ?? null,
                        'source' => $integration->source?->source_name ?? $integration->provider,
                        'external_payload' => $item['payload'] ?? null,
                        'status' => LeadStatus::ACTIVE,
                        'user_assigned_id' => $assigneeId,
                        'user_created_id' => $assigneeId,
                    ]
                );

                if ($lead->wasRecentlyCreated) {
                    $result['created']++;
                } else {
                    $result['duplicates']++;
                }
            }

            $integration->update(['last_synced_at' => now(), 'last_error' => null]);
            return $result;
        } catch (\Throwable $e) {
            $integration->update(['last_error' => $e->getMessage()]);
            throw $e;
        }
    }
}
