<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!DB::table('roles')->where('name', 'super_administrator')->exists()) {
            DB::table('roles')->insert([
                'name' => 'super_administrator',
                'display_name' => 'Platform Super Admin',
                'description' => 'Manages companies and tenants across the platform.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Do not remove a role which may have been assigned after deployment.
    }
};
