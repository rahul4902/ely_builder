<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyPayment;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlatformPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'platform.super_admin']);
    }

    public function index(Request $request)
    {
        $query = CompanyPayment::with(['company.plan', 'recordedBy'])
            ->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->input('company_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        $payments = $query->paginate(20)->withQueryString();

        $metrics = [
            'total_collected' => CompanyPayment::where('status', 'completed')->sum('amount'),
            'this_month' => CompanyPayment::where('status', 'completed')
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
            'pending_amount' => CompanyPayment::where('status', 'pending')->sum('amount'),
            'total_transactions' => CompanyPayment::count(),
        ];

        $companies = Company::orderBy('name')->get(['id', 'name', 'plan_expires_at']);

        return view('platform.payments', [
            'payments' => $payments,
            'metrics' => $metrics,
            'companies' => $companies,
            'paymentMethods' => CompanyPayment::paymentMethods(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['required', 'string', 'max:10'],
            'payment_method' => ['required', 'string'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'payment_date' => ['required', 'date'],
            'status' => ['required', 'string', 'in:completed,pending,failed,refunded'],
            'notes' => ['nullable', 'string', 'max:500'],
            'extend_months' => ['nullable', 'integer', 'min:0', 'max:36'],
        ]);

        $company = Company::findOrFail($data['company_id']);

        $payment = CompanyPayment::create([
            'company_id' => $company->id,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?: 'INR',
            'payment_method' => $data['payment_method'],
            'transaction_reference' => $data['transaction_reference'] ?? null,
            'payment_date' => $data['payment_date'],
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
            'recorded_by_user_id' => $request->user()->id,
        ]);

        // Auto-extend company subscription expiry date if requested and payment completed
        if (!empty($data['extend_months']) && $data['status'] === 'completed') {
            $currentExpiry = ($company->plan_expires_at && $company->plan_expires_at->isFuture())
                ? $company->plan_expires_at
                : now();

            $newExpiry = $currentExpiry->copy()->addMonths((int) $data['extend_months']);

            $company->update([
                'plan_expires_at' => $newExpiry,
                'subscription_status' => 'active',
                'is_active' => true,
            ]);
        }

        return back()->with('flash_message', "Payment of ₹" . number_format($payment->amount, 2) . " for {$company->name} recorded successfully.");
    }

    public function destroy(CompanyPayment $payment)
    {
        $payment->delete();

        return back()->with('flash_message', 'Payment record deleted.');
    }
}
