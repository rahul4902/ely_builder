<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        if (!Role::where('name', 'super_administrator')->exists()) {
            $superAdminRole = new Role;
            $superAdminRole->display_name = 'Platform Super Admin';
            $superAdminRole->name = 'super_administrator';
            $superAdminRole->description = 'Manages companies and tenants across the platform.';
            $superAdminRole->save();
        }

        if (!Role::where('name', 'administrator')->exists()) {
            $adminRole = new Role;
            $adminRole->display_name = 'Administrator';
            $adminRole->name = 'administrator';
            $adminRole->description = 'System Administrator';
            $adminRole->save();
        }

        if (!Role::where('name', 'manager')->exists()) {
            $editorRole = new Role;
            $editorRole->display_name = 'Manager';
            $editorRole->name = 'manager';
            $editorRole->description = 'System Manager';
            $editorRole->save();
        }

        if (!Role::where('name', 'employee')->exists()) {
            $employeeRole = new Role;
            $employeeRole->display_name = 'Employee';
            $employeeRole->name = 'employee';
            $employeeRole->description = 'Employee';
            $employeeRole->save();
        }
    }
}
