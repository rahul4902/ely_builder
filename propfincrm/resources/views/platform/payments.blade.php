@extends('layouts.master')

@section('page-title', 'Payment Collection')
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
    .badge-status.completed { background: #dcfce7; color: #15803d; }
    .badge-status.pending { background: #fef3c7; color: #b45309; }
    .badge-status.failed { background: #fee2e2; color: #b91c1c; }
</style>
@endsection

@section('content')
<div class="platform-container space-y-6">

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Payment Collection Ledger</h1>
            <p class="text-xs text-slate-500 mt-0.5">Track, record, and verify subscription payments collected across tenants.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <button type="button" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-600 text-xs font-semibold text-white hover:bg-emerald-700 transition shadow-sm border-0 cursor-pointer" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                Record Payment
            </button>
        </div>
    </div>

    {{-- Revenue Metrics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Revenue Collected</div>
                <div class="stat-val text-emerald-600">₹{{ number_format($metrics['total_collected'], 2) }}</div>
            </div>
            <div class="stat-icon bg-emerald-50 text-emerald-600">
                <i data-lucide="banknote" class="h-5 w-5"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Collected This Month</div>
                <div class="stat-val text-slate-900">₹{{ number_format($metrics['this_month'], 2) }}</div>
            </div>
            <div class="stat-icon bg-blue-50 text-blue-600">
                <i data-lucide="calendar" class="h-5 w-5"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Pending / Unconfirmed</div>
                <div class="stat-val text-amber-600">₹{{ number_format($metrics['pending_amount'], 2) }}</div>
            </div>
            <div class="stat-icon bg-amber-50 text-amber-600">
                <i data-lucide="clock" class="h-5 w-5"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Total Transactions</div>
                <div class="stat-val text-slate-800">{{ $metrics['total_transactions'] }}</div>
            </div>
            <div class="stat-icon bg-violet-50 text-violet-600">
                <i data-lucide="receipt" class="h-5 w-5"></i>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-sm">
        <form method="GET" action="{{ route('platform.payments.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end m-0">
            <x-form.input
                layout="standard"
                label="Filter by Tenant"
                name="company_id"
                type="select"
                :value="request('company_id')"
                :options="$companies->pluck('name', 'id')->prepend('All Tenants', '')->toArray()"
                class="form-control text-xs"
            />

            <x-form.input
                layout="standard"
                label="Payment Method"
                name="payment_method"
                type="select"
                :value="request('payment_method')"
                :options="collect($paymentMethods)->prepend('All Methods', '')->toArray()"
                class="form-control text-xs"
            />

            <x-form.input
                layout="standard"
                label="Status"
                name="status"
                type="select"
                :value="request('status')"
                :options="[
                    '' => 'All Statuses',
                    'completed' => 'Completed',
                    'pending' => 'Pending',
                    'failed' => 'Failed',
                ]"
                class="form-control text-xs"
            />

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full h-[35px] rounded-lg bg-slate-800 text-xs font-semibold text-white hover:bg-slate-900 transition border-0 cursor-pointer">
                    Apply Filter
                </button>
                @if (request()->hasAny(['company_id', 'payment_method', 'status']))
                    <a href="{{ route('platform.payments.index') }}" class="h-[35px] px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-50 transition no-underline">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Payments Table Card --}}
    <div class="platform-card">
        <div class="platform-card-header">
            <div>
                <h3 class="text-sm font-bold text-slate-800 m-0">Transactions Ledger</h3>
                <span class="text-xs text-slate-400">Chronological ledger of tenant payments and fees</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Date &amp; Ref ID</th>
                        <th>Tenant / Company</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Recorded By</th>
                        <th>Notes</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>
                                <div class="font-semibold text-slate-800 text-xs">{{ $payment->payment_date->format('d M Y') }}</div>
                                <div class="font-mono text-[11px] text-slate-400 mt-0.5">
                                    {{ $payment->transaction_reference ?: 'No Reference' }}
                                </div>
                            </td>

                            <td>
                                <div class="font-semibold text-slate-800 text-[13px]">{{ $payment->company?->name ?? 'Unknown' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $payment->company?->planName() }}</div>
                            </td>

                            <td class="font-bold text-slate-900 text-sm">
                                {{ $payment->formattedAmount() }}
                            </td>

                            <td>
                                @if ($payment->payment_method === 'razorpay')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i data-lucide="zap" class="h-3 w-3 text-blue-600"></i> Razorpay
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700 capitalize">
                                        {{ str_replace('_', ' ', $payment->payment_method) }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="badge-status {{ $payment->status }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $payment->status === 'completed' ? 'bg-emerald-500' : ($payment->status === 'pending' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>

                            <td class="text-xs text-slate-600">
                                {{ $payment->recordedBy?->name ?? 'System' }}
                            </td>

                            <td class="text-xs text-slate-500 max-w-[200px] truncate" title="{{ $payment->notes }}">
                                {{ $payment->notes ?: '—' }}
                            </td>

                            <td class="text-end">
                                {{-- 3-Dot Ellipsis Dropdown (Strict AGENTS.md Lead-List Action Pattern) --}}
                                <div class="dropdown text-end inline-block">
                                    <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition border-0 bg-transparent cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i class="fa-solid fa-ellipsis text-sm"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 140px;">
                                        <li>
                                            <form action="{{ route('platform.payments.destroy', $payment) }}" method="POST" class="m-0 p-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item py-1.5 px-3 flex items-center text-rose-600 hover:bg-rose-50 w-full text-start border-0 bg-transparent" onclick="return confirm('Do you want to delete this payment record?')">
                                                    <i class="fa-regular fa-trash-can me-2 text-rose-500"></i> Delete Record
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i data-lucide="receipt" class="h-8 w-8 mx-auto mb-2 text-slate-300"></i>
                                No payment records matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payments->hasPages())
            <div class="px-5 py-3 border-t border-slate-100">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Global Record Payment Modal using shared <x-form.input> --}}
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg rounded-xl overflow-hidden" method="POST" action="{{ route('platform.payments.store') }}">
            @csrf
            <input type="hidden" name="currency" value="INR">

            <div class="modal-header border-b border-slate-100 px-5 py-3.5 bg-slate-50">
                <h5 class="text-sm font-bold text-slate-800 m-0">Record Tenant Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-5 space-y-3.5">
                <x-form.input
                    layout="standard"
                    label="Select Tenant / Company"
                    name="company_id"
                    type="select"
                    :options="$companies->pluck('name', 'id')->prepend('Select Tenant...', '')->toArray()"
                    required
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Amount Received (INR ₹)"
                    name="amount"
                    type="number"
                    step="0.01"
                    placeholder="e.g. 2499.00"
                    required
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Payment Method"
                    name="payment_method"
                    type="select"
                    :options="$paymentMethods"
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
                    placeholder="Receipt notes, invoice reference, bank details..."
                    class="form-control text-xs"
                />
            </div>

            <div class="modal-footer border-t border-slate-100 px-5 py-3 bg-slate-50 flex justify-end gap-2">
                <button type="button" class="btn btn-sm btn-light text-xs font-semibold px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-success text-xs font-semibold px-4 bg-emerald-600 text-white hover:bg-emerald-700 border-0">Save Payment Record</button>
            </div>
        </form>
    </div>
</div>
@endsection
