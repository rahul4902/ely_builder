<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Lead;
use App\Models\User;
use App\Jobs\SyncCompanyIntegrationJob;
use App\Support\CurrentCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class CompanyAuthorizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'tenancy.database.central_connection' => 'sqlite',
        ]);
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('users', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('email')->unique(); $table->string('password'); $table->rememberToken(); $table->timestamps();
        });
        Schema::create('companies', function (Blueprint $table) {
            $table->id(); $table->string('tenant_id')->nullable(); $table->string('name'); $table->string('slug')->unique(); $table->boolean('is_active')->default(true); $table->timestamps();
        });
        Schema::create('company_user', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('company_id'); $table->unsignedBigInteger('user_id'); $table->string('role'); $table->unsignedBigInteger('teamlead_user_id')->nullable(); $table->boolean('is_active')->default(true); $table->timestamps(); $table->unique(['company_id', 'user_id']);
        });
        Schema::create('leads', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('company_id'); $table->string('name'); $table->unsignedBigInteger('user_assigned_id'); $table->unsignedBigInteger('user_created_id'); $table->integer('status'); $table->timestamps();
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); $table->string('name')->unique(); $table->string('display_name')->nullable(); $table->string('description')->nullable(); $table->timestamps();
        });
        Schema::create('role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id'); $table->unsignedBigInteger('role_id'); $table->primary(['user_id', 'role_id']);
        });
    }

    public function test_authenticated_user_has_an_active_company_membership(): void
    {
        $company = Company::create(['name' => 'Alpha', 'slug' => 'alpha']);
        $user = User::create(['name' => 'Alice', 'email' => 'alice@example.test', 'password' => Hash::make('secret-password')]);
        $user->companies()->attach($company, ['role' => 'employee', 'is_active' => true]);

        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->belongsToCompany($company));
    }

    public function test_company_scope_and_policy_deny_cross_company_leads(): void
    {
        $alpha = Company::create(['name' => 'Alpha', 'slug' => 'alpha']);
        $beta = Company::create(['name' => 'Beta', 'slug' => 'beta']);
        $alice = User::create(['name' => 'Alice', 'email' => 'alice@example.test', 'password' => Hash::make('password')]);
        $bob = User::create(['name' => 'Bob', 'email' => 'bob@example.test', 'password' => Hash::make('password')]);
        $alice->companies()->attach($alpha, ['role' => 'employee', 'is_active' => true]);
        $bob->companies()->attach($beta, ['role' => 'company_admin', 'is_active' => true]);
        $lead = Lead::withoutGlobalScopes()->create(['company_id' => $beta->id, 'name' => 'Beta lead', 'user_assigned_id' => $bob->id, 'user_created_id' => $bob->id, 'status' => 1]);

        app(CurrentCompany::class)->set($alpha);

        $this->assertNull(Lead::find($lead->id));
        $this->assertFalse(Gate::forUser($alice)->allows('view', $lead));
        $this->assertFalse(Gate::forUser($alice)->allows('assign', $lead));
    }

    public function test_company_admin_can_manage_but_employee_cannot_assign(): void
    {
        $company = Company::create(['name' => 'Alpha', 'slug' => 'alpha']);
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => Hash::make('password')]);
        $employee = User::create(['name' => 'Employee', 'email' => 'employee@example.test', 'password' => Hash::make('password')]);
        $admin->companies()->attach($company, ['role' => 'company_admin', 'is_active' => true]);
        $employee->companies()->attach($company, ['role' => 'employee', 'is_active' => true]);
        app(CurrentCompany::class)->set($company);
        $lead = Lead::create(['name' => 'Alpha lead', 'user_assigned_id' => $employee->id, 'user_created_id' => $admin->id, 'status' => 1]);

        $this->assertTrue(Gate::forUser($admin)->allows('assign', $lead));
        $this->assertFalse(Gate::forUser($employee)->allows('assign', $lead));
    }

    public function test_integration_sync_is_dispatched_to_the_dedicated_queue(): void
    {
        Queue::fake();

        SyncCompanyIntegrationJob::dispatch(42);

        Queue::assertPushed(SyncCompanyIntegrationJob::class, function (SyncCompanyIntegrationJob $job) {
            return $job->integrationId === 42 && $job->queue === 'integrations';
        });
    }
}
