<?php

use Illuminate\Database\Seeder;
use App\Models\PermissionRole;

class RolePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($permissionId = 1; $permissionId <= 10; $permissionId++) {
            if (!PermissionRole::where('role_id', 1)->where('permission_id', $permissionId)->exists()) {
                $rolePerm = new PermissionRole;
                $rolePerm->role_id = 1;
                $rolePerm->permission_id = $permissionId;
                $rolePerm->timestamps = false;
                $rolePerm->save();
            }
        }
    }
}
