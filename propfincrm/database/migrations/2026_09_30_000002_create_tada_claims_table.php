<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tada_claims')) {
            Schema::create('tada_claims', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('name', 255);
                $table->string('dept', 100);
                $table->date('date');
                $table->string('purpose', 255);
                $table->decimal('allowance', 10, 2)->default(0.00);
                $table->string('status', 50)->default('Approved');
                $table->timestamps();
            });

            $seedClaims = [
                ['id' => 1, 'name' => 'John Doe', 'dept' => 'HR', 'date' => '2024-10-01', 'purpose' => 'Meeting', 'allowance' => 50],
                ['id' => 2, 'name' => 'Jane Smith', 'dept' => 'IT', 'date' => '2024-10-02', 'purpose' => 'Conference', 'allowance' => 75],
                ['id' => 3, 'name' => 'Mike Johnson', 'dept' => 'Sales', 'date' => '2024-10-03', 'purpose' => 'Client Visit', 'allowance' => 80],
                ['id' => 4, 'name' => 'Emily Davis', 'dept' => 'Marketing', 'date' => '2024-10-04', 'purpose' => 'Workshop', 'allowance' => 65],
                ['id' => 5, 'name' => 'Robert Brown', 'dept' => 'Finance', 'date' => '2024-10-05', 'purpose' => 'Training', 'allowance' => 55],
                ['id' => 6, 'name' => 'Linda Wilson', 'dept' => 'Operations', 'date' => '2024-10-06', 'purpose' => 'Audit', 'allowance' => 90],
                ['id' => 7, 'name' => 'James Taylor', 'dept' => 'Admin', 'date' => '2024-10-07', 'purpose' => 'Inspection', 'allowance' => 70],
                ['id' => 8, 'name' => 'Mary White', 'dept' => 'Legal', 'date' => '2024-10-08', 'purpose' => 'Legal Review', 'allowance' => 85],
                ['id' => 9, 'name' => 'Chris Miller', 'dept' => 'R&D', 'date' => '2024-10-09', 'purpose' => 'Research', 'allowance' => 95],
                ['id' => 10, 'name' => 'Patricia Garcia', 'dept' => 'Customer Service', 'date' => '2024-10-10', 'purpose' => 'Support', 'allowance' => 60],
                ['id' => 11, 'name' => 'David Lee', 'dept' => 'HR', 'date' => '2024-10-11', 'purpose' => 'Meeting', 'allowance' => 50],
                ['id' => 12, 'name' => 'Susan Clark', 'dept' => 'IT', 'date' => '2024-10-12', 'purpose' => 'Conference', 'allowance' => 75],
                ['id' => 13, 'name' => 'Brian Lewis', 'dept' => 'Sales', 'date' => '2024-10-13', 'purpose' => 'Client Visit', 'allowance' => 80],
                ['id' => 14, 'name' => 'Jessica Walker', 'dept' => 'Marketing', 'date' => '2024-10-14', 'purpose' => 'Workshop', 'allowance' => 65],
                ['id' => 15, 'name' => 'Thomas Hall', 'dept' => 'Finance', 'date' => '2024-10-15', 'purpose' => 'Training', 'allowance' => 55],
                ['id' => 16, 'name' => 'Angela Young', 'dept' => 'Operations', 'date' => '2024-10-16', 'purpose' => 'Audit', 'allowance' => 90],
                ['id' => 17, 'name' => 'Mark King', 'dept' => 'Admin', 'date' => '2024-10-17', 'purpose' => 'Inspection', 'allowance' => 70],
                ['id' => 18, 'name' => 'Melissa Hernandez', 'dept' => 'Legal', 'date' => '2024-10-18', 'purpose' => 'Legal Review', 'allowance' => 85],
                ['id' => 19, 'name' => 'Paul Perez', 'dept' => 'R&D', 'date' => '2024-10-19', 'purpose' => 'Research', 'allowance' => 95],
                ['id' => 20, 'name' => 'Anna Scott', 'dept' => 'Customer Service', 'date' => '2024-10-20', 'purpose' => 'Support', 'allowance' => 60],
                ['id' => 21, 'name' => 'Kevin Adams', 'dept' => 'HR', 'date' => '2024-10-21', 'purpose' => 'Training', 'allowance' => 50],
                ['id' => 22, 'name' => 'Donna Baker', 'dept' => 'IT', 'date' => '2024-10-22', 'purpose' => 'Conference', 'allowance' => 75],
                ['id' => 23, 'name' => 'Carlos Gonzalez', 'dept' => 'Sales', 'date' => '2024-10-23', 'purpose' => 'Client Visit', 'allowance' => 80],
                ['id' => 24, 'name' => 'Brenda Mitchell', 'dept' => 'Marketing', 'date' => '2024-10-24', 'purpose' => 'Workshop', 'allowance' => 65],
                ['id' => 25, 'name' => 'Eric Robinson', 'dept' => 'Finance', 'date' => '2024-10-25', 'purpose' => 'Training', 'allowance' => 55],
                ['id' => 26, 'name' => 'Laura Reed', 'dept' => 'Operations', 'date' => '2024-10-26', 'purpose' => 'Audit', 'allowance' => 90],
                ['id' => 27, 'name' => 'Scott Turner', 'dept' => 'Admin', 'date' => '2024-10-27', 'purpose' => 'Inspection', 'allowance' => 70],
                ['id' => 28, 'name' => 'Betty Phillips', 'dept' => 'Legal', 'date' => '2024-10-28', 'purpose' => 'Legal Review', 'allowance' => 85],
                ['id' => 29, 'name' => 'Harry Evans', 'dept' => 'R&D', 'date' => '2024-10-29', 'purpose' => 'Research', 'allowance' => 95],
                ['id' => 30, 'name' => 'Rachel Collins', 'dept' => 'Customer Service', 'date' => '2024-10-30', 'purpose' => 'Support', 'allowance' => 60],
                ['id' => 31, 'name' => 'Tyler Fisher', 'dept' => 'HR', 'date' => '2024-10-31', 'purpose' => 'Meeting', 'allowance' => 50],
                ['id' => 32, 'name' => 'Sophie Foster', 'dept' => 'IT', 'date' => '2024-11-01', 'purpose' => 'Conference', 'allowance' => 75],
                ['id' => 33, 'name' => 'Jack Moore', 'dept' => 'Sales', 'date' => '2024-11-02', 'purpose' => 'Client Visit', 'allowance' => 80],
                ['id' => 34, 'name' => 'Megan Howard', 'dept' => 'Marketing', 'date' => '2024-11-03', 'purpose' => 'Workshop', 'allowance' => 65],
                ['id' => 35, 'name' => 'Tim Harris', 'dept' => 'Finance', 'date' => '2024-11-04', 'purpose' => 'Training', 'allowance' => 55],
            ];

            $defaultCompanyId = DB::table('companies')->where('id', 14)->value('id')
                ?? DB::table('companies')->value('id')
                ?? 14;

            foreach ($seedClaims as $c) {
                DB::table('tada_claims')->insert(array_merge($c, [
                    'company_id' => $defaultCompanyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tada_claims');
    }
};
