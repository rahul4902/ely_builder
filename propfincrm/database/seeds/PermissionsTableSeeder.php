<?php

use Illuminate\Database\Seeder;
use App\Models\Permissions;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            ['name' => 'user-create', 'display_name' => 'Create user', 'description' => 'Permission to create user'],
            ['name' => 'user-update', 'display_name' => 'Update user', 'description' => 'Permission to update user'],
            ['name' => 'user-delete', 'display_name' => 'Delete user', 'description' => 'Permission to update delete'],
            ['name' => 'client-create', 'display_name' => 'Create client', 'description' => 'Permission to create client'],
            ['name' => 'client-update', 'display_name' => 'Update client', 'description' => 'Permission to update client'],
            ['name' => 'client-delete', 'display_name' => 'Delete client', 'description' => 'Permission to delete client'],
            ['name' => 'task-create', 'display_name' => 'Create task', 'description' => 'Permission to create task'],
            ['name' => 'task-update', 'display_name' => 'Update task', 'description' => 'Permission to update task'],
            ['name' => 'lead-create', 'display_name' => 'Create lead', 'description' => 'Permission to create lead'],
            ['name' => 'lead-update', 'display_name' => 'Update lead', 'description' => 'Permission to update lead'],
        ];

        foreach ($permissions as $perm) {
            Permissions::firstOrCreate(
                ['name' => $perm['name']],
                ['display_name' => $perm['display_name'], 'description' => $perm['description']]
            );
        }
    }
}
