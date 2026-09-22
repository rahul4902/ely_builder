<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('lead_follow_up')) {
            Schema::create('lead_follow_up', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('lead_id')->index();
                $table->integer('user_id');
                $table->dateTime('follow_up_date');
                $table->text('comment');
                $table->integer('status')->default(0);
                $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('meeting_date')->nullable();
                $table->string('senior_visit', 250)->nullable();
                $table->string('meeting_type', 250)->nullable();
                $table->string('follow_up_status', 250)->nullable();
                $table->dateTime('action_date')->nullable();
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }

        if (!Schema::hasTable('dump_leads1')) {
            Schema::create('dump_leads1', function (Blueprint $table) {
                $table->unsignedInteger('id');
                $table->string('title')->nullable();
                $table->text('note')->nullable();
                $table->string('name', 225)->nullable();
                $table->string('contact_no', 225)->nullable();
                $table->string('email', 225)->nullable();
                $table->string('source', 225)->nullable();
                $table->string('country', 225)->nullable();
                $table->string('state', 225)->nullable();
                $table->string('city', 225)->nullable();
                $table->string('pin', 50)->nullable();
                $table->string('location', 225)->nullable();
                $table->string('project', 225)->nullable();
                $table->string('requirement', 225)->nullable();
                $table->string('Budget', 225)->nullable();
                $table->integer('status')->default(1);
                $table->unsignedInteger('user_assigned_id')->nullable();
                $table->dateTime('user_assign_date')->nullable();
                $table->unsignedInteger('client_id')->nullable();
                $table->unsignedInteger('user_created_id')->default(1);
                $table->dateTime('contact_date')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable()->useCurrent();
                $table->string('lead_type', 250)->nullable();
                $table->dateTime('action_date')->nullable();
                $table->integer('lead_reverse')->default(0);
                $table->dateTime('lead_rev_date')->nullable();
                $table->string('reverse_remark')->nullable();
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }

        if (!Schema::hasTable('app_notifications')) {
            Schema::create('app_notifications', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('title', 500);
                $table->string('content', 500);
                $table->boolean('n_read')->default(false);
                $table->timestamp('created_at')->useCurrent();
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'teamlead')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('teamlead')->nullable()->index();
            });
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'user_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('user_token', 225)->default('0')->index();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
        Schema::dropIfExists('dump_leads1');
        Schema::dropIfExists('lead_follow_up');
    }
};
