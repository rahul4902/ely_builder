<?php

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PlatformSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Ensure Roles exist
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_administrator'],
            [
                'display_name' => 'Platform Super Admin',
                'description' => 'Manages companies and tenants across the platform.',
            ]
        );

        $adminRole = Role::firstOrCreate(
            ['name' => 'administrator'],
            [
                'display_name' => 'Administrator',
                'description' => 'System Administrator',
            ]
        );

        // 2. Provision Dedicated Platform Super Admin (Tenant Manager)
        // This user manages platform tenants and has ZERO company attachments.
        $superAdmin = User::firstOrNew(['email' => 'superadmin@admin.com']);
        $superAdmin->name = 'Platform Super Admin';
        $superAdmin->password = Hash::make('admin123');
        $superAdmin->address = 'Platform HQ';
        $superAdmin->work_number = '0000000000';
        $superAdmin->personal_number = '0000000000';
        $superAdmin->save();

        // Assign ONLY super_administrator role
        DB::table('role_user')->where('user_id', $superAdmin->id)->delete();
        $superAdmin->attachRole($superAdminRole);

        // Ensure Superadmin is NOT attached to any tenant companies
        $superAdmin->companies()->detach();

        // 3. Ensure Default Company Admin (admin@admin.com) manages ONLY the default company
        $defaultAdmin = User::where('email', 'admin@admin.com')->first();
        if (!$defaultAdmin) {
            $defaultAdmin = User::find(1);
        }

        if ($defaultAdmin) {
            $defaultAdmin->name = 'Default Company Admin';
            $defaultAdmin->email = 'admin@admin.com';
            $defaultAdmin->password = Hash::make('admin123');
            $defaultAdmin->save();

            // Detach super_administrator, keep administrator only
            DB::table('role_user')
                ->where('user_id', $defaultAdmin->id)
                ->where('role_id', $superAdminRole->id)
                ->delete();

            if (!$defaultAdmin->hasRole('administrator')) {
                $defaultAdmin->attachRole($adminRole);
            }

            // Attach default admin strictly to default company (Company 1: RiskyRush Company)
            $defaultCompany = Company::first();
            if ($defaultCompany) {
                $defaultAdmin->companies()->syncWithoutDetaching([
                    $defaultCompany->id => ['role' => 'company_admin', 'is_active' => true],
                ]);
            }
        }
    }
}
