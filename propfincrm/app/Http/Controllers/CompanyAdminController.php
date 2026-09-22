<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyIntegration;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Jobs\SyncCompanyIntegrationJob;

class CompanyAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'company.admin']);
    }

    public function index(Request $request)
    {
        $company = $request->user()->activeCompany();
        return view('companies.admin', [
            'company' => $company,
            'settings' => ['timezone' => Setting::query()->first()?->timezone ?? 'Asia/Kolkata'],
            'integrations' => CompanyIntegration::with('source')->get(),
            'providers' => config('lead_providers', []),
            'sources' => \App\SourceMasterTableModel::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'settings' => ['array'],
            'settings.company_name' => ['nullable', 'string', 'max:120'],
            'settings.timezone' => ['nullable', 'timezone'],
        ]);
        $company = $request->user()->activeCompany();
        $name = trim((string) ($data['settings']['company_name'] ?? $company->name));
        if ($name !== '' && $name !== $company->name) {
            $baseSlug = Str::slug($name) ?: 'company';
            $slug = $baseSlug;
            $suffix = 2;
            while (Company::where('slug', $slug)->whereKeyNot($company->id)->exists()) {
                $slug = $baseSlug . '-' . $suffix++;
            }
            $company->update(['name' => $name, 'slug' => $slug]);
            if ($tenant = $company->tenant) {
                $tenant->update(['data' => array_merge($tenant->data ?? [], ['company_name' => $name])]);
            }
        }
        $setting = Setting::query()->firstOrNew(['company_id' => $company->id]);
        if (!$setting->exists) {
            $setting->fill([
                'task_complete_allowed' => 2,
                'task_assign_allowed' => 2,
                'lead_complete_allowed' => 2,
                'lead_assign_allowed' => 2,
                'time_change_allowed' => 2,
                'comment_allowed' => 2,
            ]);
        }
        $setting->company = $company->name;
        $setting->timezone = $data['settings']['timezone'] ?? 'Asia/Kolkata';
        $setting->save();
        return back()->with('flash_message', 'Company settings updated.');
    }

    public function upsertIntegration(Request $request)
    {
        $data = $request->validate([
            'id' => ['nullable', 'integer'], 'provider' => ['required', 'string', 'max:64'], 'name' => ['nullable', 'string', 'max:120'],
            'source_id' => ['nullable', 'integer'], 'sync_frequency' => ['required', 'in:manual,every_15_minutes,every_30_minutes,hourly,twice_daily,daily,weekly'],
            'credentials' => ['required', 'array'], 'configuration' => ['nullable', 'array'], 'is_active' => ['nullable', 'boolean'],
        ]);

        $integration = !empty($data['id']) ? CompanyIntegration::findOrFail($data['id']) : new CompanyIntegration();

        if (in_array(strtolower($data['provider']), ['99acres', '99_acres'], true)) {
            $request->validate([
                'credentials.endpoint' => ['required', 'url'],
                'credentials.username' => ['required', 'string'],
                'credentials.password' => [$integration->exists ? 'nullable' : 'required', 'string'],
            ]);
        }

        if ($integration->exists && empty($data['credentials']['password']) && !empty($integration->credentials['password'])) {
            $data['credentials']['password'] = $integration->credentials['password'];
        }

        $data['name'] = $data['name'] ?: (config('lead_providers.' . $data['provider'] . '.name') ?? $data['provider']);
        $integration->fill($data + ['is_active' => $request->boolean('is_active')])->save();
        return back()->with('flash_message', 'Integration saved.');
    }

    public function syncIntegration(Request $request, $id, \App\Services\CompanyLeadSyncService $syncService)
    {
        $integration = CompanyIntegration::findOrFail($id);
        abort_unless($integration->is_active, 422, 'This integration is disabled. Enable it before syncing.');

        // When testing manually from the UI or requested synchronously:
        if ($request->boolean('now', true)) {
            try {
                $result = $syncService->sync($integration);
                $integration->refresh();
                return response()->json([
                    'ok' => true,
                    'queued' => false,
                    'integration_id' => $integration->id,
                    'message' => sprintf('Synced successfully: %d received (%d new created, %d duplicate).', $result['received'], $result['created'], $result['duplicates']),
                    'result' => $result,
                    'last_synced_at' => $integration->last_synced_at?->diffForHumans() ?? 'Just now',
                ]);
            } catch (\Throwable $e) {
                return response()->json([
                    'ok' => false,
                    'integration_id' => $integration->id,
                    'message' => $e->getMessage(),
                ], 422);
            }
        }

        SyncCompanyIntegrationJob::dispatch($integration->id);
        return response()->json(['ok' => true, 'queued' => true, 'integration_id' => $integration->id, 'message' => 'Sync queued in background.'], 202);
    }

    public function destroyIntegration(Request $request, $id)
    {
        $integration = CompanyIntegration::findOrFail($id);
        $integration->delete();
        return back()->with('flash_message', 'Integration deleted.');
    }

    public function syncAll(Request $request)
    {
        $integrationIds = [];
        foreach (CompanyIntegration::where('is_active', true)->get() as $integration) {
            SyncCompanyIntegrationJob::dispatch($integration->id);
            $integrationIds[] = $integration->id;
        }
        return response()->json(['ok' => true, 'queued' => true, 'integration_ids' => $integrationIds], 202);
    }
}
