<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ThemeSettingsTest extends TestCase
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
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->text('data')->nullable();
            $table->timestamps();
        });

        Schema::create('company_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role');
            $table->unsignedBigInteger('teamlead_user_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['company_id', 'user_id']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->integer('task_complete_allowed')->default(2);
            $table->integer('task_assign_allowed')->default(2);
            $table->integer('lead_complete_allowed')->default(2);
            $table->integer('lead_assign_allowed')->default(2);
            $table->integer('time_change_allowed')->default(2);
            $table->integer('comment_allowed')->default(2);
            $table->string('country')->nullable();
            $table->string('company')->nullable();
            $table->string('timezone')->default('Asia/Kolkata');
            $table->string('theme_mode', 20)->default('light');
            $table->string('theme_accent', 20)->default('#0B57D0');
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->primary(['user_id', 'role_id']);
        });
    }

    public function test_company_admin_can_update_theme_settings(): void
    {
        $this->withoutExceptionHandling();
        $company = Company::create(['name' => 'Acme Corp', 'slug' => 'acme-corp']);
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret'),
        ]);
        $user->companies()->attach($company->id, ['role' => 'company_admin', 'is_active' => true]);

        $response = $this->actingAs($user)->patch(route('settings.company.general'), [
            'settings' => [
                'theme_mode' => 'dark',
                'theme_accent' => '#F97316',
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $setting = Setting::query()->first();
        $this->assertNotNull($setting);
        $this->assertEquals('dark', $setting->theme_mode);
        $this->assertEquals('#F97316', $setting->theme_accent);
    }

    public function test_admin_can_update_overall_settings_with_theme(): void
    {
        $company = Company::create(['name' => 'Acme Corp', 'slug' => 'acme-corp']);
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => Hash::make('secret'),
        ]);
        $user->companies()->attach($company->id, ['role' => 'company_admin', 'is_active' => true]);

        $adminRole = DB::table('roles')->insertGetId(['name' => 'administrator', 'display_name' => 'Admin']);
        DB::table('role_user')->insert(['user_id' => $user->id, 'role_id' => $adminRole]);

        $response = $this->actingAs($user)->patch(url('settings/overall'), [
            'task_complete_allowed' => 1,
            'task_assign_allowed' => 1,
            'lead_complete_allowed' => 1,
            'lead_assign_allowed' => 1,
            'theme_mode' => 'light',
            'theme_accent' => '#00796B',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $setting = Setting::query()->first();
        $this->assertNotNull($setting);
        $this->assertEquals('light', $setting->theme_mode);
        $this->assertEquals('#00796B', $setting->theme_accent);
    }
}
