<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->unique()->after('id');
        });

        $companyId = DB::table('companies')->orderBy('id')->value('id');
        if ($companyId) {
            DB::table('settings')->whereNull('company_id')->update(['company_id' => $companyId]);
        }
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
