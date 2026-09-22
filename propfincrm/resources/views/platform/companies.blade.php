@extends('layouts.master')

@section('page-title', 'Platform Tenants')
@section('main-class', 'p-0 bg-slate-50/50 min-h-[calc(100vh-44px)]')

@section('styles')
<style>
    .platform-container { padding: 20px 24px 40px; max-width: 1400px; margin: 0 auto; font-family: 'Outfit', sans-serif; }
    .stat-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.03); }
    .stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .stat-label { font-size: 11.5px; font-weight: 500; color: #64748b; margin-bottom: 2px; }
    .stat-val { font-size: 20px; font-weight: 700; color: #1e293b; line-height: 1.1; }
    
    .platform-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); overflow: hidden; }
    .platform-card-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #edf2f7; }
    
    .custom-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .custom-table th { padding: 12px 18px; background: #f8fafc; color: #64748b; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #edf2f7; text-align: left; }
    .custom-table td { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
    .custom-table tr:hover { background-color: #fafbfc; }

    .badge-status { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 9999px; font-size: 11px; font-weight: 600; }
    .badge-status.active { background: #dcfce7; color: #15803d; }
    .badge-status.suspended { background: #fee2e2; color: #b91c1c; }
    .badge-status.expired { background: #fef3c7; color: #b45309; }
</style>
@endsection

@section('content')
<div class="platform-container space-y-6">

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Tenants &amp; Companies</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage isolated tenant workspaces, active subscriptions, expiry dates, and 1-click login.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('platform.payments.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm no-underline">
                <i data-lucide="receipt" class="h-3.5 w-3.5 text-slate-500"></i>
                Payments
            </a>
            <a href="{{ route('platform.tickets.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm no-underline">
                <i data-lucide="life-buoy" class="h-3.5 w-3.5 text-slate-500"></i>
                Tickets Desk
            </a>
            <button type="button" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-orange-600 text-xs font-semibold text-white hover:bg-orange-700 transition shadow-sm border-0 cursor-pointer" data-bs-toggle="modal" data-bs-target="#createCompanyModal">
                <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                Add Tenant
            </button>
        </div>
    </div>

    {{-- Platform Metrics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Tenants</div>
                <div class="stat-val">{{ $metrics['total_companies'] }}</div>
            </div>
            <div class="stat-icon bg-blue-50 text-blue-600">
                <i data-lucide="building-2" class="h-5 w-5"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Active Workspaces</div>
                <div class="stat-val text-emerald-600">{{ $metrics['active_companies'] }}</div>
            </div>
            <div class="stat-icon bg-emerald-50 text-emerald-600">
                <i data-lucide="check-circle-2" class="h-5 w-5"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Suspended / Expired</div>
                <div class="stat-val text-amber-600">{{ $metrics['suspended_companies'] + $metrics['expired_companies'] }}</div>
            </div>
            <div class="stat-icon bg-amber-50 text-amber-600">
                <i data-lucide="alert-triangle" class="h-5 w-5"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Collected Revenue</div>
                <div class="stat-val text-slate-900">₹{{ number_format($metrics['total_revenue'], 0) }}</div>
            </div>
            <div class="stat-icon bg-violet-50 text-violet-600">
                <i data-lucide="wallet" class="h-5 w-5"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Open Tickets</div>
                <div class="stat-val text-rose-600">{{ $metrics['open_tickets'] }}</div>
            </div>
            <div class="stat-icon bg-rose-50 text-rose-600">
                <i data-lucide="message-square" class="h-5 w-5"></i>
            </div>
        </div>
    </div>

    {{-- Tenants Main Table Card --}}
    <div class="platform-card">
        <div class="platform-card-header">
            <div>
                <h3 class="text-sm font-bold text-slate-800 m-0">All Tenant Workspaces</h3>
                <span class="text-xs text-slate-400">Total of {{ $companies->count() }} company instances on this platform</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Company &amp; Tenant ID</th>
                        <th>Current Plan</th>
                        <th>Expiry Date</th>
                        <th>Admin &amp; Users</th>
                        <th>Payments</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($companies as $company)
                        @php
                            $isExpired = $company->isExpired();
                            $daysRemaining = $company->daysRemaining();
                        @endphp
                        <tr>
                            <td>
                                <div class="font-semibold text-slate-800 text-[13.5px]">{{ $company->name }}</div>
                                <div class="font-mono text-[11px] text-slate-400 mt-0.5 flex items-center gap-1">
                                    <span>slug: {{ $company->slug }}</span>
                                    <span>·</span>
                                    <span title="{{ $company->tenant_id }}">{{ substr($company->tenant_id, 0, 13) }}...</span>
                                </div>
                            </td>

                            <td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $company->planName() }}
                                </span>
                            </td>

                            <td>
                                @if ($company->plan_expires_at)
                                    <div class="font-medium text-[12.5px] {{ $isExpired ? 'text-rose-600 font-bold' : 'text-slate-700' }}">
                                        {{ $company->plan_expires_at->format('d M Y') }}
                                    </div>
                                    <div class="text-[10.5px] mt-0.5">
                                        @if ($isExpired)
                                            <span class="text-rose-600 font-semibold">Expired {{ abs($daysRemaining) }}d ago</span>
                                        @elseif ($daysRemaining <= 7)
                                            <span class="text-amber-600 font-semibold">Expires in {{ $daysRemaining }}d</span>
                                        @else
                                            <span class="text-slate-400">{{ $daysRemaining }} days remaining</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs">No Expiry Set</span>
                                @endif
                            </td>

                            <td>
                                <div class="text-xs text-slate-700 font-medium">
                                    {{ $company->users->pluck('email')->first() ?: 'Unassigned' }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $company->active_users_count }} active users
                                    @if ($company->max_users)
                                        / max {{ $company->max_users }}
                                    @endif
                                </div>
                            </td>

                            <td>
                                <div class="text-xs font-semibold text-slate-800">
                                    ₹{{ number_format($company->completed_payments_sum ?? 0, 0) }}
                                </div>
                                <div class="text-[10.5px] text-slate-400">Total collected</div>
                            </td>

                            <td>
                                @if (!$company->is_active)
                                    <span class="badge-status suspended">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Suspended
                                    </span>
                                @elseif ($isExpired)
                                    <span class="badge-status expired">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Expired
                                    </span>
                                @else
                                    <span class="badge-status active">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @endif
                            </td>

                            <td class="text-end">
                                {{-- 3-Dot Ellipsis Dropdown (Strict AGENTS.md Lead-List Action Pattern) --}}
                                <div class="dropdown text-end inline-block">
                                    <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition border-0 bg-transparent cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i class="fa-solid fa-ellipsis text-sm"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 175px;">
                                        {{-- 1-Click Login / Impersonate --}}
                                        <li>
                                            <form action="{{ route('platform.companies.impersonate', $company) }}" method="POST" class="m-0 p-0">
                                                @csrf
                                                <button type="submit" class="dropdown-item py-1.5 px-3 flex items-center text-slate-700 hover:bg-slate-50 w-full text-start border-0 bg-transparent">
                                                    <i class="fa-solid fa-right-to-bracket me-2 text-indigo-500"></i> Login as Tenant
                                                </button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider my-1 border-slate-100"></li>
                                        {{-- Edit Details & Expiry --}}
                                        <li>
                                            <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-slate-700 hover:bg-slate-50 w-full text-start border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#editCompany{{ $company->id }}">
                                                <i class="fa-regular fa-pen-to-square me-2 text-slate-400"></i> Edit Details &amp; Plan
                                            </button>
                                        </li>
                                        {{-- Record Payment Modal Trigger --}}
                                        <li>
                                            <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-slate-700 hover:bg-slate-50 w-full text-start border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#paymentModal{{ $company->id }}">
                                                <i class="fa-solid fa-receipt me-2 text-emerald-500"></i> Record Payment
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider my-1 border-slate-100"></li>
                                        {{-- Toggle Activation (Suspend / Activate) --}}
                                        <li>
                                            <form action="{{ route('platform.companies.status', $company) }}" method="POST" class="m-0 p-0">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="is_active" value="{{ $company->is_active ? 0 : 1 }}">
                                                <button type="submit" class="dropdown-item py-1.5 px-3 flex items-center {{ $company->is_active ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-600 hover:bg-emerald-50' }} w-full text-start border-0 bg-transparent" onclick="return confirm('Are you sure you want to {{ $company->is_active ? 'suspend' : 'activate' }} {{ $company->name }}?')">
                                                    @if ($company->is_active)
                                                        <i class="fa-solid fa-ban me-2 text-rose-500"></i> Suspend Workspace
                                                    @else
                                                        <i class="fa-solid fa-check me-2 text-emerald-500"></i> Activate Workspace
                                                    @endif
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i data-lucide="building" class="h-8 w-8 mx-auto mb-2 text-slate-300"></i>
                                No tenant workspaces created yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modals for each company (Edit + Quick Payment) --}}
@foreach ($companies as $company)
    {{-- Edit Company Modal using shared <x-form.input> --}}
    <div class="modal fade" id="editCompany{{ $company->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow-lg rounded-xl overflow-hidden" method="POST" action="{{ route('platform.companies.update', $company) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header border-b border-slate-100 px-5 py-3.5 bg-slate-50">
                    <div>
                        <h5 class="text-sm font-bold text-slate-800 m-0">Edit {{ $company->name }}</h5>
                        <span class="text-[11px] text-slate-400 font-mono">Tenant {{ $company->tenant_id }}</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-5 space-y-3.5">
                    <x-form.input
                        layout="standard"
                        label="Company Name"
                        name="name"
                        :value="$company->name"
                        required
                        placeholder="Company name"
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Timezone"
                        name="timezone"
                        :value="optional($company->settings)->timezone ?: 'Asia/Kolkata'"
                        required
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Assigned Plan"
                        name="plan_id"
                        type="select"
                        :value="$company->plan_id"
                        :options="$plans->pluck('name', 'id')->prepend('No Plan', '')->toArray()"
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Subscription Expiry Date"
                        name="plan_expires_at"
                        type="date"
                        :value="optional($company->plan_expires_at)->format('Y-m-d')"
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Max Users Limit (blank for unlimited)"
                        name="max_users"
                        type="number"
                        :value="$company->max_users"
                        class="form-control text-xs"
                        placeholder="e.g. 10"
                    />

                    <x-form.input
                        layout="standard"
                        label="Assign Company Administrator"
                        name="administrator_id"
                        type="select"
                        :options="$users->mapWithKeys(fn($u) => [$u->id => $u->name . ' (' . $u->email . ')'])->prepend('Keep Current Administrators', '')->toArray()"
                        class="form-control text-xs"
                    />
                </div>

                <div class="modal-footer border-t border-slate-100 px-5 py-3 bg-slate-50 flex justify-end gap-2">
                    <button type="button" class="btn btn-sm btn-light text-xs font-semibold px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-warning text-xs font-semibold px-4 bg-orange-600 text-white hover:bg-orange-700 border-0">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Quick Record Payment Modal using shared <x-form.input> --}}
    <div class="modal fade" id="paymentModal{{ $company->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow-lg rounded-xl overflow-hidden" method="POST" action="{{ route('platform.payments.store') }}">
                @csrf
                <input type="hidden" name="company_id" value="{{ $company->id }}">
                <input type="hidden" name="currency" value="INR">

                <div class="modal-header border-b border-slate-100 px-5 py-3.5 bg-slate-50">
                    <div>
                        <h5 class="text-sm font-bold text-slate-800 m-0">Record Payment for {{ $company->name }}</h5>
                        <span class="text-[11px] text-slate-400">Current Plan: {{ $company->planName() }}</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-5 space-y-3.5">
                    <x-form.input
                        layout="standard"
                        label="Amount (INR ₹)"
                        name="amount"
                        type="number"
                        step="0.01"
                        :value="optional($company->plan)->price ?: '2499'"
                        required
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Payment Method"
                        name="payment_method"
                        type="select"
                        :options="[
                            'bank_transfer' => 'Bank Transfer (NEFT/RTGS/IMPS)',
                            'upi' => 'UPI / QR Code',
                            'cash' => 'Cash',
                            'cheque' => 'Cheque',
                            'card' => 'Debit / Credit Card',
                            'other' => 'Other Offline',
                        ]"
                        required
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Transaction Reference / Cheque No."
                        name="transaction_reference"
                        placeholder="e.g. UTR / UPI / Reference ID"
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Payment Date"
                        name="payment_date"
                        type="date"
                        :value="date('Y-m-d')"
                        required
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Payment Status"
                        name="status"
                        type="select"
                        :options="[
                            'completed' => 'Completed (Received)',
                            'pending' => 'Pending Confirmation',
                        ]"
                        required
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Auto-Extend Subscription (Months)"
                        name="extend_months"
                        type="select"
                        :options="[
                            '1' => 'Extend by 1 Month',
                            '3' => 'Extend by 3 Months (Quarterly)',
                            '6' => 'Extend by 6 Months',
                            '12' => 'Extend by 12 Months (Yearly)',
                            '0' => 'Do not change expiry date',
                        ]"
                        value="1"
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Notes / Receipt Remarks"
                        name="notes"
                        type="textarea"
                        placeholder="Additional notes for receipt..."
                        class="form-control text-xs"
                    />
                </div>

                <div class="modal-footer border-t border-slate-100 px-5 py-3 bg-slate-50 flex justify-end gap-2">
                    <button type="button" class="btn btn-sm btn-light text-xs font-semibold px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success text-xs font-semibold px-4 bg-emerald-600 text-white hover:bg-emerald-700 border-0">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

