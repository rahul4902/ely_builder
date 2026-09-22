<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlatformSubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'platform.super_admin']);
    }

    public function index()
    {
        $plans = Plan::withCount('companies')->orderBy('price')->get();
        $companies = Company::with(['plan', 'subscriptions'])->orderBy('name')->get();
        $recentSubscriptions = CompanySubscription::with(['company', 'plan'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('platform.subscriptions', [
            'plans' => $plans,
            'companies' => $companies,
            'recentSubscriptions' => $recentSubscriptions,
        ]);
    }

    public function storePlan(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'string', 'in:monthly,yearly,lifetime'],
            'max_users' => ['nullable', 'integer', 'min:1'],
            'features' => ['nullable', 'string'],
        ]);

        $slug = Str::slug($data['name']);
        $uniqueSlug = $slug;
        $counter = 2;
        while (Plan::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $counter++;
        }

        Plan::create([
            'name' => $data['name'],
            'slug' => $uniqueSlug,
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'billing_cycle' => $data['billing_cycle'],
            'max_users' => $data['max_users'] ?? null,
            'features' => $data['features'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('flash_message', 'Subscription plan created successfully.');
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'string', 'in:monthly,yearly,lifetime'],
            'max_users' => ['nullable', 'integer', 'min:1'],
            'features' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $plan->update($data);

        return back()->with('flash_message', 'Subscription plan updated.');
    }

    public function assignSubscription(Request $request)
    {
        $data = $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'starts_at' => ['required', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'billing_cycle' => ['required', 'string', 'in:monthly,yearly,lifetime'],
            'price_paid' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $company = Company::findOrFail($data['company_id']);
        $plan = Plan::findOrFail($data['plan_id']);

        $subscription = CompanySubscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'starts_at' => $data['starts_at'],
            'expires_at' => $data['expires_at'] ?? null,
            'status' => 'active',
            'billing_cycle' => $data['billing_cycle'],
            'price_paid' => $data['price_paid'] ?? $plan->price,
            'notes' => $data['notes'] ?? null,
        ]);

        $company->update([
            'plan_id' => $plan->id,
            'plan_expires_at' => $data['expires_at'] ?? null,
            'subscription_status' => 'active',
            'max_users' => $plan->max_users,
        ]);

        return back()->with('flash_message', "Subscription for {$company->name} assigned successfully.");
    }
}
