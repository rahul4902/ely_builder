<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('settings', 'timezone')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('timezone')->nullable()->after('company');
            });
        }

        // The key/value table was introduced for tenant settings but remained
        // empty. Existing per-company settings stay in the original scoped
        // settings table, so no data is discarded.
        if (Schema::hasTable('company_settings')) {
            Schema::drop('company_settings');
        }

        foreach (DB::table('companies')->select('id', 'name')->cursor() as $company) {
            DB::table('settings')->where('company_id', $company->id)->update([
                'company' => $company->name,
                'timezone' => DB::raw("COALESCE(timezone, 'Asia/Kolkata')"),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('settings', 'timezone')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('timezone');
            });
        }
    }
};
