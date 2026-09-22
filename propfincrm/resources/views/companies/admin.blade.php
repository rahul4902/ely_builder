@extends('layouts.master')

@section('page-title', 'Company Settings')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')

@section('styles')
<style>
    /* Same flat, compact workspace rules as the Lead List page. */
    #company-settings-page { min-height:calc(100vh - 40px); padding:0; background:#fff; color:#334155; font-family:'Outfit',sans-serif; }
    #company-settings-page .company-settings-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; min-height:52px; padding:0 24px; border-bottom:1px solid #e8edf3; }
    #company-settings-page .company-settings-toolbar h1 { margin:0; color:#1e293b; font-size:14px; font-weight:600; }
    #company-settings-page .company-settings-company { border:1px solid #e2e8f0; border-radius:6px; background:#f8fafc; padding:5px 8px; color:#475569; font-size:11px; font-weight:600; }
    #company-settings-page .company-settings-layout { display:grid; grid-template-columns:200px minmax(0,1fr); min-height:calc(100vh - 92px); align-items:stretch; gap:0; }
    #company-settings-page .settings-sidebar { border-right:1px solid #e3e7ec; background:#f5f6f8; }
    #company-settings-page .settings-nav { overflow:hidden; background:#f5f6f8; }
    #company-settings-page .settings-nav-section { padding:20px 16px 8px; color:#94a3b8; font-size:10px; font-weight:600; letter-spacing:.05em; text-transform:uppercase; }
    #company-settings-page .settings-nav-section + .settings-nav-section { padding-top:21px; }
    #company-settings-page .settings-nav-button { display:flex; width:100%; align-items:center; gap:11px; border:0; background:transparent; padding:7px 16px; color:#42526a; font-size:12px; line-height:17px; text-align:left; transition:.15s ease; }
    #company-settings-page .settings-nav-button:hover { background:#fff; color:#17233b; }
    #company-settings-page .settings-nav-button.is-active { background:#edf1f5; color:#17233b; font-weight:600; box-shadow:inset 2px 0 0 #f97316; }
    #company-settings-page .settings-nav-button svg { width:16px; height:16px; flex:0 0 16px; color:#64748b; }
    #company-settings-page .settings-workspace { padding:16px 20px 28px; }
    #company-settings-page .settings-content-title { margin:0 0 12px; color:#1e293b; font-size:14px; font-weight:600; }
    #company-settings-page .settings-panel { width:100%; max-width:820px; overflow:hidden; border:1px solid #e2e8f0; border-radius:0; background:#fff; box-shadow:none; }
    #company-settings-page .settings-pane { display:none; min-height:0; }
    #company-settings-page .settings-pane.is-active { display:block; }
    #company-settings-form-body { padding:18px 20px; }
    #company-settings-page .settings-form-body { padding:18px 20px; }
    #company-settings-page .setting-row { padding:0 0 18px; border-bottom:1px solid #e8edf2; }
    #company-settings-page .setting-row + .setting-row { padding-top:18px; }
    #company-settings-page .setting-row:last-child { border-bottom:0; padding-bottom:0; }
    #company-settings-page .setting-label { display:block; margin-bottom:7px; color:#4b596d; font-size:12px; font-weight:600; }
    #company-settings-page .setting-help { margin:-2px 0 10px; color:#8b98a9; font-size:11px; }
    #company-settings-page .setting-control { width:100%; max-width:450px; height:34px; border:1px solid #d8e0e8; border-radius:6px; padding:6px 9px; color:#334155; font-size:12px; box-shadow:none; }
    #company-settings-page .setting-control:focus { border-color:#f97316; outline:0; box-shadow:0 0 0 2px rgba(249,115,22,.12); }
    #company-settings-page .settings-footer { display:flex; justify-content:flex-end; border-top:1px solid #e1e6ec; padding:12px 24px; background:#fff; }
    #company-settings-page .settings-save { border:1px solid #f97316; border-radius:7px; background:#f97316; padding:8px 14px; color:#fff; font-size:12px; font-weight:600; cursor:pointer; }
    #company-settings-page .settings-save:hover { background:#ea580c; border-color:#ea580c; }
    #company-settings-page .integration-form { padding:20px 24px; }
    #company-settings-page .provider-picker { display:flex; align-items:center; gap:8px; border-bottom:1px solid #e1e6ec; padding:12px 14px; background:#fff; }
    #company-settings-page .provider-scroll { display:flex; flex:1; gap:8px; overflow-x:auto; scroll-behavior:smooth; scrollbar-width:none; }
    #company-settings-page .provider-scroll::-webkit-scrollbar { display:none; }
    #company-settings-page .provider-card { min-width:138px; border:1px solid #dfe6ed; border-radius:6px; background:#fff; padding:9px 10px; color:#42526a; text-align:left; font-size:11px; cursor:pointer; }
    #company-settings-page .provider-card:hover:not(:disabled) { border-color:#94a3b8; }
    #company-settings-page .provider-card.is-selected { border-color:#f97316; box-shadow:inset 0 0 0 1px #f97316; color:#17233b; }
    #company-settings-page .provider-card.is-planned { border-style:dashed; opacity:0.75; }
    #company-settings-page .provider-card-title { display:flex; align-items:center; gap:6px; font-weight:700; }
    #company-settings-page .provider-card-title svg { width:14px; height:14px; }
    #company-settings-page .provider-scroll-button { display:flex; width:28px; height:28px; flex:0 0 28px; align-items:center; justify-content:center; border:1px solid #d8e0e8; border-radius:5px; background:#fff; color:#64748b; cursor:pointer; }
    #company-settings-page .provider-scroll-button:hover { background:#f1f5f9; color:#17233b; }
    #company-settings-page .integration-setup { display:block; }
    #company-settings-page .integration-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    #company-settings-page .integration-wide { grid-column:1 / -1; }
    #company-settings-page .integration-list { margin:0 24px 24px; border-top:1px solid #e8edf2; padding-top:18px; }
    #company-settings-page .integration-item { display:flex; flex-direction:column; gap:10px; padding:14px 16px; border:1px solid #e2e8f0; border-radius:8px; margin-bottom:12px; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,0.02); }
    #company-settings-page .integration-item-top { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; }
    #company-settings-page .status-active { color:#15803d; background:#dcfce7; padding:2px 8px; border-radius:9999px; font-size:10px; font-weight:600; display:inline-flex; align-items:center; }
    #company-settings-page .status-disabled { color:#64748b; background:#f1f5f9; padding:2px 8px; border-radius:9999px; font-size:10px; font-weight:600; display:inline-flex; align-items:center; }
    #company-settings-page .freq-badge { color:#1d4ed8; background:#eff6ff; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:600; border:1px solid #dbeafe; }
    #company-settings-page .integration-actions { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    #company-settings-page .integration-sync-btn { border:1px solid #fdba74; border-radius:6px; background:#fff7ed; padding:5px 10px; color:#c2410c; font-size:11px; font-weight:600; display:inline-flex; align-items:center; gap:5px; cursor:pointer; transition:.15s ease; }
    #company-settings-page .integration-sync-btn:hover:not(:disabled) { background:#ffedd5; border-color:#fb923c; }
    #company-settings-page .integration-sync-btn:disabled { opacity:.6; cursor:wait; }
    #company-settings-page .integration-edit-btn { border:1px solid #d8e0e8; border-radius:6px; background:#fff; padding:5px 10px; color:#475569; font-size:11px; font-weight:600; display:inline-flex; align-items:center; gap:5px; cursor:pointer; }
    #company-settings-page .integration-edit-btn:hover { background:#f8fafc; border-color:#94a3b8; color:#1e293b; }
    #company-settings-page .integration-del-btn { border:1px solid #fecaca; border-radius:6px; background:#fff; padding:5px 10px; color:#dc2626; font-size:11px; font-weight:600; display:inline-flex; align-items:center; gap:5px; cursor:pointer; }
    #company-settings-page .integration-del-btn:hover { background:#fef2f2; border-color:#f87171; }
    @media (max-width:850px) { #company-settings-page .company-settings-layout { grid-template-columns:1fr; } #company-settings-page .settings-sidebar { border-right:0; border-bottom:1px solid #e3e7ec; } #company-settings-page .settings-nav { display:flex; overflow-x:auto; } #company-settings-page .settings-nav-section, #company-settings-page .settings-nav-note { display:none; } #company-settings-page .settings-nav-button { width:auto; min-width:max-content; padding:12px 14px; } #company-settings-page .settings-nav-button.is-active { box-shadow:inset 0 -2px 0 #f97316; } }
    @media (max-width:560px) { #company-settings-page .integration-grid { grid-template-columns:1fr; } #company-settings-page .settings-form-body, #company-settings-page .integration-form { padding:18px; } #company-settings-page .integration-item-top { flex-direction:column; } }
</style>
@endsection

@section('content')
<div id="company-settings-page">
    <div class="company-settings-toolbar">
        <div><h1>Company Settings</h1></div>
        <span class="company-settings-company"><i data-lucide="building-2" class="mr-1 inline-block h-3 w-3"></i>{{ $company->name }}</span>
    </div>

    <div class="company-settings-layout">
        <aside class="settings-sidebar">
            <div class="settings-nav">
                <div class="settings-nav-section">Company</div>
                <button class="settings-nav-button is-active" type="button" data-settings-target="general"><i data-lucide="settings"></i>General</button>
                <div class="settings-nav-section">Lead Management</div>
                <button class="settings-nav-button" type="button" data-settings-target="integrations"><i data-lucide="plug-zap"></i>Lead Integrations</button>
            </div>
        </aside>

        <section class="settings-workspace">
            @if(session('flash_message'))
                <div class="mb-3 px-4 py-2.5 rounded border border-emerald-200 bg-emerald-50 text-xs text-emerald-800 flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                    <span>{{ session('flash_message') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-3 px-4 py-2.5 rounded border border-rose-200 bg-rose-50 text-xs text-rose-800">
                    <div class="font-bold mb-1">Please fix the following errors:</div>
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h2 class="settings-content-title" id="paneTitle">Company General Settings</h2>

            <div class="settings-panel">
                {{-- GENERAL SETTINGS PANE --}}
                <div id="company-settings-general" class="settings-pane is-active">
                    <form method="POST" action="{{ route('settings.company.general') }}">
                        @csrf
                        @method('PATCH')
                        <div class="settings-form-body">
                            <div class="setting-row">
                                <label class="setting-label">Company Name</label>
                                <p class="setting-help">This name appears in the sidebar and company switcher.</p>
                                <input class="setting-control" name="settings[company_name]" value="{{ old('settings.company_name', $settings['company_name'] ?? $company->name) }}" required>
                            </div>
                            <div class="setting-row">
                                <label class="setting-label">Timezone</label>
                                <p class="setting-help">Dates, follow-ups, and scheduled integrations use this timezone.</p>
                                <input class="setting-control" name="settings[timezone]" value="{{ old('settings.timezone', $settings['timezone'] ?? 'Asia/Kolkata') }}" placeholder="Asia/Kolkata" required>
                            </div>
                        </div>
                        <div class="settings-footer">
                            <button class="settings-save" type="submit">Save Settings</button>
                        </div>
                    </form>
                </div>

                {{-- LEAD INTEGRATIONS PANE --}}
                <div id="company-settings-integrations" class="settings-pane">
                    {{-- Provider Picker Carousel --}}
                    <div class="provider-picker">
                        <button type="button" class="provider-scroll-button" data-provider-scroll="previous" aria-label="Previous providers"><i data-lucide="chevron-left"></i></button>
                        <div class="provider-scroll" id="leadProviderPicker">
                            @foreach($providers as $providerKey => $provider)
                                <button type="button" class="provider-card {{ $loop->first ? 'is-selected' : '' }} {{ $provider['enabled'] ? '' : 'is-planned' }}" data-provider="{{ $providerKey }}" title="{{ $provider['enabled'] ? 'Available for lead syncing' : 'Integration roadmap' }}">
                                    <span class="provider-card-title">
                                        <i data-lucide="{{ $provider['icon'] }}"></i>{{ $provider['name'] }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                        <button type="button" class="provider-scroll-button" data-provider-scroll="next" aria-label="More providers"><i data-lucide="chevron-right"></i></button>
                    </div>

                    {{-- Form Header & Form --}}
                    <div class="px-5 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between" id="formHeaderBar">
                        <div>
                            <span class="text-xs font-bold text-slate-800" id="formModeTitle">Configure New Integration</span>
                            <span class="text-[11px] text-slate-500 ml-2" id="formModeSubtitle">Set up automated XML lead imports for 99acres</span>
                        </div>
                        <button type="button" class="crm-btn text-xs hidden" id="cancelEditBtn">
                            <i data-lucide="x" class="w-3.5 h-3.5 mr-1"></i> Cancel Edit
                        </button>
                    </div>

                    <form method="POST" action="{{ route('settings.company.integrations') }}" class="integration-form integration-setup" id="integrationSetupForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="integrationId" value="">
                        <input type="hidden" name="provider" id="selectedProvider" value="99acres">

                        <div class="integration-grid">
                            <div class="integration-wide">
                                <label class="setting-label">Integration Label / Name</label>
                                <input class="setting-control" style="max-width:100%" name="name" id="integrationName" placeholder="e.g. 99acres Primary Feed">
                                <p class="setting-help">Identifies this integration in notifications, sync logs, and lead source tracking.</p>
                            </div>

                            <div>
                                <label class="setting-label">Sync Frequency</label>
                                <select class="setting-control" name="sync_frequency" id="syncFrequencySelect">
                                    <option value="every_15_minutes">Every 15 minutes (Real-time polling)</option>
                                    <option value="every_30_minutes">Every 30 minutes</option>
                                    <option value="hourly">Hourly (Every hour)</option>
                                    <option value="twice_daily">Twice daily (Every 12 hours)</option>
                                    <option value="daily">Daily (Every 24 hours)</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="manual">Manual / Custom Cron only</option>
                                </select>
                                <p class="setting-help">Automatic sync runs via the shared hosting cron schedule.</p>
                            </div>

                            <div>
                                <label class="setting-label">Assign Lead Source</label>
                                <select class="setting-control" name="source_id" id="integrationSourceId">
                                    <option value="">Default (Auto-mapped to 99acres)</option>
                                    @foreach($sources as $src)
                                        <option value="{{ $src->id }}">{{ $src->name }}</option>
                                    @endforeach
                                </select>
                                <p class="setting-help">Incoming leads will be tagged with this lead source.</p>
                            </div>

                            <div class="integration-wide">
                                <label class="setting-label">API Endpoint URL</label>
                                <input class="setting-control" style="max-width:100%" name="credentials[endpoint]" id="integrationEndpoint" placeholder="https://www.99acres.com/99api/v1/getmy99/xml/fetchxmlqueries/query.xml" required>
                                <p class="setting-help">The XML API fetch query URL provided by 99acres (e.g. <code>https://www.99acres.com/99api/v1/getmy99/xml/fetchxmlqueries/query.xml</code>).</p>
                            </div>

                            <div>
                                <label class="setting-label">Username / API Key</label>
                                <input class="setting-control" name="credentials[username]" id="integrationUsername" required placeholder="99acres username or key">
                            </div>

                            <div>
                                <label class="setting-label">Password / API Secret</label>
                                <input class="setting-control" type="password" name="credentials[password]" id="integrationPassword" placeholder="Enter password (leave blank to keep current)">
                                <p class="setting-help" id="passwordHelp">Required when creating a new integration.</p>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                            <label class="text-xs text-slate-700 flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="is_active" id="integrationIsActive" value="1" checked class="rounded text-orange-600 focus:ring-orange-500">
                                <span class="font-medium">Enable this integration</span>
                            </label>
                            <button class="settings-save" type="submit" id="saveIntegrationBtn">Save Integration</button>
                        </div>
                    </form>

                    {{-- Configured Integrations List --}}
                    <div class="integration-list">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <label class="setting-label mb-0">Configured Integrations ({{ $integrations->count() }})</label>
                                <p class="setting-help mb-0">Active API connections, manual testing triggers, and sync health.</p>
                            </div>
                            @if($integrations->count() > 0)
                                <button type="button" class="crm-btn text-xs" id="btnSyncAll">
                                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 mr-1"></i> Sync All Integrations
                                </button>
                            @endif
                        </div>

                        <div id="syncAllAlert" class="hidden mb-3 p-2 text-xs rounded bg-blue-50 border border-blue-200 text-blue-800"></div>

                        @forelse($integrations as $integration)
                            @php
                                $freqLabels = [
                                    'every_15_minutes' => 'Every 15 mins',
                                    'every_30_minutes' => 'Every 30 mins',
                                    'hourly' => 'Hourly',
                                    'twice_daily' => 'Twice daily',
                                    'daily' => 'Daily',
                                    'weekly' => 'Weekly',
                                    'manual' => 'Manual / Cron',
                                ];
                                $freqLabel = $freqLabels[$integration->sync_frequency] ?? ucfirst($integration->sync_frequency);
                            @endphp
                            <div class="integration-item" id="integration-card-{{ $integration->id }}">
                                <div class="integration-item-top">
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <strong class="text-slate-800 text-sm">{{ $integration->name }}</strong>
                                            <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">{{ $integration->provider }}</span>
                                            <span class="freq-badge">{{ $freqLabel }}</span>
                                            <span class="{{ $integration->is_active ? 'status-active' : 'status-disabled' }}">
                                                {{ $integration->is_active ? 'Active' : 'Disabled' }}
                                            </span>
                                        </div>
                                        <div class="mt-1 text-slate-500 text-xs flex items-center gap-3 flex-wrap">
                                            <span><i data-lucide="clock" class="w-3 h-3 inline mr-1 text-slate-400"></i>Last Synced: <span id="lastSynced-{{ $integration->id }}">{{ $integration->last_synced_at?->diffForHumans() ?? 'Never synced' }}</span></span>
                                            @if(!empty($integration->credentials['endpoint']))
                                                <span class="text-slate-300">|</span>
                                                <span class="text-slate-400 truncate max-w-[280px]" title="{{ $integration->credentials['endpoint'] }}">{{ $integration->credentials['endpoint'] }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="integration-actions">
                                        @if($integration->is_active)
                                            <button type="button" class="integration-sync-btn" data-sync-integration="{{ $integration->id }}" title="Run an immediate test sync to verify credentials and pull latest leads">
                                                <i data-lucide="play" class="w-3.5 h-3.5"></i> Test Sync Now
                                            </button>
                                        @endif
                                        <button type="button" class="integration-edit-btn" data-edit-integration
                                            data-id="{{ $integration->id }}"
                                            data-provider="{{ $integration->provider }}"
                                            data-name="{{ $integration->name }}"
                                            data-frequency="{{ $integration->sync_frequency }}"
                                            data-source="{{ $integration->source_id ?? '' }}"
                                            data-endpoint="{{ $integration->credentials['endpoint'] ?? '' }}"
                                            data-username="{{ $integration->credentials['username'] ?? '' }}"
                                            data-active="{{ $integration->is_active ? 1 : 0 }}">
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                                        </button>
                                        <form method="POST" action="{{ route('settings.company.integrations.destroy', $integration->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this integration?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="integration-del-btn">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                @if($integration->last_error)
                                    <div class="mt-2 p-2.5 rounded bg-rose-50 border border-rose-200 text-xs text-rose-800 flex items-start gap-2" id="lastError-{{ $integration->id }}">
                                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5"></i>
                                        <div>
                                            <span class="font-bold text-rose-900">Last Sync Error:</span>
                                            <div class="mt-0.5 font-mono text-[11px] text-rose-700 break-all">{{ $integration->last_error }}</div>
                                        </div>
                                    </div>
                                @endif

                                <div class="hidden" id="syncAlert-{{ $integration->id }}"></div>
                            </div>
                        @empty
                            <div class="text-center py-6 border border-dashed border-slate-200 rounded-lg bg-slate-50">
                                <i data-lucide="plug-2" class="w-8 h-8 mx-auto text-slate-400 mb-2"></i>
                                <p class="text-xs text-slate-500 font-medium">No integrations configured yet.</p>
                                <p class="text-[11px] text-slate-400">Select a provider above and enter your credentials to enable automatic lead import.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        // Tab switching
        var controls = document.querySelectorAll('[data-settings-target]');
        var paneTitle = document.getElementById('paneTitle');
        controls.forEach(function (control) {
            control.addEventListener('click', function () {
                var target = control.getAttribute('data-settings-target');
                controls.forEach(function (item) { item.classList.toggle('is-active', item.getAttribute('data-settings-target') === target); });
                document.querySelectorAll('#company-settings-page .settings-pane').forEach(function (pane) {
                    pane.classList.toggle('is-active', pane.id === 'company-settings-' + target);
                });
                if (paneTitle) {
                    paneTitle.textContent = target === 'general' ? 'Company General Settings' : 'Lead Integrations';
                }
            });
        });

        // Provider picker
        var providerPicker = document.getElementById('leadProviderPicker');
        var providerInput = document.getElementById('selectedProvider');
        var integrationSetupForm = document.getElementById('integrationSetupForm');
        var providers = @json($providers);

        document.querySelectorAll('[data-provider-scroll]').forEach(function (button) {
            button.addEventListener('click', function () {
                providerPicker.scrollBy({ left: button.getAttribute('data-provider-scroll') === 'next' ? 220 : -220, behavior: 'smooth' });
            });
        });

        document.querySelectorAll('[data-provider]').forEach(function (card) {
            card.addEventListener('click', function () {
                var key = card.getAttribute('data-provider');
                var provider = providers[key];
                if (!provider) return;
                document.querySelectorAll('[data-provider]').forEach(function (item) { item.classList.toggle('is-selected', item === card); });
                integrationSetupForm.style.display = provider.enabled ? 'block' : 'none';
                if (provider.enabled) {
                    providerInput.value = key;
                    if (document.getElementById('formModeSubtitle')) {
                        document.getElementById('formModeSubtitle').textContent = 'Set up automated lead imports for ' + provider.name;
                    }
                }
            });
        });

        // Edit Integration handler
        var formModeTitle = document.getElementById('formModeTitle');
        var formModeSubtitle = document.getElementById('formModeSubtitle');
        var cancelEditBtn = document.getElementById('cancelEditBtn');
        var saveIntegrationBtn = document.getElementById('saveIntegrationBtn');
        var integrationIdInput = document.getElementById('integrationId');
        var integrationNameInput = document.getElementById('integrationName');
        var syncFrequencySelect = document.getElementById('syncFrequencySelect');
        var integrationSourceId = document.getElementById('integrationSourceId');
        var integrationEndpoint = document.getElementById('integrationEndpoint');
        var integrationUsername = document.getElementById('integrationUsername');
        var integrationPassword = document.getElementById('integrationPassword');
        var integrationIsActive = document.getElementById('integrationIsActive');
        var passwordHelp = document.getElementById('passwordHelp');

        function resetForm() {
            integrationIdInput.value = '';
            integrationNameInput.value = '';
            syncFrequencySelect.value = 'every_15_minutes';
            integrationSourceId.value = '';
            integrationEndpoint.value = '';
            integrationUsername.value = '';
            integrationPassword.value = '';
            integrationPassword.required = true;
            integrationIsActive.checked = true;
            formModeTitle.textContent = 'Configure New Integration';
            formModeSubtitle.textContent = 'Set up automated lead imports';
            saveIntegrationBtn.textContent = 'Save Integration';
            cancelEditBtn.classList.add('hidden');
            if (passwordHelp) passwordHelp.textContent = 'Required when creating a new integration.';
        }

        cancelEditBtn.addEventListener('click', function () {
            resetForm();
        });

        document.querySelectorAll('[data-edit-integration]').forEach(function (button) {
            button.addEventListener('click', function () {
                var id = button.dataset.id;
                var provider = button.dataset.provider;
                var name = button.dataset.name;
                var frequency = button.dataset.frequency;
                var source = button.dataset.source;
                var endpoint = button.dataset.endpoint;
                var username = button.dataset.username;
                var active = button.dataset.active === '1';

                integrationIdInput.value = id;
                providerInput.value = provider;
                integrationNameInput.value = name || '';
                syncFrequencySelect.value = frequency || 'every_15_minutes';
                integrationSourceId.value = source || '';
                integrationEndpoint.value = endpoint || '';
                integrationUsername.value = username || '';
                integrationPassword.value = '';
                integrationPassword.required = false;
                integrationIsActive.checked = active;

                formModeTitle.textContent = 'Edit Integration: ' + (name || provider);
                formModeSubtitle.textContent = 'Editing integration #' + id + ' (' + provider + ')';
                saveIntegrationBtn.textContent = 'Update Integration';
                cancelEditBtn.classList.remove('hidden');
                if (passwordHelp) passwordHelp.textContent = 'Leave password blank to keep existing stored credentials.';

                // Select corresponding provider card
                document.querySelectorAll('[data-provider]').forEach(function (card) {
                    card.classList.toggle('is-selected', card.getAttribute('data-provider') === provider);
                });

                // Scroll to form smoothly
                integrationSetupForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });

        // Test Sync handler (with live feedback)
        document.querySelectorAll('[data-sync-integration]').forEach(function (button) {
            button.addEventListener('click', function () {
                var id = button.dataset.syncIntegration;
                var alertBox = document.getElementById('syncAlert-' + id);
                var lastSyncEl = document.getElementById('lastSynced-' + id);
                var originalHtml = button.innerHTML;

                button.disabled = true;
                button.innerHTML = '<i data-lucide="loader-2" class="w-3.5 h-3.5 mr-1 inline-block"></i> Syncing...';
                if (window.lucide) window.lucide.createIcons();

                if (alertBox) {
                    alertBox.className = 'mt-2 p-2.5 text-xs rounded bg-amber-50 border border-amber-200 text-amber-800 flex items-center gap-2';
                    alertBox.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 text-amber-600 flex-shrink-0 animate-spin"></i><span>Connecting to API and checking for new leads...</span>';
                    alertBox.classList.remove('hidden');
                    if (window.lucide) window.lucide.createIcons();
                }

                fetch('{{ url('settings/company/integrations') }}/' + id + '/sync?now=1', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(function (res) {
                    return res.json().then(function (data) {
                        return { ok: res.ok, status: res.status, data: data };
                    });
                })
                .then(function (res) {
                    button.disabled = false;
                    button.innerHTML = originalHtml;
                    if (window.lucide) window.lucide.createIcons();

                    if (res.ok && res.data.ok) {
                        if (alertBox) {
                            var resData = res.data.result || {};
                            alertBox.className = 'mt-2 p-2.5 text-xs rounded bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-2';
                            alertBox.innerHTML = '<i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"></i>' +
                                '<div><div class="font-bold text-emerald-900">' + (res.data.message || 'Sync completed successfully.') + '</div>' +
                                '<div class="text-[11px] text-emerald-700 mt-0.5">Total received: ' + (resData.received !== undefined ? resData.received : 0) +
                                ' | New leads created: ' + (resData.created !== undefined ? resData.created : 0) +
                                ' | Duplicate skipped: ' + (resData.duplicates !== undefined ? resData.duplicates : 0) + '</div></div>';
                            alertBox.classList.remove('hidden');
                            if (window.lucide) window.lucide.createIcons();
                        }
                        if (lastSyncEl) {
                            lastSyncEl.textContent = res.data.last_synced_at || 'Just now';
                        }
                        var errBanner = document.getElementById('lastError-' + id);
                        if (errBanner) errBanner.style.display = 'none';
                    } else {
                        var errMsg = (res.data && res.data.message) ? res.data.message : 'HTTP ' + res.status + ' error occurred during sync.';
                        if (alertBox) {
                            alertBox.className = 'mt-2 p-2.5 text-xs rounded bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-2';
                            alertBox.innerHTML = '<i data-lucide="x-circle" class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5"></i>' +
                                '<div><div class="font-bold text-rose-900">Sync Failed</div>' +
                                '<div class="mt-0.5 font-mono text-[11px] text-rose-700 break-all">' + errMsg + '</div></div>';
                            alertBox.classList.remove('hidden');
                            if (window.lucide) window.lucide.createIcons();
                        }
                    }
                })
                .catch(function (err) {
                    button.disabled = false;
                    button.innerHTML = originalHtml;
                    if (window.lucide) window.lucide.createIcons();
                    if (alertBox) {
                        alertBox.className = 'mt-2 p-2.5 text-xs rounded bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-2';
                        alertBox.innerHTML = '<i data-lucide="x-circle" class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5"></i>' +
                            '<div><div class="font-bold text-rose-900">Network Error</div><div class="mt-0.5 text-[11px]">' + err.message + '</div></div>';
                        alertBox.classList.remove('hidden');
                        if (window.lucide) window.lucide.createIcons();
                    }
                });
            });
        });

        // Sync All button handler
        var btnSyncAll = document.getElementById('btnSyncAll');
        var syncAllAlert = document.getElementById('syncAllAlert');
        if (btnSyncAll) {
            btnSyncAll.addEventListener('click', function () {
                btnSyncAll.disabled = true;
                var origText = btnSyncAll.innerHTML;
                btnSyncAll.innerHTML = '<i data-lucide="loader-2" class="w-3.5 h-3.5 mr-1 inline-block animate-spin"></i> Triggering All...';
                if (window.lucide) window.lucide.createIcons();

                fetch('{{ route('settings.company.integrations.sync-all') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    btnSyncAll.disabled = false;
                    btnSyncAll.innerHTML = origText;
                    if (window.lucide) window.lucide.createIcons();
                    if (syncAllAlert) {
                        syncAllAlert.className = 'mb-3 p-2.5 text-xs rounded bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-2';
                        syncAllAlert.innerHTML = '<i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i><span>All active integrations have been dispatched for background syncing.</span>';
                        syncAllAlert.classList.remove('hidden');
                        if (window.lucide) window.lucide.createIcons();
                    }
                })
                .catch(function (err) {
                    btnSyncAll.disabled = false;
                    btnSyncAll.innerHTML = origText;
                    if (window.lucide) window.lucide.createIcons();
                    if (syncAllAlert) {
                        syncAllAlert.className = 'mb-3 p-2.5 text-xs rounded bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-2';
                        syncAllAlert.innerHTML = '<i data-lucide="x-circle" class="w-4 h-4 text-rose-600 flex-shrink-0"></i><span>Failed to dispatch integrations: ' + err.message + '</span>';
                        syncAllAlert.classList.remove('hidden');
                        if (window.lucide) window.lucide.createIcons();
                    }
                });
            });
        }

        // Initialize Lucide icons on page load
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }());
</script>
@endpush
