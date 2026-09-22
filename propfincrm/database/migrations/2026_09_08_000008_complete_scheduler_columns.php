<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('schedulers')) {
            return;
        }

        if (!Schema::hasColumn('schedulers', 'projects')) {
            Schema::table('schedulers', function (Blueprint $table) {
                $table->json('projects')->nullable();
            });
        }

        if (!Schema::hasColumn('schedulers', 'sec_index')) {
            Schema::table('schedulers', function (Blueprint $table) {
                $table->unsignedInteger('sec_index')->default(0);
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('schedulers')) {
            return;
        }

        Schema::table('schedulers', function (Blueprint $table) {
            if (Schema::hasColumn('schedulers', 'sec_index')) $table->dropColumn('sec_index');
            if (Schema::hasColumn('schedulers', 'projects')) $table->dropColumn('projects');
        });
    }
};
