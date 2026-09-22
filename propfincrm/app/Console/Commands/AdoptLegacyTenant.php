<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdoptLegacyTenant extends Command
{
    protected $signature = 'tenancy:adopt-legacy {--force : Confirm that the central database was freshly migrated}';

    protected $description = 'Copies central identity data and adopts the configured legacy database as RiskyRush Company.';

    public function handle(): int
    {
        if (!$this->option('force')) {
            $this->error('Run this command with --force after migrating the fresh central database.');

            return self::FAILURE;
        }

        $legacy = DB::connection('mysql');
        $central = DB::connection('central');
        $legacyCompany = $legacy->table('companies')->orderBy('id')->first();

        if (!$legacyCompany) {
            $this->error('No company was found in the legacy database.');

            return self::FAILURE;
        }

        $tables = [
            'users', 'roles', 'permissions', 'role_user', 'permission_role',
            'tenants', 'companies', 'company_user', 'settings',
            'company_integrations', 'domains',
        ];

        foreach ($tables as $table) {
            if (!Schema::connection('mysql')->hasTable($table) || !Schema::connection('central')->hasTable($table)) {
                $this->error("Required table [{$table}] is missing from the legacy or central database.");

                return self::FAILURE;
            }
        }

        $central->transaction(function () use ($central, $legacy, $tables, $legacyCompany) {
            $central->statement('SET FOREIGN_KEY_CHECKS = 0');

            try {
                foreach (array_reverse($tables) as $table) {
                    $central->table($table)->delete();
                }

                foreach ($tables as $table) {
                    $columns = Schema::connection('mysql')->getColumnListing($table);
                    $orderBy = in_array('id', $columns, true) ? 'id' : $columns[0];

                    $legacy->table($table)->orderBy($orderBy)->chunk(500, function ($rows) use ($central, $table) {
                        $central->table($table)->insert(array_map(fn ($row) => (array) $row, $rows->all()));
                    });
                }

                $data = json_decode(
                    (string) $central->table('tenants')->where('id', $legacyCompany->tenant_id)->value('data'),
                    true
                ) ?: [];
                $data['company_name'] = 'RiskyRush Company';
                $data['tenancy_db_name'] = config('database.connections.mysql.database');
                $data['tenancy_db_connection'] = 'mysql';

                $central->table('companies')->where('id', $legacyCompany->id)->update([
                    'name' => 'RiskyRush Company',
                    'slug' => 'risky-rush-company',
                    'updated_at' => now(),
                ]);
                $central->table('settings')->where('company_id', $legacyCompany->id)->update([
                    'company' => 'RiskyRush Company',
                    'updated_at' => now(),
                ]);
                $central->table('tenants')->where('id', $legacyCompany->tenant_id)->update([
                    'data' => json_encode($data),
                    'updated_at' => now(),
                ]);
            } finally {
                $central->statement('SET FOREIGN_KEY_CHECKS = 1');
            }
        });

        $this->info('Adopted propfin_crm as RiskyRush Company and copied central identity data.');

        return self::SUCCESS;
    }
}
