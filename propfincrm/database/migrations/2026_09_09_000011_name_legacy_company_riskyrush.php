<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // This was a one-time rename for the legacy `propfin_crm` database.
        // Tenant schema creation also executes the migration history, but
        // must never rename a newly registered company's local records.
        if (DB::connection()->getDatabaseName() !== config('database.connections.mysql.database')) {
            return;
        }

        if (!Schema::hasTable('companies')) {
            return;
        }

        $company = DB::table('companies')->orderBy('id')->first();

        if (!$company) {
            return;
        }

        DB::table('companies')->where('id', $company->id)->update([
            'name' => 'RiskyRush Company',
            'slug' => 'risky-rush-company',
            'updated_at' => now(),
        ]);

        if (Schema::hasTable('settings')) {
            DB::table('settings')->where('company_id', $company->id)->update([
                'company' => 'RiskyRush Company',
                'updated_at' => now(),
            ]);
        }

        if (Schema::hasTable('tenants')) {
            $tenant = DB::table('tenants')->where('id', $company->tenant_id)->first();

            if ($tenant) {
                $data = json_decode($tenant->data ?: '{}', true) ?: [];
                $data['company_name'] = 'RiskyRush Company';
                $data['tenancy_db_name'] = $data['tenancy_db_name'] ?? config('database.connections.mysql.database', 'propfin_crm');
                $data['tenancy_db_connection'] = $data['tenancy_db_connection'] ?? 'mysql';

                DB::table('tenants')->where('id', $company->tenant_id)->update([
                    'data' => json_encode($data),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // The previous name is not retained, so this intentional data rename
        // is not reversible.
    }
};
