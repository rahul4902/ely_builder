<?php

namespace App\Services;

use App\Models\CompanyIntegration;
use App\Models\Lead;
use App\Support\LeadStatus;
use App\Services\LeadProviders\NinetyNineAcresLeadProvider;
use App\Services\LeadProviders\MetaLeadAdsProvider;
use App\Services\Tenant\TenantHandler;
use App\Support\CurrentCompany;
use RuntimeException;
use Stancl\Tenancy\Tenancy;

class CompanyLeadSyncService
{
    public function sync(CompanyIntegration $integration): array
    {
        if (!$integration->is_active) {
            throw new RuntimeException('This integration is disabled.');
        }
        $provider = match (strtolower($integration->provider)) {
            '99acres', '99_acres' => app(NinetyNineAcresLeadProvider::class),
            'meta_lead_ads' => app(MetaLeadAdsProvider::class),
            default => throw new RuntimeException("Unsupported lead provider [{$integration->provider}]."),
        };

        try {
            $result = $this->import($integration, $provider->fetch($integration));
            $integration->update(['last_synced_at' => now(), 'last_error' => null]);
            return $result;
        } catch (\Throwable $e) {
            $integration->update(['last_error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Persist normalized provider records and suppress duplicate provider lead IDs.
     * This is also used by real-time webhooks, so polling and push sources follow
     * the exact same lead-assignment and audit path.
     */
    public function import(CompanyIntegration $integration, iterable $items): array
    {
        return $this->runInIntegrationContext($integration, function () use ($integration, $items): array {
        $result = ['received' => 0, 'created' => 0, 'duplicates' => 0];
        $assigneeId = $integration->company->activeUsers()
            ->wherePivotIn('role', ['company_admin', 'team_leader'])
            ->value('users.id')
            ?? $integration->company->activeUsers()->value('users.id')
            ?? $integration->company->users()->value('users.id');

        if (!$assigneeId) {
            throw new RuntimeException('The company needs at least one user before leads can be imported.');
        }

        foreach ($items as $item) {
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
                    'source' => $integration->source?->source_name ?? (config('lead_providers.' . $integration->provider . '.name') ?? $integration->provider),
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

        return $result;
        });
    }

    /**
     * Webhooks and queue workers do not have the signed-in user's tenant
     * middleware. Establish it from the integration before touching leads so
     * each incoming record lands in that company's normal All Leads database.
     */
    private function runInIntegrationContext(CompanyIntegration $integration, callable $callback)
    {
        $tenancy = app(Tenancy::class);
        $startedHere = !$tenancy->initialized;
        $currentCompany = app(CurrentCompany::class);
        $setCompanyHere = $startedHere && !$currentCompany->id();

        if ($startedHere && $setCompanyHere) {
            if ($integration->company->tenant) {
                app(TenantHandler::class)->activate($integration->company);
            } else {
                // Legacy/single-database workspaces still need company scoping,
                // but have no Stancl tenant to initialize.
                $currentCompany->set($integration->company);
            }
        }

        try {
            return $callback();
        } finally {
            if ($startedHere && $tenancy->initialized) {
                $tenancy->end();
            }
            if ($setCompanyHere) {
                $currentCompany->clear();
            }
        }
    }
}
