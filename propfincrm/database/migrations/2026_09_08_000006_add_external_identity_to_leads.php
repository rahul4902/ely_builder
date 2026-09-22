<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('company_id');
            $table->string('provider_lead_id')->nullable()->after('provider');
            $table->json('external_payload')->nullable()->after('provider_lead_id');
            $table->unique(['company_id', 'provider', 'provider_lead_id'], 'leads_company_provider_external_unique');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropUnique('leads_company_provider_external_unique');
            $table->dropColumn(['provider', 'provider_lead_id', 'external_payload']);
        });
    }
};
