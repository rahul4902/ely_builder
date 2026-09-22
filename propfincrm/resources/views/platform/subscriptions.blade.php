@extends('layouts.master')

@section('page-title', 'Subscriptions & Plans')
@section('main-class', 'p-0 bg-slate-50/50 min-h-[calc(100vh-44px)]')

@section('styles')
<style>
    .platform-container { padding: 20px 24px 40px; max-width: 1400px; margin: 0 auto; font-family: 'Outfit', sans-serif; }
    .plan-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: space-between; }
    .plan-card.featured { border-color: #ea580c; position: relative; }
    .platform-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); overflow: hidden; }
    .platform-card-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #edf2f7; }
    .custom-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .custom-table th { padding: 12px 18px; background: #f8fafc; color: #64748b; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #edf2f7; text-align: left; }
    .custom-table td { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
    .custom-table tr:hover { background-color: #fafbfc; }
</style>
@endsection

@section('content')
<div class="platform-container space-y-6">

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Subscriptions &amp; Plans</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage subscription tiers, feature allocations, and assign tenant licenses.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm" data-bs-toggle="modal" data-bs-target="#assignSubscriptionModal">
                <i data-lucide="award" class="h-3.5 w-3.5 text-orange-500"></i>
                Assign Subscription
            </button>
            <button type="button" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-orange-600 text-xs font-semibold text-white hover:bg-orange-700 transition shadow-sm border-0 cursor-pointer" data-bs-toggle="modal" data-bs-target="#createPlanModal">
                <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                New Plan
            </button>
        </div>
    </div>

    {{-- Plans Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($plans as $plan)
            <div class="plan-card {{ $plan->slug === 'professional' ? 'featured' : '' }}">
                <div>
                    @if ($plan->slug === 'professional')
                        <span class="inline-block bg-orange-600 text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md mb-2">Most Popular</span>
                    @endif
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-slate-800 m-0">{{ $plan->name }}</h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $plan->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $plan->is_active ? 'Active' : 'Disabled' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 mb-3 min-h-[32px]">{{ $plan->description ?: 'No description provided.' }}</p>

                    <div class="my-4 pb-3 border-b border-slate-100">
                        <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-black text-slate-900">₹{{ number_format($plan->price, 0) }}</span>
                            <span class="text-xs text-slate-400 font-medium">/ {{ $plan->billing_cycle }}</span>
                        </div>
                        <div class="text-[11.5px] text-slate-600 mt-1">
                            <i data-lucide="users" class="inline-block h-3.5 w-3.5 text-slate-400 mr-1"></i>
                            {{ $plan->max_users ? "Up to {$plan->max_users} users" : 'Unlimited users' }}
                        </div>
                    </div>

                    <div class="space-y-1.5 text-xs text-slate-600 mb-5">
                        <div class="font-semibold text-slate-700 text-[11px] uppercase tracking-wider">Features Included:</div>
                        @if ($plan->features)
                            @foreach (explode(',', $plan->features) as $feature)
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="check" class="h-3.5 w-3.5 text-emerald-500 shrink-0"></i>
                                    <span>{{ trim($feature) }}</span>
                                </div>
                            @endforeach
                        @else
                            <span class="text-slate-400 italic">Standard CRM features</span>
                        @endif
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11.5px] text-slate-500 font-medium">
                        <strong>{{ $plan->companies_count }}</strong> tenants enrolled
                    </span>
                    <button type="button" class="text-xs font-semibold text-orange-600 hover:text-orange-700 bg-transparent border-0 cursor-pointer p-0" data-bs-toggle="modal" data-bs-target="#editPlanModal{{ $plan->id }}">
                        Edit Plan &rarr;
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Subscriptions History Card --}}
    <div class="platform-card">
        <div class="platform-card-header">
            <div>
                <h3 class="text-sm font-bold text-slate-800 m-0">Subscription Assignments Log</h3>
                <span class="text-xs text-slate-400">Recent plan assignments and renewals across tenants</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Plan</th>
                        <th>Billing Cycle</th>
                        <th>Price Paid</th>
                        <th>Starts At</th>
                        <th>Expires At</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentSubscriptions as $sub)
                        <tr>
                            <td class="font-semibold text-slate-800">{{ $sub->company?->name ?? 'Unknown Company' }}</td>
                            <td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $sub->plan?->name ?? 'Custom' }}
                                </span>
                            </td>
                            <td class="capitalize text-slate-600 text-xs">{{ $sub->billing_cycle }}</td>
                            <td class="font-semibold text-slate-800 text-xs">₹{{ number_format($sub->price_paid, 2) }}</td>
                            <td class="text-xs text-slate-600">{{ $sub->starts_at->format('d M Y') }}</td>
                            <td class="text-xs {{ $sub->isExpired() ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                {{ $sub->expires_at ? $sub->expires_at->format('d M Y') : 'Lifetime / None' }}
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $sub->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">No subscription records found yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal: Assign Subscription using shared <x-form.input> --}}
<div class="modal fade" id="assignSubscriptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg rounded-xl overflow-hidden" method="POST" action="{{ route('platform.subscriptions.assign') }}">
            @csrf
            <div class="modal-header border-b border-slate-100 px-5 py-3.5 bg-slate-50">
                <h5 class="text-sm font-bold text-slate-800 m-0">Assign Subscription to Tenant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-5 space-y-3.5">
                <x-form.input
                    layout="standard"
                    label="Select Tenant / Company"
                    name="company_id"
                    type="select"
                    :options="$companies->pluck('name', 'id')->prepend('Choose Tenant...', '')->toArray()"
                    required
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Select Plan"
                    name="plan_id"
                    type="select"
                    :options="$plans->pluck('name', 'id')->prepend('Choose Plan...', '')->toArray()"
                    required
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Billing Cycle"
                    name="billing_cycle"
                    type="select"
                    :options="[
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly (Annual)',
                        'lifetime' => 'Lifetime',
                    ]"
                    required
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Start Date"
                    name="starts_at"
                    type="date"
                    :value="date('Y-m-d')"
                    required
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Expiry Date"
                    name="expires_at"
                    type="date"
                    :value="now()->addMonths(1)->format('Y-m-d')"
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Price Paid (INR ₹)"
                    name="price_paid"
                    type="number"
                    step="0.01"
                    placeholder="e.g. 2499.00"
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Internal Notes"
                    name="notes"
                    type="textarea"
                    placeholder="Renewal or special terms..."
                    class="form-control text-xs"
                />
            </div>

            <div class="modal-footer border-t border-slate-100 px-5 py-3 bg-slate-50 flex justify-end gap-2">
                <button type="button" class="btn btn-sm btn-light text-xs font-semibold px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-warning text-xs font-semibold px-4 bg-orange-600 text-white hover:bg-orange-700 border-0">Assign Subscription</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Create Plan using shared <x-form.input> --}}
