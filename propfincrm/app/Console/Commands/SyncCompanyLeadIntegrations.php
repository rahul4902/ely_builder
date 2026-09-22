<?php

namespace App\Console\Commands;

use App\Models\CompanyIntegration;
use App\Jobs\SyncCompanyIntegrationJob;
use Illuminate\Console\Command;

class SyncCompanyLeadIntegrations extends Command
{
    protected $signature = 'leads:sync-integrations {--company= : Filter by company ID} {--id= : Sync a specific integration ID} {--force : Force sync even if not due or manual} {--now : Run synchronously in console without queuing (ideal for shared hosting crons)}';
    protected $description = 'Sync active company lead integrations that are due.';

    public function handle(\App\Services\CompanyLeadSyncService $syncService): int
    {
        $query = CompanyIntegration::withoutGlobalScopes()->where('is_active', true);

        if ($id = $this->option('id')) {
            $query->where('id', $id);
        }

        if ($company = $this->option('company')) {
            $query->where('company_id', $company);
        }

        $integrations = $query->get();

        if ($integrations->isEmpty()) {
            $this->comment('No active integrations found to sync.');
            return self::SUCCESS;
        }

        $force = (bool) $this->option('force');
        $now = (bool) $this->option('now');

        foreach ($integrations as $integration) {
            if (!$force && !$this->isDue($integration)) {
                continue;
            }

            if ($now) {
                $this->line("Syncing {$integration->name} (provider: {$integration->provider})...");
                try {
                    $result = $syncService->sync($integration);
                    $this->info("✓ {$integration->name}: {$result['received']} received, {$result['created']} created, {$result['duplicates']} duplicate.");
                } catch (\Throwable $e) {
                    $this->error("✗ {$integration->name} failed: " . $e->getMessage());
                }
            } else {
                SyncCompanyIntegrationJob::dispatch($integration->id);
                $this->info("✓ {$integration->name}: queued in background.");
            }
        }

        return self::SUCCESS;
    }

    public function isDue(CompanyIntegration $integration): bool
    {
        if ($integration->sync_frequency === 'manual') {
            return false;
        }

        if (!$integration->last_synced_at) {
            return true;
        }

        return match ($integration->sync_frequency) {
            'every_15_minutes' => $integration->last_synced_at->lte(now()->subMinutes(15)),
            'every_30_minutes' => $integration->last_synced_at->lte(now()->subMinutes(30)),
            'hourly'           => $integration->last_synced_at->lte(now()->subHour()),
            'twice_daily'      => $integration->last_synced_at->lte(now()->subHours(12)),
            'daily'            => $integration->last_synced_at->lte(now()->subDay()),
            'weekly'           => $integration->last_synced_at->lte(now()->subWeek()),
            default            => $integration->last_synced_at->lte(now()->subHour()),
        };
    }
}
