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
        Schema::table('dump_leads1', function (Blueprint $table) {
            $table->index('id', 'idx_dump_id');
            $table->index(['user_assigned_id', 'status'], 'idx_dump_user_status');
            $table->index('status', 'idx_dump_status');
            $table->index('updated_at', 'idx_dump_updated_at');
        });

        Schema::table('app_notifications', function (Blueprint $table) {
            $table->index(['user_id', 'n_read'], 'idx_app_notif_user_read');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('teamlead', 'idx_users_teamlead');
            $table->index('user_token', 'idx_users_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dump_leads1', function (Blueprint $table) {
            $table->dropIndex('idx_dump_id');
            $table->dropIndex('idx_dump_user_status');
            $table->dropIndex('idx_dump_status');
            $table->dropIndex('idx_dump_updated_at');
        });

        Schema::table('app_notifications', function (Blueprint $table) {
            $table->dropIndex('idx_app_notif_user_read');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_teamlead');
            $table->dropIndex('idx_users_token');
        });
    }
};
