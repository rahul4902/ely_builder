<?php

use Illuminate\Database\Seeder;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Department::firstOrCreate(
            ['id' => 1],
            ['name' => 'Management']
        );

        if (!DB::table('department_user')->where('department_id', 1)->where('user_id', 1)->exists()) {
            DB::table('department_user')->insert([
                'department_id' => 1,
                'user_id' => 1
            ]);
        }
    }
}
