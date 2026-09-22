<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private array $tables = ['leads', 'dump_leads1', 'clients', 'tasks', 'notes', 'lead_follow_up', 'integrations'];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'company_id')) {
                continue;
            }
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }

        $companyId = DB::table('companies')->orderBy('id')->value('id');
        foreach ($this->tables as $tableName) {
            if ($companyId && Schema::hasTable($tableName)) {
                DB::table($tableName)->whereNull('company_id')->update(['company_id' => $companyId]);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropIndex([$table->getTable() . '_company_id_index']);
                    $table->dropColumn('company_id');
                });
            }
        }
    }
};
