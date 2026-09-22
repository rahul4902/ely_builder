<?php

namespace App\Http\Controllers;

use App\Models\CompanyPayment;
use App\Models\CompanySubscription;
use App\Models\Plan;
use App\Services\RazorpayService;
use Illuminate\Http\Request;

class TenantSubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $company = $request->user()->activeCompany();
        abort_unless($company, 403, 'No active company selected.');

        $company->load(['plan', 'subscriptions.plan', 'payments' => function ($query) {
            $query->orderBy('payment_date', 'desc')->limit(20);
        }]);

        $plans = Plan::where('is_active', true)->orderBy('price')->get();
        $activeUsersCount = $company->activeUsers()->count();

        return view('subscription.index', [
            'company' => $company,
            'plans' => $plans,
            'activeUsersCount' => $activeUsersCount,
            'razorpayKey' => config('services.razorpay.key') ?: 'rzp_test_YourKeyId',
        ]);
    }

    public function createRazorpayOrder(Request $request, RazorpayService $razorpay)
    {
        $company = $request->user()->activeCompany();
        abort_unless($company, 403, 'No active company selected.');

        $data = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
        ]);

        $plan = Plan::findOrFail($data['plan_id']);

        if ((float) $plan->price <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Free plans cannot be checked out via payment gateway.',
            ], 422);
        }

        $receipt = 'rcpt_' . $company->id . '_' . time();
        $orderData = $razorpay->createOrder((float) $plan->price, $receipt, [
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
        ]);

        return response()->json([
            'success' => true,
            'key' => $orderData['key'],
            'order_id' => $orderData['order_id'],
            'amount' => $orderData['amount'],
            'currency' => $orderData['currency'],
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'company_name' => $company->name,
            'user_name' => $request->user()->name,
            'user_email' => $request->user()->email,
            'user_contact' => $request->user()->personal_number ?: $request->user()->work_number ?: '',
        ]);
    }

    public function verifyRazorpayPayment(Request $request, RazorpayService $razorpay)
    {
        $company = $request->user()->activeCompany();
        abort_unless($company, 403, 'No active company selected.');

        $data = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $isValid = $razorpay->verifyPaymentSignature(
            $data['razorpay_order_id'],
            $data['razorpay_payment_id'],
            $data['razorpay_signature']
        );

        if (!$isValid) {
            return response()->json([
                'success' => false,
                'message' => 'Payment signature verification failed. Please contact support.',
            ], 422);
        }

        $plan = Plan::findOrFail($data['plan_id']);

        // Calculate new expiry date based on billing cycle
        $baseDate = ($company->plan_expires_at && $company->plan_expires_at->isFuture())
            ? $company->plan_expires_at
            : now();

        $newExpiry = match ($plan->billing_cycle) {
            'yearly' => $baseDate->copy()->addYear(),
            'lifetime' => null,
            default => $baseDate->copy()->addMonth(),
        };

        // Create subscription history record
        $subscription = CompanySubscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'expires_at' => $newExpiry,
            'status' => 'active',
            'billing_cycle' => $plan->billing_cycle,
            'price_paid' => $plan->price,
            'notes' => 'Renewed via Razorpay. Order ID: ' . $data['razorpay_order_id'],
        ]);

        // Record payment
        CompanyPayment::create([
            'company_id' => $company->id,
            'subscription_id' => $subscription->id,
            'amount' => $plan->price,
            'currency' => 'INR',
            'payment_method' => 'razorpay',
            'transaction_reference' => $data['razorpay_payment_id'],
            'payment_date' => date('Y-m-d'),
            'status' => 'completed',
            'notes' => 'Razorpay Payment ID: ' . $data['razorpay_payment_id'] . ' | Order: ' . $data['razorpay_order_id'],
            'recorded_by_user_id' => $request->user()->id,
        ]);

        // Update company tenant
        $company->update([
            'plan_id' => $plan->id,
            'plan_expires_at' => $newExpiry,
            'subscription_status' => 'active',
            'is_active' => true,
            'max_users' => $plan->max_users,
        ]);

        $request->session()->flash('flash_message', "Payment of ₹" . number_format($plan->price, 2) . " successful! Your workspace has been updated to the {$plan->name} plan.");

        return response()->json([
            'success' => true,
            'message' => 'Subscription activated successfully!',
            'redirect_url' => route('tenant.subscription'),
        ]);
    }
}
