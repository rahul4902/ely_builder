<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\Company;
use App\Services\TenantUserSynchronizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RepairTenantDatabase extends Command
{
    protected $signature = 'tenancy:repair-database {tenant : The tenant UUID}';

    protected $description = 'Create and migrate a missing tenant database without changing central tenant records';

    public function handle(TenantUserSynchronizer $users): int
    {
        $tenant = Tenant::find($this->argument('tenant'));

        if (!$tenant) {
            $this->error('Tenant was not found in the central registry.');

            return self::FAILURE;
        }

        $database = $tenant->database();
        $database->makeCredentials();
        $manager = $database->manager();

        if (!$manager->databaseExists($database->getName())) {
            $manager->createDatabase($tenant);
            $this->info("Created tenant database {$database->getName()}.");
        } else {
            $this->line("Tenant database {$database->getName()} already exists.");
        }

        $exitCode = Artisan::call('tenants:migrate', [
            '--tenants' => [$tenant->getTenantKey()],
        ]);

        $this->output->write(Artisan::output());

        if ($exitCode !== self::SUCCESS) {
            $this->error('Tenant database migration failed.');

            return $exitCode;
        }

        $company = Company::where('tenant_id', $tenant->getTenantKey())->first();
        if ($company) {
            $users->syncCompanyUsers($company);
        }

        $this->info('Tenant database is ready.');

        return self::SUCCESS;
    }
}