{{-- Create Company Modal using shared <x-form.input> --}}
<div class="modal fade" id="createCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg rounded-xl overflow-hidden" method="POST" action="{{ route('platform.companies.store') }}">
            @csrf
            <div class="modal-header border-b border-slate-100 px-5 py-3.5 bg-slate-50">
                <h5 class="text-sm font-bold text-slate-800 m-0">Provision New Tenant Workspace</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-5 space-y-3.5">
                <x-form.input
                    layout="standard"
                    label="Company Name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    placeholder="e.g. Apex Realty"
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Timezone"
                    name="timezone"
                    value="{{ old('timezone', 'Asia/Kolkata') }}"
                    required
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Existing Administrator Email (optional)"
                    name="administrator_email"
                    type="email"
                    value="{{ old('administrator_email') }}"
                    placeholder="admin@example.com"
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Initial Plan"
                    name="plan_id"
                    type="select"
                    :options="$plans->pluck('name', 'id')->prepend('Select Plan', '')->toArray()"
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Initial Expiry Date"
                    name="plan_expires_at"
                    type="date"
                    :value="now()->addMonths(1)->format('Y-m-d')"
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Max Users Limit (leave blank for plan default)"
                    name="max_users"
                    type="number"
                    placeholder="e.g. 10"
                    class="form-control text-xs"
                />
            </div>

            <div class="modal-footer border-t border-slate-100 px-5 py-3 bg-slate-50 flex justify-end gap-2">
                <button type="button" class="btn btn-sm btn-light text-xs font-semibold px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-warning text-xs font-semibold px-4 bg-orange-600 text-white hover:bg-orange-700 border-0">Create Tenant Workspace</button>
            </div>
        </form>
    </div>
</div>
@endsection
