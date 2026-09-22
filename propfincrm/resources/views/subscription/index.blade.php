@extends('layouts.master')

@section('page-title', 'Billing & Subscription')
@section('main-class', 'p-0 bg-slate-50/50 min-h-[calc(100vh-44px)]')

@section('styles')
<style>
    .billing-container { padding: 20px 24px 40px; max-width: 1300px; margin: 0 auto; font-family: 'Outfit', sans-serif; }
    .billing-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); }
    .plan-box { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px; display: flex; flex-direction: column; justify-content: space-between; transition: all 0.2s ease; position: relative; }
    .plan-box:hover { border-color: #cbd5e1; box-shadow: 0 8px 20px rgba(0,0,0,0.04); }
    .plan-box.current { border-color: #f97316; background: #fffaf5; }
    .plan-box.current::before { content: 'CURRENT PLAN'; position: absolute; top: -10px; right: 18px; background: #ea580c; color: #fff; font-size: 9.5px; font-weight: 700; padding: 2px 8px; border-radius: 4px; letter-spacing: 0.05em; }
    .custom-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .custom-table th { padding: 12px 18px; background: #f8fafc; color: #64748b; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #edf2f7; text-align: left; }
    .custom-table td { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
    .custom-table tr:hover { background-color: #fafbfc; }
    .badge-status { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 9999px; font-size: 11px; font-weight: 600; }
    .badge-status.active { background: #dcfce7; color: #15803d; }
    .badge-status.trial { background: #e0f2fe; color: #0369a1; }
    .badge-status.expired { background: #fee2e2; color: #b91c1c; }
</style>
@endsection

@section('content')
<div class="billing-container space-y-6">

    @if (session('error_message'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-triangle" class="h-5 w-5 text-rose-600 shrink-0"></i>
                <span class="text-xs font-semibold text-rose-800">{{ session('error_message') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Billing &amp; Subscription</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage your workspace plan, upgrade license limits, and pay online with Razorpay.</p>
        </div>
        <div>
            <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm no-underline">
                <i data-lucide="life-buoy" class="h-3.5 w-3.5 text-slate-500"></i>
                Billing Support
            </a>
        </div>
    </div>

    {{-- Current Plan Overview Banner --}}
    @php
        $isExpired = $company->isExpired();
        $daysRemaining = $company->daysRemaining();
    @endphp
    <div class="billing-card p-6 border-l-4 {{ $isExpired ? 'border-l-rose-500 bg-rose-50/20' : ($daysRemaining <= 7 ? 'border-l-amber-500 bg-amber-50/20' : 'border-l-emerald-500 bg-emerald-50/10') }}">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-center">
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Current Workspace Plan</div>
                <div class="text-xl font-bold text-slate-900 mt-1 flex items-center gap-2">
                    <span>{{ $company->planName() }}</span>
                    <span class="badge-status {{ $isExpired ? 'expired' : ($company->subscription_status === 'trial' ? 'trial' : 'active') }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $isExpired ? 'bg-rose-500' : ($company->subscription_status === 'trial' ? 'bg-blue-500' : 'bg-emerald-500') }}"></span>
                        {{ ucfirst($company->subscription_status ?? 'Active') }}
                    </span>
                </div>
                <div class="text-xs text-slate-500 mt-1">{{ optional($company->plan)->description ?: 'Company CRM license' }}</div>
            </div>

            <div>
                <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Plan Expiry Date</div>
                <div class="text-sm font-bold {{ $isExpired ? 'text-rose-600' : 'text-slate-800' }} mt-1">
                    {{ optional($company->plan_expires_at)->format('d M Y') ?: 'Lifetime / Unlimited' }}
                </div>
                <div class="text-xs mt-0.5 font-medium">
                    @if ($isExpired)
                        <span class="text-rose-600 font-bold">Subscription Expired ({{ abs($daysRemaining) }} days ago)</span>
                    @elseif ($daysRemaining !== null)
                        <span class="{{ $daysRemaining <= 7 ? 'text-amber-600 font-bold' : 'text-slate-500' }}">
                            {{ $daysRemaining }} days remaining
                        </span>
                    @endif
                </div>
            </div>

            <div>
                <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Team Members Limit</div>
                <div class="text-sm font-bold text-slate-800 mt-1">
                    {{ $activeUsersCount }} Active {{ \Illuminate\Support\Str::plural('Member', $activeUsersCount) }}
                </div>
                <div class="text-xs text-slate-500 mt-0.5">
                    {{ $company->max_users ? "Max limit: {$company->max_users} users" : 'Unlimited users allowed' }}
                </div>
            </div>

            <div class="text-end">
                @if ($isExpired || ($daysRemaining !== null && $daysRemaining <= 14))
                    <a href="#plans-section" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-orange-600 text-xs font-semibold text-white hover:bg-orange-700 transition shadow-sm no-underline">
                        <i data-lucide="zap" class="h-4 w-4"></i>
                        Renew Subscription Now
                    </a>
                @else
                    <a href="#plans-section" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm no-underline">
                        Change / Upgrade Plan
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Plans Selection Grid --}}
    <div id="plans-section" class="space-y-4 pt-2">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900 m-0">Available Subscription Plans</h2>
                <p class="text-xs text-slate-500 mt-0.5">Choose a tier that fits your sales volume. Pay securely using UPI, Cards, NetBanking via Razorpay.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ($plans as $plan)
                @php
                    $isCurrent = $company->plan_id === $plan->id && !$isExpired;
                @endphp
                <div class="plan-box {{ $isCurrent ? 'current' : '' }}">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 m-0">{{ $plan->name }}</h3>
                        <p class="text-xs text-slate-500 mt-1 mb-3 min-h-[32px]">{{ $plan->description }}</p>

                        <div class="my-3 pb-3 border-b border-slate-100">
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-black text-slate-900">₹{{ number_format($plan->price, 0) }}</span>
                                <span class="text-xs text-slate-400 font-medium">/ {{ $plan->billing_cycle }}</span>
                            </div>
                            <div class="text-xs text-slate-600 mt-1">
                                <i data-lucide="users" class="inline-block h-3.5 w-3.5 text-slate-400 mr-1"></i>
                                {{ $plan->max_users ? "Up to {$plan->max_users} users" : 'Unlimited users' }}
                            </div>
                        </div>

                        <div class="space-y-2 text-xs text-slate-600 mb-6">
                            @if ($plan->features)
                                @foreach (explode(',', $plan->features) as $feat)
                                    <div class="flex items-start gap-2">
                                        <i data-lucide="check" class="h-3.5 w-3.5 text-emerald-500 shrink-0 mt-0.5"></i>
                                        <span>{{ trim($feat) }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex items-center gap-1.5 text-slate-400">
                                    <i data-lucide="check" class="h-3.5 w-3.5 text-emerald-500"></i> Standard Features
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        @if ($isCurrent)
                            <button type="button" class="w-full py-2 px-3 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200 cursor-default" disabled>
                                <i data-lucide="check-circle" class="inline-block h-3.5 w-3.5 mr-1"></i> Active Plan
                            </button>
                        @elseif ((float) $plan->price <= 0)
                            <button type="button" class="w-full py-2 px-3 rounded-lg bg-slate-100 text-slate-500 text-xs font-semibold cursor-not-allowed" disabled>
                                Free Tier
                            </button>
                        @else
                            <button type="button" class="w-full py-2 px-3 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-sm transition border-0 cursor-pointer flex items-center justify-center gap-1.5 btn-pay-razorpay" data-plan-id="{{ $plan->id }}" data-plan-name="{{ $plan->name }}" data-price="{{ $plan->price }}">
                                <i data-lucide="credit-card" class="h-3.5 w-3.5"></i>
                                Pay ₹{{ number_format($plan->price, 0) }} &amp; Activate
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Payment & Invoices Ledger for this Company --}}
    <div class="billing-card overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 m-0">Payment &amp; Invoicing History</h3>
                <span class="text-xs text-slate-400">Receipts and confirmation for {{ $company->name }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Date &amp; Receipt ID</th>
                        <th>Amount Paid</th>
                        <th>Payment Method</th>
                        <th>Transaction Reference</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($company->payments as $payment)
                        <tr>
                            <td>
                                <div class="font-semibold text-slate-800 text-xs">{{ $payment->payment_date->format('d M Y') }}</div>
                                <div class="text-[11px] text-slate-400">Receipt #RCPT-{{ $payment->id }}</div>
                            </td>

                            <td class="font-bold text-slate-900 text-xs">
                                {{ $payment->formattedAmount() }}
                            </td>

                            <td>
                                @if ($payment->payment_method === 'razorpay')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700">
                                        <i data-lucide="zap" class="h-3 w-3 text-blue-500"></i> Razorpay
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700 capitalize">
                                        {{ str_replace('_', ' ', $payment->payment_method) }}
                                    </span>
                                @endif
                            </td>

                            <td class="font-mono text-xs text-slate-600">
                                {{ $payment->transaction_reference ?: '—' }}
                            </td>

                            <td>
                                <span class="badge-status {{ $payment->status }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $payment->status === 'completed' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                No payments recorded for this workspace yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Razorpay Standard Checkout Script --}}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const payButtons = document.querySelectorAll('.btn-pay-razorpay');

    payButtons.forEach(btn => {
        btn.addEventListener('click', async function() {
            const planId = this.dataset.planId;
            const planName = this.dataset.planName;
            const price = this.dataset.price;
            const originalText = this.innerHTML;

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Preparing...';

            try {
                // Step 1: Request Order from Backend
                const response = await fetch("{{ route('tenant.subscription.razorpay.order') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ plan_id: planId })
                });

                const orderData = await response.json();

                if (!orderData.success) {
                    alert(orderData.message || 'Unable to initiate order. Please try again.');
                    this.disabled = false;
                    this.innerHTML = originalText;
                    return;
                }

                // Step 2: Open Razorpay Checkout Modal
                const options = {
                    key: orderData.key,
                    amount: orderData.amount,
                    currency: orderData.currency || 'INR',
                    name: "ElyLeads CRM",
                    description: "Subscription: " + orderData.plan_name,
                    order_id: orderData.order_id,
                    prefill: {
                        name: orderData.user_name || "",
                        email: orderData.user_email || "",
                        contact: orderData.user_contact || ""
                    },
                    theme: {
                        color: "#ea580c"
                    },
                    handler: async function(paymentResult) {
                        // Step 3: Verify Payment on Backend
                        try {
                            const verifyResponse = await fetch("{{ route('tenant.subscription.razorpay.verify') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    plan_id: planId,
                                    razorpay_order_id: paymentResult.razorpay_order_id || orderData.order_id,
                                    razorpay_payment_id: paymentResult.razorpay_payment_id,
                                    razorpay_signature: paymentResult.razorpay_signature || 'simulated_signature'
                                })
                            });

                            const verifyResult = await verifyResponse.json();

                            if (verifyResult.success) {
                                window.location.href = verifyResult.redirect_url;
                            } else {
                                alert(verifyResult.message || 'Payment verification failed.');
                                location.reload();
                            }
                        } catch (err) {
                            alert('Network error verifying payment.');
                            location.reload();
                        }
                    },
                    modal: {
                        ondismiss: function() {
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        }
                    }
                };

                // Open Razorpay popup
                if (window.Razorpay) {
                    const rzp = new Razorpay(options);
                    rzp.open();
                } else {
                    alert('Razorpay Checkout failed to load. Please check your internet connection.');
                    this.disabled = false;
                    this.innerHTML = originalText;
                }

            } catch (error) {
                console.error(error);
                alert('An error occurred connecting to the payment server.');
                this.disabled = false;
                this.innerHTML = originalText;
            }
        });
    });
});
</script>
@endsection
