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
            'settings' => [
                'timezone' => Setting::query()->first()?->timezone ?? 'Asia/Kolkata',
                'theme_mode' => Setting::query()->first()?->theme_mode ?? 'light',
                'theme_accent' => Setting::query()->first()?->theme_accent ?? '#0B57D0',
            ],
            'integrations' => CompanyIntegration::with('source')->get(),
            'providers' => config('lead_providers', []),
            'sources' => \App\SourceMasterTableModel::where('status', 1)->orderBy('source_name')->get(['id', 'source_name']),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'settings' => ['array'],
            'settings.company_name' => ['nullable', 'string', 'max:120'],
            'settings.timezone' => ['nullable', 'timezone'],
            'settings.theme_mode' => ['nullable', 'in:light,dark'],
            'settings.theme_accent' => ['nullable', 'string', 'max:10'],
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
        if (!empty($data['settings']['timezone'])) {
            $setting->timezone = $data['settings']['timezone'];
        }
        if (isset($data['settings']['theme_mode'])) {
            $setting->theme_mode = $data['settings']['theme_mode'];
        }
        if (isset($data['settings']['theme_accent'])) {
            $setting->theme_accent = $data['settings']['theme_accent'];
        }
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

        $provider = strtolower($data['provider']);
        abort_unless(array_key_exists($provider, config('lead_providers', [])), 422, 'Unknown lead provider.');
        abort_unless((bool) (config('lead_providers.' . $provider . '.enabled') ?? false), 422, 'This lead provider is not available yet.');

        if (in_array($provider, ['99acres', '99_acres'], true)) {
            $request->validate([
                'credentials.endpoint' => ['required', 'url'],
                'credentials.username' => ['required', 'string'],
                'credentials.password' => [$integration->exists ? 'nullable' : 'required', 'string'],
            ]);
        }
        if ($provider === 'meta_lead_ads') {
            $request->validate([
                'credentials.page_id' => ['required', 'string', 'max:80'],
                'credentials.access_token' => ['required', 'string'],
                'credentials.app_secret' => ['required', 'string'],
                'credentials.webhook_verify_token' => ['required', 'string', 'min:16'],
                'credentials.form_id' => ['nullable', 'string', 'max:80'],
            ]);
        }

        if ($integration->exists && empty($data['credentials']['password']) && !empty($integration->credentials['password'])) {
            $data['credentials']['password'] = $integration->credentials['password'];
        }
        if (in_array($provider, ['website_api', 'generic_webhook', 'google_forms'], true)) {
            $data['credentials']['webhook_token'] = $integration->credentials['webhook_token'] ?? bin2hex(random_bytes(24));
        }
        if (in_array($provider, ['meta_lead_ads', 'website_api', 'generic_webhook', 'google_forms'], true)) {
            // These sources deliver new leads instantly. They must not be placed
            // on the polling scheduler (Meta historical imports remain available
            // through the explicit Test action when a Form ID is configured).
            $data['sync_frequency'] = 'manual';
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
