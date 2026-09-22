<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Create plans table on central connection
        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2)->default(0.00);
                $table->string('billing_cycle', 32)->default('monthly'); // monthly, yearly, lifetime
                $table->unsignedInteger('max_users')->nullable(); // null means unlimited
                $table->text('features')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // Seed initial plans
            DB::table('plans')->insert([
                [
                    'name' => 'Trial',
                    'slug' => 'trial',
                    'description' => '14-day free trial for testing CRM features',
                    'price' => 0.00,
                    'billing_cycle' => 'monthly',
                    'max_users' => 5,
                    'features' => 'Lead Management, Scheduler, Call Logs, Basic Reports',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
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
                    'created_at' => now(),
                    'updated_at' => now(),
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
                    'created_at' => now(),
                    'updated_at' => now(),
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
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 2. Add plan and expiry columns to companies table
        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) {
                if (!Schema::hasColumn('companies', 'plan_id')) {
                    $table->unsignedBigInteger('plan_id')->nullable()->after('slug');
                }
                if (!Schema::hasColumn('companies', 'plan_expires_at')) {
                    $table->dateTime('plan_expires_at')->nullable()->after('plan_id');
                }
                if (!Schema::hasColumn('companies', 'subscription_status')) {
                    $table->string('subscription_status', 32)->default('active')->after('plan_expires_at');
                }
                if (!Schema::hasColumn('companies', 'max_users')) {
                    $table->unsignedInteger('max_users')->nullable()->after('subscription_status');
                }
            });

            // Assign default plan to existing companies
            $starterPlanId = DB::table('plans')->where('slug', 'starter')->value('id');
            if ($starterPlanId) {
                DB::table('companies')->whereNull('plan_id')->update([
                    'plan_id' => $starterPlanId,
                    'plan_expires_at' => now()->addMonths(6),
                    'subscription_status' => 'active',
                ]);
            }
        }

        // 3. Create company_subscriptions table
        if (!Schema::hasTable('company_subscriptions')) {
            Schema::create('company_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
                $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
                $table->dateTime('starts_at');
                $table->dateTime('expires_at')->nullable();
                $table->string('status', 32)->default('active'); // active, expired, cancelled, trial
                $table->string('billing_cycle', 32)->default('monthly');
                $table->decimal('price_paid', 10, 2)->default(0.00);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['company_id', 'status']);
            });
        }

        // 4. Create company_payments table
        if (!Schema::hasTable('company_payments')) {
            Schema::create('company_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
                $table->foreignId('subscription_id')->nullable()->constrained('company_subscriptions')->nullOnDelete();
                $table->decimal('amount', 10, 2);
                $table->string('currency', 10)->default('INR');
                $table->string('payment_method', 50)->default('bank_transfer'); // bank_transfer, upi, cash, card, cheque, stripe, razorpay, other
                $table->string('transaction_reference')->nullable();
                $table->date('payment_date');
                $table->string('status', 32)->default('completed'); // completed, pending, failed, refunded
                $table->text('notes')->nullable();
                $table->unsignedInteger('recorded_by_user_id')->nullable();
                $table->timestamps();

                $table->index(['company_id', 'payment_date']);
            });
        }

        // 5. Create support_tickets table
        if (!Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_number')->unique();
                $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
                $table->unsignedInteger('user_id'); // User who created ticket
                $table->string('subject');
                $table->string('category', 50)->default('general'); // technical, billing, feature_request, general
                $table->string('priority', 20)->default('medium'); // low, medium, high, urgent
                $table->string('status', 32)->default('open'); // open, in_progress, resolved, closed
                $table->dateTime('last_reply_at')->nullable();
                $table->timestamps();

                $table->index(['company_id', 'status']);
                $table->index(['status', 'priority']);
            });
        }

        // 6. Create ticket_replies table
        if (!Schema::hasTable('ticket_replies')) {
            Schema::create('ticket_replies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('support_ticket_id')->constrained('support_tickets')->cascadeOnDelete();
                $table->unsignedInteger('user_id');
                $table->text('message');
                $table->boolean('is_superadmin_reply')->default(false);
                $table->string('attachment_path')->nullable();
                $table->timestamps();

                $table->index('support_ticket_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('company_payments');
        Schema::dropIfExists('company_subscriptions');

        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) {
                if (Schema::hasColumn('companies', 'max_users')) {
                    $table->dropColumn('max_users');
                }
                if (Schema::hasColumn('companies', 'subscription_status')) {
                    $table->dropColumn('subscription_status');
                }
                if (Schema::hasColumn('companies', 'plan_expires_at')) {
                    $table->dropColumn('plan_expires_at');
                }
                if (Schema::hasColumn('companies', 'plan_id')) {
                    $table->dropColumn('plan_id');
                }
            });
        }

        Schema::dropIfExists('plans');
    }
};
