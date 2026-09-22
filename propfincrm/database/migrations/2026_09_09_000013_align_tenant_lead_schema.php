<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * The legacy CRM accumulated lead fields outside the original migration
     * history. Fresh tenant databases start from migrations, so retain schema
     * parity with the existing production tenant before lead workflows run.
     */
    public function up(): void
    {
        if (!Schema::hasTable('leads')) {
            return;
        }

        $missing = fn (string $column): bool => !Schema::hasColumn('leads', $column);

        if ($missing('name') || $missing('contact_no') || $missing('email') || $missing('source')
            || $missing('country') || $missing('state') || $missing('city') || $missing('pin')
            || $missing('location') || $missing('project') || $missing('requirement') || $missing('Budget')
            || $missing('user_assign_date') || $missing('lead_type') || $missing('action_date')
            || $missing('lead_reverse') || $missing('lead_rev_date') || $missing('reverse_remark')) {
            Schema::table('leads', function (Blueprint $table) use ($missing) {
                if ($missing('name')) $table->string('name', 225)->nullable();
                if ($missing('contact_no')) $table->string('contact_no', 225)->nullable();
                if ($missing('email')) $table->string('email', 225)->nullable();
                if ($missing('source')) $table->string('source', 225)->nullable();
                if ($missing('country')) $table->string('country', 225)->nullable();
                if ($missing('state')) $table->string('state', 225)->nullable();
                if ($missing('city')) $table->string('city', 225)->nullable();
                if ($missing('pin')) $table->string('pin', 50)->nullable();
                if ($missing('location')) $table->string('location', 225)->nullable();
                if ($missing('project')) $table->string('project', 225)->nullable();
                if ($missing('requirement')) $table->string('requirement', 225)->nullable();
                if ($missing('Budget')) $table->string('Budget', 225)->nullable();
                if ($missing('user_assign_date')) $table->dateTime('user_assign_date')->nullable();
                if ($missing('lead_type')) $table->string('lead_type', 250)->nullable();
                if ($missing('action_date')) $table->dateTime('action_date')->nullable()->index();
                if ($missing('lead_reverse')) $table->integer('lead_reverse')->default(0);
                if ($missing('lead_rev_date')) $table->dateTime('lead_rev_date')->nullable();
                if ($missing('reverse_remark')) $table->string('reverse_remark')->nullable();
            });
        }
    }

    public function down(): void
    {
        // These fields preserve compatibility with pre-migration tenant data.
        // Removing them would make a rollback destructive.
    }
};
