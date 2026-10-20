<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. project_master
        if (!Schema::hasTable('project_master')) {
            Schema::create('project_master', function (Blueprint $table) {
                $table->increments('id');
                $table->string('project_name', 225);
                $table->string('project_location', 225)->default('');
                $table->integer('status')->default(1);
                $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->timestamps();
            });

            DB::table('project_master')->insert([
                ['project_name' => 'Other', 'project_location' => 'General', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['project_name' => 'Default Project', 'project_location' => 'Main', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 2. requirement_master
        if (!Schema::hasTable('requirement_master')) {
            Schema::create('requirement_master', function (Blueprint $table) {
                $table->increments('id');
                $table->string('requirement_name', 255)->nullable();
                $table->integer('status')->default(1);
                $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->timestamps();
            });

            $defaults = ['1 BHK', '2 BHK', '3 BHK', '4 BHK', 'Plot', 'Commercial', 'Other'];
            foreach ($defaults as $name) {
                DB::table('requirement_master')->insert([
                    'requirement_name' => $name,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 3. source_master
        if (!Schema::hasTable('source_master')) {
            Schema::create('source_master', function (Blueprint $table) {
                $table->increments('id');
                $table->string('source_name', 225);
                $table->integer('status')->default(1);
                $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->timestamps();
            });

            $defaults = ['Direct', 'Website', 'Walk in', 'Housing', '99acres', 'MagicBricks', 'Facebook', 'Referral', 'Other'];
            foreach ($defaults as $name) {
                DB::table('source_master')->insert([
                    'source_name' => $name,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 4. budget_master
        if (!Schema::hasTable('budget_master')) {
            Schema::create('budget_master', function (Blueprint $table) {
                $table->increments('Id');
                $table->string('budget_range', 225);
                $table->integer('status')->default(1);
                $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->timestamps();
            });

            $defaults = [
                '10 Lac - 20 Lac', '20 Lac - 40 Lac', '40 Lac - 60 Lac',
                '60 Lac - 80 Lac', '80 Lac - 1 Crore', '1 Crore - 1.5 Crore',
                '1.5 Crore+', 'Other',
            ];
            foreach ($defaults as $range) {
                DB::table('budget_master')->insert([
                    'budget_range' => $range,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 5. tbl_lead_status
        if (!Schema::hasTable('tbl_lead_status')) {
            Schema::create('tbl_lead_status', function (Blueprint $table) {
                $table->increments('id');
                $table->string('status', 250);
            });

            $statuses = [
                1 => 'interested',
                2 => 'Not Picked',
                3 => 'Not reachable',
                4 => 'Switch off',
                5 => 'Call back Later',
                6 => 'Not Interested',
                7 => 'Other requirements',
            ];
            foreach ($statuses as $id => $status) {
                DB::table('tbl_lead_status')->insert(['id' => $id, 'status' => $status]);
            }
        }

        // 6. tbl_meeting_type
        if (!Schema::hasTable('tbl_meeting_type')) {
            Schema::create('tbl_meeting_type', function (Blueprint $table) {
                $table->increments('id');
                $table->string('type', 250);
            });

            DB::table('tbl_meeting_type')->insert([
                ['id' => 1, 'type' => 'Office Meeting'],
                ['id' => 2, 'type' => 'Site visit'],
            ]);
        }

        // 7. countries
        if (!Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->increments('id');
                $table->string('sortname', 3)->nullable();
                $table->string('name', 150)->nullable();
                $table->string('mobile_code', 10)->nullable();
                $table->enum('deleted', ['Y', 'N'])->default('N');
            });

            $countries = [
                ['id' => 101, 'sortname' => 'IN', 'name' => 'India', 'mobile_code' => '+91', 'deleted' => 'N'],
                ['id' => 231, 'sortname' => 'US', 'name' => 'United States', 'mobile_code' => '+1', 'deleted' => 'N'],
                ['id' => 230, 'sortname' => 'GB', 'name' => 'United Kingdom', 'mobile_code' => '+44', 'deleted' => 'N'],
                ['id' => 38, 'sortname' => 'CA', 'name' => 'Canada', 'mobile_code' => '+1', 'deleted' => 'N'],
                ['id' => 13, 'sortname' => 'AU', 'name' => 'Australia', 'mobile_code' => '+61', 'deleted' => 'N'],
                ['id' => 196, 'sortname' => 'SG', 'name' => 'Singapore', 'mobile_code' => '+65', 'deleted' => 'N'],
            ];
            foreach ($countries as $c) {
                DB::table('countries')->insert($c);
            }
        }

        // 8. states
        if (!Schema::hasTable('states')) {
            Schema::create('states', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('name', 30)->nullable();
                $table->integer('country_id')->default(1);
            });
        }

        // 9. sell_lead
        if (!Schema::hasTable('sell_lead')) {
            Schema::create('sell_lead', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 255)->default('');
                $table->string('Contact_number', 50)->default('');
                $table->string('email', 255)->default('');
                $table->string('property_type', 255)->default('');
                $table->string('project', 255)->default('');
                $table->string('category', 255)->default('');
                $table->string('sub_category', 255)->default('');
                $table->string('expected_price', 255)->default('');
                $table->string('primium', 255)->default('');
                $table->string('market_price', 255)->default('');
                $table->string('booking_price', 255)->default('');
                $table->string('maintenance_charge', 255)->default('');
                $table->string('other_charge', 255)->default('');
                $table->string('security_deposit', 255)->default('');
                $table->string('properry_status', 255)->default('');
                $table->string('disposition', 255)->default('');
                $table->string('source', 255)->default('');
                $table->string('property_tag', 255)->default('');
                $table->string('address', 255)->default('');
                $table->string('block', 255)->default('');
                $table->string('country', 255)->default('');
                $table->string('state', 255)->default('');
                $table->string('city', 255)->default('');
                $table->string('locality', 255)->default('');
                $table->string('super_area', 255)->default('');
                $table->string('plot_area', 255)->default('');
                $table->string('cover_area', 255)->default('');
                $table->string('carpet_area', 255)->default('');
                $table->string('bedrooms', 255)->default('');
                $table->string('bathroom', 255)->default('');
                $table->string('work_station', 255)->default('');
                $table->string('cabins', 255)->default('');
                $table->string('facing', 255)->default('');
                $table->string('furnish', 255)->default('');
                $table->string('floor', 255)->default('');
                $table->string('built_year', 255)->default('');
                $table->string('car_parking', 255)->default('');
                $table->string('remarks', 255)->default('');
                $table->string('description', 255)->default('');
                $table->string('other_feature', 255)->default('');
                $table->integer('status')->default(1);
                $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->timestamps();
            });
        }

        // 10. dump_leads
        if (!Schema::hasTable('dump_leads')) {
            Schema::create('dump_leads', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title')->nullable();
                $table->text('note')->nullable();
                $table->string('name', 225)->nullable();
                $table->string('contact_no', 225)->nullable();
                $table->string('email', 225)->nullable();
                $table->string('source', 225)->nullable();
                $table->string('country', 225)->nullable();
                $table->string('state', 225)->nullable();
                $table->string('city', 225)->nullable();
                $table->string('pin', 50)->nullable();
                $table->string('location', 225)->nullable();
                $table->string('project', 225)->nullable();
                $table->string('requirement', 225)->nullable();
                $table->string('Budget', 225)->nullable();
                $table->integer('status')->default(1);
                $table->unsignedInteger('user_assigned_id')->nullable();
                $table->unsignedInteger('client_id')->nullable();
                $table->unsignedInteger('user_created_id')->default(1);
                $table->dateTime('contact_date')->nullable();
                $table->string('lead_type', 250)->nullable();
                $table->dateTime('action_date')->nullable();
                $table->timestamps();
            });
        }

        // 11. call_logs
        if (!Schema::hasTable('call_logs')) {
            Schema::create('call_logs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('call_type', 255)->default('');
                $table->string('client_number', 255)->default('');
                $table->dateTime('call_start_datetime')->nullable();
                $table->dateTime('call_end_datetime')->nullable();
                $table->integer('user_id')->default(0);
                $table->integer('lead_id')->default(0);
                $table->timestamp('created_at')->nullable()->useCurrent();
            });
        }

        // 12. users_checkin_checkout
        if (!Schema::hasTable('users_checkin_checkout')) {
            Schema::create('users_checkin_checkout', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->timestamp('check_in_date')->useCurrent();
                $table->float('check_in_lat')->default(0);
                $table->float('check_in_lng')->default(0);
                $table->string('check_in_rem', 250)->default('');
                $table->string('check_in_address', 500)->default('');
                $table->float('check_out_lat')->default(0);
                $table->float('check_out_lng')->default(0);
                $table->string('check_out_address', 500)->default('');
                $table->string('check_out_rem', 250)->default('');
                $table->dateTime('check_out_date')->nullable();
            });
        }

        // 13. user_token
        if (!Schema::hasTable('user_token')) {
            Schema::create('user_token', function (Blueprint $table) {
                $table->increments('Id');
                $table->integer('user_id');
                $table->string('token', 225);
                $table->integer('status')->default(1);
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 14. Roles
        if (Schema::hasTable('roles')) {
            $roles = [
                ['name' => 'administrator', 'display_name' => 'Administrator', 'description' => 'System Administrator'],
                ['name' => 'team leader', 'display_name' => 'Manager', 'description' => 'System Manager'],
                ['name' => 'manager', 'display_name' => 'Manager', 'description' => 'System Manager'],
                ['name' => 'employee', 'display_name' => 'Employee', 'description' => 'Employee'],
            ];
            foreach ($roles as $r) {
                if (!DB::table('roles')->where('name', $r['name'])->exists()) {
                    DB::table('roles')->insert(array_merge($r, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));
                }
            }
        }

        // 15. Permissions
        if (Schema::hasTable('permissions')) {
            $permissions = [
                ['id' => 1, 'name' => 'user-create', 'display_name' => 'Create user', 'description' => 'Permission to create user'],
                ['id' => 2, 'name' => 'user-update', 'display_name' => 'Update user', 'description' => 'Permission to update user'],
                ['id' => 3, 'name' => 'user-delete', 'display_name' => 'Delete user', 'description' => 'Permission to update delete'],
                ['id' => 4, 'name' => 'client-create', 'display_name' => 'Create client', 'description' => 'Permission to create client'],
                ['id' => 5, 'name' => 'client-update', 'display_name' => 'Update client', 'description' => 'Permission to update client'],
                ['id' => 6, 'name' => 'client-delete', 'display_name' => 'Delete client', 'description' => 'Permission to delete client'],
                ['id' => 7, 'name' => 'task-create', 'display_name' => 'Create task', 'description' => 'Permission to create task'],
                ['id' => 8, 'name' => 'task-update', 'display_name' => 'Update task', 'description' => 'Permission to update task'],
                ['id' => 9, 'name' => 'lead-create', 'display_name' => 'Create lead', 'description' => 'Permission to create lead'],
                ['id' => 10, 'name' => 'lead-update', 'display_name' => 'Update lead', 'description' => 'Permission to update lead'],
            ];
            foreach ($permissions as $p) {
                if (!DB::table('permissions')->where('name', $p['name'])->exists()) {
                    DB::table('permissions')->insert(array_merge($p, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));
                }
            }
        }

        // 16. Industries
        if (Schema::hasTable('industries') && DB::table('industries')->count() === 0) {
            $industries = [
                ['id' => 1, 'name' => 'Accommodations'],
                ['id' => 2, 'name' => 'Accounting'],
                ['id' => 3, 'name' => 'Auto'],
                ['id' => 4, 'name' => 'Beauty & Cosmetics'],
                ['id' => 5, 'name' => 'Carpenter'],
                ['id' => 6, 'name' => 'Communications'],
                ['id' => 7, 'name' => 'Computer & IT'],
                ['id' => 8, 'name' => 'Construction'],
                ['id' => 9, 'name' => 'Consulting'],
                ['id' => 10, 'name' => 'Education'],
                ['id' => 11, 'name' => 'Electronics'],
                ['id' => 12, 'name' => 'Entertainment'],
                ['id' => 13, 'name' => 'Food & Beverages'],
                ['id' => 14, 'name' => 'Legal Services'],
                ['id' => 15, 'name' => 'Marketing'],
                ['id' => 16, 'name' => 'Real Estate'],
                ['id' => 17, 'name' => 'Retail'],
                ['id' => 18, 'name' => 'Sports'],
                ['id' => 19, 'name' => 'Technology'],
                ['id' => 20, 'name' => 'Tourism'],
                ['id' => 21, 'name' => 'Transportation'],
                ['id' => 22, 'name' => 'Travel'],
                ['id' => 23, 'name' => 'Utilities'],
                ['id' => 24, 'name' => 'Web Services'],
                ['id' => 25, 'name' => 'Other'],
            ];
            DB::table('industries')->insert($industries);
        }

        // 17. Default Client
        if (Schema::hasTable('clients') && DB::table('clients')->count() === 0) {
            $userId = DB::table('users')->value('id') ?? 1;
            $companyId = DB::table('companies')->value('id');
            DB::table('clients')->insert([
                'id' => 1,
                'name' => 'Default Client',
                'email' => 'default_client@elyleads.local',
                'company_name' => 'Default Company',
                'industry' => 'Real Estate',
                'user_id' => $userId,
                'industry_id' => 16,
                'company_id' => $companyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_token');
        Schema::dropIfExists('users_checkin_checkout');
        Schema::dropIfExists('call_logs');
        Schema::dropIfExists('dump_leads');
        Schema::dropIfExists('sell_lead');
        Schema::dropIfExists('states');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('tbl_meeting_type');
        Schema::dropIfExists('tbl_lead_status');
        Schema::dropIfExists('budget_master');
        Schema::dropIfExists('source_master');
        Schema::dropIfExists('requirement_master');
        Schema::dropIfExists('project_master');
    }
};
