<?php

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plans = [
            [
                'name' => 'Trial',
                'slug' => 'trial',
                'description' => '14-day free trial for testing CRM features',
                'price' => 0.00,
                'billing_cycle' => 'monthly',
                'max_users' => 5,
                'features' => 'Lead Management, Scheduler, Call Logs, Basic Reports',
                'is_active' => true,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Standard plan for growing sales teams',
                'price' => 2499.00,
                'billing_cycle' => 'monthly',
                'max_users' => 10,
                'features' => 'Full Lead Management, Meeting & Visits, Reverse Leads, Attendance, Bulk Upload',
                'is_active' => true,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Comprehensive package for high-volume brokerages',
                'price' => 4999.00,
                'billing_cycle' => 'monthly',
                'max_users' => 30,
                'features' => 'All Starter Features, Integrations, HR Module, Unlimited Scheduler, Priority Support',
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Dedicated solution for large real estate enterprises',
                'price' => 9999.00,
                'billing_cycle' => 'monthly',
                'max_users' => null,
                'features' => 'Unlimited Users, Dedicated Tenant DB, Custom Integrations, 24/7 Phone Support',
                'is_active' => true,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }
    }
}
