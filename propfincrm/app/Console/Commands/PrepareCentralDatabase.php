<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PrepareCentralDatabase extends Command
{
    protected $signature = 'tenancy:prepare-central {database? : Central database name}';

    protected $description = 'Create the configured central database when it does not exist.';

    public function handle(): int
    {
        $database = $this->argument('database') ?: config('database.connections.central.database');

        if (!preg_match('/^[A-Za-z0-9_]+$/', $database)) {
            $this->error('The database name may contain only letters, numbers, and underscores.');

            return self::FAILURE;
        }

        $connection = DB::connection('mysql');
        $exists = $connection->selectOne(
            'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?',
            [$database]
        );

        if ($exists) {
            $this->info("Central database [{$database}] already exists.");

            return self::SUCCESS;
        }

        $charset = $connection->getConfig('charset');
        $collation = $connection->getConfig('collation');
        $connection->statement("CREATE DATABASE `{$database}` CHARACTER SET `{$charset}` COLLATE `{$collation}`");

        $this->info("Created central database [{$database}].");

        return self::SUCCESS;
    }
}