<div class="modal fade" id="createPlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg rounded-xl overflow-hidden" method="POST" action="{{ route('platform.subscriptions.plans.store') }}">
            @csrf
            <div class="modal-header border-b border-slate-100 px-5 py-3.5 bg-slate-50">
                <h5 class="text-sm font-bold text-slate-800 m-0">Create Subscription Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-5 space-y-3.5">
                <x-form.input
                    layout="standard"
                    label="Plan Name"
                    name="name"
                    required
                    placeholder="e.g. Starter, Growth, Enterprise"
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Description"
                    name="description"
                    placeholder="Brief description of who this plan is for..."
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Price (INR ₹)"
                    name="price"
                    type="number"
                    step="0.01"
                    required
                    placeholder="e.g. 2999"
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Billing Cycle"
                    name="billing_cycle"
                    type="select"
                    :options="[
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                        'lifetime' => 'Lifetime',
                    ]"
                    required
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Max Users Limit (leave blank for unlimited)"
                    name="max_users"
                    type="number"
                    placeholder="e.g. 15"
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Included Features (comma-separated)"
                    name="features"
                    placeholder="Leads, Scheduler, Call Logs, HR Module, Integrations"
                    class="form-control text-xs"
                />
            </div>

            <div class="modal-footer border-t border-slate-100 px-5 py-3 bg-slate-50 flex justify-end gap-2">
                <button type="button" class="btn btn-sm btn-light text-xs font-semibold px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-warning text-xs font-semibold px-4 bg-orange-600 text-white hover:bg-orange-700 border-0">Save Plan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modals: Edit Plan for each plan using shared <x-form.input> --}}
@foreach ($plans as $plan)
    <div class="modal fade" id="editPlanModal{{ $plan->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow-lg rounded-xl overflow-hidden" method="POST" action="{{ route('platform.subscriptions.plans.update', $plan) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header border-b border-slate-100 px-5 py-3.5 bg-slate-50">
                    <h5 class="text-sm font-bold text-slate-800 m-0">Edit Plan: {{ $plan->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-5 space-y-3.5">
                    <x-form.input
                        layout="standard"
                        label="Plan Name"
                        name="name"
                        :value="$plan->name"
                        required
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Description"
                        name="description"
                        :value="$plan->description"
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Price (INR ₹)"
                        name="price"
                        type="number"
                        step="0.01"
                        :value="$plan->price"
                        required
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Billing Cycle"
                        name="billing_cycle"
                        type="select"
                        :value="$plan->billing_cycle"
                        :options="[
                            'monthly' => 'Monthly',
                            'yearly' => 'Yearly',
                            'lifetime' => 'Lifetime',
                        ]"
                        required
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Max Users Limit (leave blank for unlimited)"
                        name="max_users"
                        type="number"
                        :value="$plan->max_users"
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Included Features (comma-separated)"
                        name="features"
                        :value="$plan->features"
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Status"
                        name="is_active"
                        type="select"
                        :value="$plan->is_active ? 1 : 0"
                        :options="[
                            '1' => 'Active (Available for tenants)',
                            '0' => 'Disabled (Hidden)',
                        ]"
                        required
                        class="form-control text-xs"
                    />
                </div>

                <div class="modal-footer border-t border-slate-100 px-5 py-3 bg-slate-50 flex justify-end gap-2">
                    <button type="button" class="btn btn-sm btn-light text-xs font-semibold px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-warning text-xs font-semibold px-4 bg-orange-600 text-white hover:bg-orange-700 border-0">Update Plan</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection
