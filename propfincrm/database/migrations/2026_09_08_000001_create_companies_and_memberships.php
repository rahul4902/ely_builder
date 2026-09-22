<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id')->unique();
                $table->string('name');
                $table->string('slug')->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('company_user')) {
            Schema::create('company_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                // The legacy users table uses increments(), not bigIncrements().
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                $table->unsignedInteger('teamlead_user_id')->nullable();
                $table->string('role', 32)->default('employee');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->unique(['company_id', 'user_id']);
                $table->index(['company_id', 'role']);
            });
        }

        $name = (string) (DB::table('settings')->value('company') ?: config('app.name', 'Default Company'));
        $tenantId = (string) Str::uuid();
        DB::table('tenants')->insert([
            'id' => $tenantId,
            'data' => json_encode(['company_name' => $name]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $companyId = DB::table('companies')->insertGetId([
            'name' => $name,
            'slug' => Str::slug($name) ?: 'default-company',
            'tenant_id' => $tenantId,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Preserve the legacy role hierarchy for the first (existing) company.
        // A missing legacy role must never promote a user to Team Leader.
        $legacyRolesByUser = [];
        foreach (DB::table('role_user')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('role_user.user_id', 'roles.name')
            ->cursor() as $legacyRole) {
            $candidate = match ($legacyRole->name) {
                'super_administrator', 'administrator', 'admin' => 'company_admin',
                'team_leader', 'teamleader' => 'team_leader',
                default => 'employee',
            };
            $priority = ['employee' => 1, 'team_leader' => 2, 'company_admin' => 3];
            $current = $legacyRolesByUser[$legacyRole->user_id] ?? 'employee';
            if ($priority[$candidate] >= $priority[$current]) {
                $legacyRolesByUser[$legacyRole->user_id] = $candidate;
            }
        }

        foreach (DB::table('users')->select('id', 'teamlead')->cursor() as $user) {
            DB::table('company_user')->insert([
                'company_id' => $companyId,
                'user_id' => $user->id,
                'role' => $legacyRolesByUser[$user->id] ?? 'employee',
                'teamlead_user_id' => $user->teamlead,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('company_user');
        Schema::dropIfExists('companies');
    }
};
