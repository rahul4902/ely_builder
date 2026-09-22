<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'pushtoken')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('pushtoken')->nullable()->after('personal_number');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'pushtoken')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('pushtoken');
            });
        }
    }
};
