<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_follow_up', function (Blueprint $table) {
            $table->index(['lead_id', 'follow_up_date'], 'idx_follow_up_lead_date');
            $table->index(['lead_id', 'meeting_date'], 'idx_follow_up_lead_meeting');
            $table->index('action_date', 'idx_follow_up_action_date');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->index(['user_assigned_id', 'status'], 'idx_leads_assigned_status');
            $table->index('updated_at', 'idx_leads_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_follow_up', function (Blueprint $table) {
            $table->dropIndex('idx_follow_up_lead_date');
            $table->dropIndex('idx_follow_up_lead_meeting');
            $table->dropIndex('idx_follow_up_action_date');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('idx_leads_assigned_status');
            $table->dropIndex('idx_leads_updated_at');
        });
    }
};
