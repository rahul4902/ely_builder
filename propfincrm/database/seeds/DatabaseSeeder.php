<?php

use Illuminate\Database\Seeder;

require_once __DIR__ . '/PlatformSuperAdminSeeder.php';
require_once __DIR__ . '/PlansTableSeeder.php';

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('UsersTableSeeder');
        $this->call('IndustriesTableSeeder');
        $this->call('DepartmentsTableSeeder');
        $this->call('SettingsTableSeeder');
        $this->call('PermissionsTableSeeder');
        $this->call('RolesTablesSeeder');
        $this->call('RolePermissionTableSeeder');
        $this->call('UserRoleTableSeeder');
        $this->call('PlatformSuperAdminSeeder');
        $this->call('PlansTableSeeder');
    }
}
