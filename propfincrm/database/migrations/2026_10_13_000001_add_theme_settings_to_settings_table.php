<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $connections = array_unique(array_filter([
            config('database.default'),
            config('tenancy.database.central_connection', 'central'),
        ]));

        foreach ($connections as $connection) {
            if (Schema::connection($connection)->hasTable('settings')) {
                Schema::connection($connection)->table('settings', function (Blueprint $table) use ($connection) {
                    if (!Schema::connection($connection)->hasColumn('settings', 'theme_mode')) {
                        $table->string('theme_mode', 20)->default('light')->after('timezone');
                    }
                    if (!Schema::connection($connection)->hasColumn('settings', 'theme_accent')) {
                        $table->string('theme_accent', 20)->default('#0B57D0')->after('theme_mode');
                    }
                });
            }
        }
    }

    public function down(): void
    {
        $connections = array_unique(array_filter([
            config('database.default'),
            config('tenancy.database.central_connection', 'central'),
        ]));

        foreach ($connections as $connection) {
            if (Schema::connection($connection)->hasTable('settings')) {
                Schema::connection($connection)->table('settings', function (Blueprint $table) use ($connection) {
                    $colsToDrop = [];
                    if (Schema::connection($connection)->hasColumn('settings', 'theme_accent')) {
                        $colsToDrop[] = 'theme_accent';
                    }
                    if (Schema::connection($connection)->hasColumn('settings', 'theme_mode')) {
                        $colsToDrop[] = 'theme_mode';
                    }
                    if (!empty($colsToDrop)) {
                        $table->dropColumn($colsToDrop);
                    }
                });
            }
        }
    }
};
