<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyPayment;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\CompanyProvisioner;
use Illuminate\Http\Request;

class PlatformCompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'platform.super_admin']);
    }

    public function index()
    {
        $companies = Company::with([
            'plan',
            'settings',
            'users' => function ($query) {
                $query->wherePivot('role', 'company_admin')->wherePivot('is_active', true);
            },
        ])
        ->withCount('activeUsers')
        ->withSum(['payments as completed_payments_sum' => function ($query) {
            $query->where('status', 'completed');
        }], 'amount')
        ->orderBy('name')
        ->get();

        $metrics = [
            'total_companies' => $companies->count(),
            'active_companies' => $companies->where('is_active', true)->count(),
            'suspended_companies' => $companies->where('is_active', false)->count(),
            'expired_companies' => $companies->filter(fn($c) => $c->isExpired())->count(),
            'total_revenue' => CompanyPayment::where('status', 'completed')->sum('amount'),
            'open_tickets' => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
        ];

        return view('platform.companies', [
            'companies' => $companies,
            'metrics' => $metrics,
            'plans' => Plan::where('is_active', true)->orderBy('price')->get(),
            'users' => User::orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function store(Request $request, CompanyProvisioner $provisioner)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'administrator_email' => ['nullable', 'email'],
            'timezone' => ['required', 'timezone'],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'plan_expires_at' => ['nullable', 'date'],
            'max_users' => ['nullable', 'integer', 'min:1'],
        ]);

        $administrator = !empty($data['administrator_email'])
            ? User::where('email', $data['administrator_email'])->firstOrFail()
            : null;

        $provisionData = [
            'name' => $data['name'],
            'timezone' => $data['timezone'],
            'plan_id' => $data['plan_id'] ?? null,
            'plan_expires_at' => !empty($data['plan_expires_at']) ? $data['plan_expires_at'] : now()->addMonths(1),
            'max_users' => $data['max_users'] ?? null,
            'subscription_status' => 'active',
        ];

        $provisioner->provision($provisionData, $administrator);

        return back()->with('flash_message', 'Company tenant provisioned successfully.');
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'timezone' => ['required', 'timezone'],
            'administrator_id' => ['nullable', 'integer', 'exists:users,id'],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'plan_expires_at' => ['nullable', 'date'],
            'subscription_status' => ['nullable', 'string', 'in:active,trial,expired,suspended,cancelled'],
            'max_users' => ['nullable', 'integer', 'min:1'],
        ]);

        $updateFields = [
            'name' => $data['name'],
            'plan_id' => $data['plan_id'] ?? $company->plan_id,
            'plan_expires_at' => $data['plan_expires_at'] ?? $company->plan_expires_at,
            'subscription_status' => $data['subscription_status'] ?? $company->subscription_status,
            'max_users' => $data['max_users'] ?? $company->max_users,
        ];

        $company->update($updateFields);
        $company->tenant?->update(['data' => array_merge($company->tenant->data ?? [], ['company_name' => $company->name])]);

        Setting::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $company->id],
            ['company' => $company->name, 'timezone' => $data['timezone']]
        );

        if (!empty($data['administrator_id'])) {
            $company->users()->syncWithoutDetaching([
                $data['administrator_id'] => ['role' => 'company_admin', 'is_active' => true],
            ]);
        }

        return back()->with('flash_message', 'Company details updated.');
    }

    public function updateStatus(Request $request, Company $company)
    {
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $company->update(['is_active' => $data['is_active']]);

        return back()->with('flash_message', $company->is_active ? 'Company activated.' : 'Company access suspended.');
    }

    /**
     * One-click tenant impersonation for Platform Superadmin.
     */
    public function impersonate(Request $request, Company $company)
    {
        $user = $request->user();
        abort_unless($user->isPlatformSuperAdmin(), 403, 'Unauthorized.');

        $request->session()->put('impersonating_company_id', $company->id);
        $request->session()->put('active_company_id', $company->id);

        return redirect()->route('dashboard')->with('flash_message', "Viewing workspace for {$company->name} as Platform Superadmin.");
    }

    /**
     * Leave tenant impersonation and return to Superadmin platform.
     */
    public function leaveImpersonation(Request $request)
    {
        $request->session()->forget('impersonating_company_id');
        $request->session()->forget('active_company_id');

        return redirect()->route('platform.companies.index')->with('flash_message', 'Exited tenant workspace. Welcome back to Platform Superadmin.');
    }
}
