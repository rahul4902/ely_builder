<?php

use Illuminate\Database\Seeder;
use App\Models\RoleUser;

class UserRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!RoleUser::where('role_id', 1)->where('user_id', 1)->exists()) {
            $newrole = new RoleUser;
            $newrole->role_id = 1;
            $newrole->user_id = 1;
            $newrole->timestamps = false;
            $newrole->save();
        }
    }
}
