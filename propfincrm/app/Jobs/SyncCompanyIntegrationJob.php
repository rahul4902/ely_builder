<?php

namespace App\Jobs;

use App\Models\CompanyIntegration;
use App\Services\CompanyLeadSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SyncCompanyIntegrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 90;
    public array $backoff = [60, 300, 900];

    public function __construct(public int $integrationId)
    {
        $this->onQueue('integrations');
    }

    public function handle(CompanyLeadSyncService $sync): void
    {
        // Queue workers have no request-level company context. Deliberately
        // load by ID, then the service scopes every lead by this integration's company.
        $integration = CompanyIntegration::withoutGlobalScopes()->findOrFail($this->integrationId);

        if ($integration->is_active) {
            $sync->sync($integration);
        }
    }

    public function failed(Throwable $exception): void
    {
        $integration = CompanyIntegration::withoutGlobalScopes()->find($this->integrationId);
        if ($integration) {
            $integration->update(['last_error' => $exception->getMessage()]);
        }
    }
}
