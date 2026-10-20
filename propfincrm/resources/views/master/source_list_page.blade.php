@section('page-title', 'Source Master')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')
@extends('layouts.master')

@section('styles')
<style>
    #source-master-page { min-height: calc(100vh - 40px); background: #fff; font-family: 'Outfit', sans-serif; }
    #source-master-page .master-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 20px; border-bottom: 1px solid #e2e8f0; }
    #source-master-page .master-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 34px; padding: 0 12px; border: 1px solid #dbe3ee; border-radius: 6px; background: #fff; color: #475569; font-size: 12px; font-weight: 500; text-decoration: none; cursor: pointer; transition: all .15s ease; }
    #source-master-page .master-btn:hover { border-color: #cbd5e1; background: #f8fafc; color: #1e293b; }
    #source-master-page .master-btn-primary { border-color: #f97316; background: #f97316; color: #fff; font-weight: 600; }
    #source-master-page .master-btn-primary:hover { border-color: #ea580c; background: #ea580c; color: #fff; }
    #source-master-page .dataTables_filter, #source-master-page .dataTables_length, #source-master-page .dt-search, #source-master-page .dt-length { display: none !important; }
    #source-master-page .dataTables_wrapper, #source-master-page .dataTables_scroll { width: 100% !important; margin: 0 !important; }
    #source-master-page table.dataTable { width: 100% !important; margin: 0 !important; border-collapse: collapse !important; }
    #source-master-page .dataTables_scrollHead table thead th, #source-master-page table.dataTable thead th { height: 36px; padding: 8px 16px !important; border: 0 !important; border-bottom: 1px solid #e2e8f0 !important; background: #f7f9fc !important; color: #94a3b8 !important; font-size: 10.5px !important; font-weight: 600 !important; letter-spacing: .05em; text-transform: uppercase; white-space: nowrap; vertical-align: middle; }
    #source-master-page table.dataTable tbody td { height: 44px; padding: 8px 16px !important; border: 0 !important; border-bottom: 1px solid #f1f5f9 !important; color: #334155; font-size: 12.5px; vertical-align: middle; white-space: nowrap; }
    #source-master-page table.dataTable tbody tr:hover td { background: #fafbfc; }
    #source-master-page .master-footer { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 20px; border-top: 1px solid #e2e8f0; }
    #source-master-page .dataTables_info, #source-master-page .dt-info { padding: 0 !important; color: #64748b; font-size: 12px; }
    #source-master-page .dataTables_paginate, #source-master-page .dt-paging { display: flex; align-items: center; gap: 3px; margin-left: auto; padding: 0 !important; }
    #source-master-page .dataTables_paginate .paginate_button, #source-master-page .dt-paging .dt-paging-button { box-sizing: border-box; display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 30px; margin: 0 !important; padding: 0 8px !important; border: 1px solid #dbe3ee !important; border-radius: 6px !important; background: #fff !important; color: #475569 !important; font-size: 12px; line-height: 16px; box-shadow: none !important; cursor: pointer; }
    #source-master-page .dataTables_paginate .paginate_button.current, #source-master-page .dataTables_paginate .paginate_button.current:hover, #source-master-page .dt-paging .dt-paging-button.current, #source-master-page .dt-paging .dt-paging-button.current:hover { border-color: #f97316 !important; background: #f97316 !important; color: #fff !important; -webkit-text-fill-color: #fff !important; font-weight: 600; }
    #source-master-page .dataTables_paginate .paginate_button.disabled, #source-master-page .dt-paging .dt-paging-button.disabled { opacity: .45; cursor: not-allowed; }
    #source-master-page .connection-hub { padding: 20px; background: linear-gradient(135deg, #fffaf5, #fff 55%, #f0f9ff); border-bottom: 1px solid #e2e8f0; }
    #source-master-page .provider-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:9px; margin-top:14px; }
    #source-master-page .provider-option { position:relative; min-height:86px; padding:13px; text-align:left; border:1px solid #dce4ee; border-radius:9px; background:#fff; cursor:pointer; transition:.15s ease; }
    #source-master-page .provider-option:hover { border-color:#fb923c; transform:translateY(-1px); }
    #source-master-page .provider-option.is-selected { border-color:#f97316; box-shadow:0 0 0 2px rgba(249,115,22,.14); background:#fffaf7; }
    #source-master-page .provider-option.is-planned { opacity:.55; cursor:not-allowed; }
    #source-master-page .provider-option b { display:block; color:#1e293b; font-size:12px; margin-top:8px; }
    #source-master-page .provider-option small { display:block; color:#64748b; font-size:10.5px; line-height:1.35; margin-top:3px; }
    #source-master-page .provider-status { position:absolute; top:9px; right:9px; font-size:9px; font-weight:700; color:#c2410c; text-transform:uppercase; }
    #source-master-page .connection-form { display:none; margin-top:18px; padding:18px; border:1px solid #fed7aa; border-radius:10px; background:#fff; }
    #source-master-page .connection-form.is-visible { display:block; }
    #source-master-page .connection-fields { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:13px; }
    #source-master-page .connection-field { min-width:0; }
    #source-master-page .connection-field.wide { grid-column:1/-1; }
    #source-master-page .connection-field label { display:block; margin-bottom:5px; color:#475569; font-size:11px; font-weight:600; }
    #source-master-page .connection-field input, #source-master-page .connection-field select { width:100%; height:35px; padding:0 10px; border:1px solid #cbd5e1; border-radius:6px; color:#334155; font-size:12px; outline:none; }
    #source-master-page .connection-field input:focus, #source-master-page .connection-field select:focus { border-color:#f97316; box-shadow:0 0 0 2px rgba(249,115,22,.12); }
    #source-master-page .connection-help { margin:5px 0 0; color:#64748b; font-size:10.5px; line-height:1.4; }
    #source-master-page .connection-summary { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-top:14px; padding:11px 13px; border:1px solid #e2e8f0; border-radius:8px; background:#fff; }
    #source-master-page .connection-summary b { font-size:12px; color:#334155; }
    #source-master-page .connection-summary p { margin:3px 0 0; color:#64748b; font-size:10.5px; }
    #source-master-page .callback-url { margin-top:7px; padding:7px 9px; border-radius:5px; background:#f8fafc; color:#475569; font:10px ui-monospace,monospace; overflow-wrap:anywhere; }
    #source-master-page .google-setup { margin-top:9px; border:1px solid #dbeafe; border-radius:7px; background:#f8fbff; color:#334155; font-size:11px; }
    #source-master-page .google-setup summary { padding:8px 10px; cursor:pointer; font-weight:600; }
    #source-master-page .google-setup pre { margin:0; padding:0 10px 10px; white-space:pre-wrap; overflow-wrap:anywhere; color:#475569; font:10px/1.5 ui-monospace,monospace; }
    @media (max-width: 640px) {
        #source-master-page .master-toolbar { padding: 12px 14px; flex-direction: column; align-items: stretch; }
        #source-master-page .master-footer { flex-direction: column; align-items: flex-start; }
        #source-master-page .dataTables_paginate { margin-left: 0; max-width: 100%; overflow-x: auto; }
        #source-master-page .connection-hub { padding:14px; }
        #source-master-page .connection-fields { grid-template-columns:1fr; }
        #source-master-page .connection-summary { flex-direction:column; }
    }
</style>
@endsection

@section('content')
<div id="source-master-page">
    @if(isset($msg))
        <div class="m-4 flex items-center justify-between rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-circle" class="h-4 w-4 text-amber-600"></i>
                <span>{{ $msg }}</span>
            </div>
            <button type="button" class="btn-close text-xs" onclick="this.parentElement.remove()"></button>
        </div>
    @endif

    <section class="connection-hub" aria-labelledby="lead-connections-title">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 text-orange-600"><i data-lucide="plug-zap" class="h-4 w-4"></i><span class="text-[10px] font-bold uppercase tracking-[.14em]">Lead connections</span></div>
                <h1 id="lead-connections-title" class="mt-1 mb-0 text-base font-semibold text-slate-800">Bring new leads into ElyLeads automatically</h1>
                <p class="mt-1 mb-0 max-w-2xl text-xs leading-5 text-slate-500">Choose a source, securely add its credentials, then test or activate it. Meta leads arrive instantly by verified webhook; portal APIs sync on your selected schedule.</p>
            </div>
            <a href="{{ url('settings/company') }}" class="master-btn"><i data-lucide="settings-2" class="h-3.5 w-3.5"></i> Connection settings</a>
        </div>

        <div class="provider-grid" id="lead-provider-grid">
            @foreach($providers as $key => $provider)
                <button type="button" class="provider-option {{ !$provider['enabled'] ? 'is-planned' : '' }}" data-provider="{{ $key }}" {{ !$provider['enabled'] ? 'disabled' : '' }}>
                    <i data-lucide="{{ $provider['icon'] }}" class="h-4 w-4 text-slate-500"></i>
                    <b>{{ $provider['name'] }}</b><small>{{ $provider['description'] }}</small>
                    <span class="provider-status">{{ $provider['enabled'] ? ($provider['mode'] === 'webhook' ? 'Real-time' : 'API sync') : 'Soon' }}</span>
                </button>
            @endforeach
        </div>

        <form method="POST" action="{{ route('settings.company.integrations') }}" class="connection-form" id="lead-connection-form">
            @csrf @method('PUT')
            <input type="hidden" name="provider" id="connection-provider">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div><h2 class="m-0 text-sm font-semibold text-slate-800" id="connection-title">Connect source</h2><p class="connection-help" id="connection-description"></p></div>
                <button type="button" class="master-btn" id="close-connection-form">Cancel</button>
            </div>
            <div class="connection-fields">
                <div class="connection-field wide"><label>Connection name</label><input name="name" id="connection-name" placeholder="e.g. Meta - Main Campaign"><p class="connection-help">A clear internal label for this connection.</p></div>
                <div class="connection-field"><label>Lead source</label><select name="source_id"><option value="">Use provider name</option>@foreach($source_lists as $source)<option value="{{ $source->id }}">{{ $source->source_name }}</option>@endforeach</select></div>
                <div class="connection-field" id="sync-frequency-wrap"><label>Sync frequency</label><select name="sync_frequency" id="sync-frequency"><option value="every_15_minutes">Every 15 minutes</option><option value="every_30_minutes">Every 30 minutes</option><option value="hourly">Hourly</option><option value="manual">Manual only</option></select><p class="connection-help" id="sync-frequency-help">The server checks active connections automatically.</p></div>

                <div class="connection-field meta-only"><label>Meta Page ID</label><input name="credentials[page_id]" placeholder="e.g. 1234567890"></div>
                <div class="connection-field meta-only"><label>Meta Instant Form ID <span class="text-slate-400">optional for real-time</span></label><input name="credentials[form_id]" placeholder="For historical test sync"></div>
                <div class="connection-field meta-only wide"><label>Long-lived Page access token</label><input type="password" name="credentials[access_token]" placeholder="Never shown again after saving"><p class="connection-help">Use a Page token with <code>leads_retrieval</code>. Store this only in this secure connection form.</p></div>
                <div class="connection-field meta-only"><label>Meta App secret</label><input type="password" name="credentials[app_secret]" placeholder="Used to verify Meta signatures"></div>
                <div class="connection-field meta-only"><label>Webhook verify token</label><input name="credentials[webhook_verify_token]" minlength="16" placeholder="Create a random 16+ character token"></div>
                <div class="connection-field meta-only"><label>Meta name field <span class="text-slate-400">optional</span></label><input name="configuration[meta_field_map][name]" placeholder="full_name"></div>
                <div class="connection-field meta-only"><label>Meta phone field <span class="text-slate-400">optional</span></label><input name="configuration[meta_field_map][phone]" placeholder="phone_number"></div>
                <div class="connection-field meta-only"><label>Meta email field <span class="text-slate-400">optional</span></label><input name="configuration[meta_field_map][email]" placeholder="email"></div>
                <div class="connection-field meta-only"><label>Meta project field <span class="text-slate-400">optional</span></label><input name="configuration[meta_field_map][project]" placeholder="project"></div>
                <div class="connection-field meta-only"><label>Meta location field <span class="text-slate-400">optional</span></label><input name="configuration[meta_field_map][location]" placeholder="city"></div>
                <div class="connection-field meta-only"><label>Meta requirement field <span class="text-slate-400">optional</span></label><input name="configuration[meta_field_map][requirement]" placeholder="requirement"></div>
                <div class="connection-field meta-only"><label>Meta budget field <span class="text-slate-400">optional</span></label><input name="configuration[meta_field_map][budget]" placeholder="budget"></div>

                <div class="connection-field acres-only wide"><label>99acres API endpoint</label><input type="url" name="credentials[endpoint]" placeholder="https://www.99acres.com/99api/.../query.xml"></div>
                <div class="connection-field acres-only"><label>Username / API key</label><input name="credentials[username]"></div>
                <div class="connection-field acres-only"><label>Password / API secret</label><input type="password" name="credentials[password]"></div>

                <div class="connection-field webhook-only wide"><label>Webhook signing secret <span class="text-slate-400">optional</span></label><input type="password" name="credentials[webhook_secret]" placeholder="Your sender must send this in X-ElyLeads-Signature"><p class="connection-help">Use a secret if your website or automation supports custom headers. A unique endpoint token is always generated.</p></div>
                <div class="connection-field webhook-only"><label>Lead ID JSON field</label><input name="configuration[field_map][id]" placeholder="id"></div>
                <div class="connection-field webhook-only"><label>Name JSON field</label><input name="configuration[field_map][name]" placeholder="name"></div>
                <div class="connection-field webhook-only"><label>Phone JSON field</label><input name="configuration[field_map][phone]" placeholder="phone"></div>
                <div class="connection-field webhook-only"><label>Email JSON field</label><input name="configuration[field_map][email]" placeholder="email"></div>
                <div class="connection-field webhook-only"><label>Project JSON field</label><input name="configuration[field_map][project]" placeholder="project"></div>
                <div class="connection-field webhook-only"><label>Location JSON field</label><input name="configuration[field_map][location]" placeholder="location"></div>
                <div class="connection-field webhook-only"><label>Requirement JSON field</label><input name="configuration[field_map][requirement]" placeholder="requirement"></div>
                <div class="connection-field webhook-only"><label>Budget JSON field</label><input name="configuration[field_map][budget]" placeholder="budget"></div>
            </div>
            <div class="google-forms-only hidden mt-3 rounded-lg border border-blue-100 bg-blue-50 p-3 text-[11px] leading-5 text-blue-900"><strong>Google Forms setup:</strong> save this connection first. Then copy its generated webhook endpoint into an installable Apps Script submit trigger for your form’s response spreadsheet. Map fields such as <code>answers.Full Name</code> and <code>answers.Phone</code> above.</div>
            <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3"><label class="flex items-center gap-2 text-xs text-slate-700"><input type="checkbox" name="is_active" value="1" checked> Enable this connection</label><button class="master-btn master-btn-primary" type="submit">Save & activate</button></div>
        </form>

        @foreach($integrations as $integration)
            <div class="connection-summary">
                <div><b>{{ $integration->name }}</b> <span class="ml-1 rounded bg-slate-100 px-1.5 py-0.5 text-[9px] font-bold uppercase text-slate-600">{{ $integration->provider }}</span> <span class="ml-1 text-[10px] {{ $integration->is_active ? 'text-emerald-600' : 'text-slate-400' }}">{{ $integration->is_active ? 'Active' : 'Disabled' }}</span>
                    <p>Last activity: {{ $integration->last_synced_at?->diffForHumans() ?? 'Not received yet' }}{{ $integration->last_error ? ' · Check connection error below' : '' }}</p>
                    @if($integration->provider === 'meta_lead_ads')<div class="callback-url">Meta callback: {{ route('webhooks.meta.verify', $integration->id) }}</div>@endif
                    @if(in_array($integration->provider, ['website_api', 'generic_webhook', 'google_forms'], true))<div class="callback-url">Webhook endpoint: {{ route('webhooks.leads.receive', ['integration' => $integration->id, 'token' => $integration->credentials['webhook_token'] ?? '']) }}</div>@endif
                    @if($integration->provider === 'google_forms')
                        <details class="google-setup"><summary>Google Forms Apps Script setup</summary><pre>function onFormSubmit(e) {
  const answers = Object.fromEntries(Object.entries(e.namedValues).map(([key, value]) => [key, value.join(', ')]));
  const payload = { id: e.range.getSheet().getSheetId() + '-' + e.range.getRow(), answers: answers, submitted_at: new Date().toISOString() };
  UrlFetchApp.fetch('{{ route('webhooks.leads.receive', ['integration' => $integration->id, 'token' => $integration->credentials['webhook_token'] ?? '']) }}', { method: 'post', contentType: 'application/json', payload: JSON.stringify(payload), muteHttpExceptions: true });
}</pre></details>
                    @endif
                    @if($integration->last_error)<p class="text-rose-600">{{ $integration->last_error }}</p>@endif
                </div>
                <div class="flex shrink-0 gap-2">
                    @if($integration->is_active && ($integration->provider === '99acres' || ($integration->provider === 'meta_lead_ads' && !empty($integration->credentials['form_id']))))<button type="button" class="master-btn test-connection" data-id="{{ $integration->id }}"><i data-lucide="play" class="h-3.5 w-3.5"></i> Test</button>@endif
                    <form method="POST" action="{{ route('settings.company.integrations.destroy', $integration->id) }}" onsubmit="return confirm('Remove this lead connection?')">@csrf @method('DELETE')<button class="master-btn text-rose-600" type="submit">Remove</button></form>
                </div>
            </div>
        @endforeach
    </section>

 </div>
@endsection

@push('scripts')
<script>
$(function() {
    var providers = @json($providers);
    var connectionForm = $('#lead-connection-form');
    function resetConnectionFields() {
        connectionForm.find('.meta-only, .acres-only, .webhook-only, .google-forms-only').hide();
        connectionForm.find('input, select').prop('required', false);
    }

    $('[data-provider]').on('click', function() {
        var key = $(this).data('provider');
        var provider = providers[key];
        if (!provider || !provider.enabled) return;
        $('[data-provider]').removeClass('is-selected');
        $(this).addClass('is-selected');
        resetConnectionFields();
        $('#connection-provider').val(key);
        $('#connection-name').attr('placeholder', provider.name + ' connection');
        $('#connection-title').text('Connect ' + provider.name);
        $('#connection-description').text(provider.description + '. ' + provider.requirements.join(' · '));
        $('#sync-frequency-wrap').toggle(provider.mode === 'polling');
        $('#sync-frequency').val(provider.mode === 'polling' ? 'every_15_minutes' : 'manual');
        if (key === 'meta_lead_ads') {
            connectionForm.find('.meta-only').show();
            connectionForm.find('[name="credentials[page_id]"], [name="credentials[access_token]"], [name="credentials[app_secret]"], [name="credentials[webhook_verify_token]"]').prop('required', true);
        } else if (key === '99acres') {
            connectionForm.find('.acres-only').show();
            connectionForm.find('[name="credentials[endpoint]"], [name="credentials[username]"], [name="credentials[password]"]').prop('required', true);
        } else {
            connectionForm.find('.webhook-only').show();
            if (key === 'google_forms') connectionForm.find('.google-forms-only').removeClass('hidden').show();
        }
        connectionForm.addClass('is-visible');
        if (window.lucide) window.lucide.createIcons();
        document.getElementById('lead-connection-form').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    $('#close-connection-form').on('click', function() {
        connectionForm.removeClass('is-visible');
        $('[data-provider]').removeClass('is-selected');
    });

    $('.test-connection').on('click', function() {
        var button = $(this), original = button.html(), id = button.data('id');
        button.prop('disabled', true).text('Testing…');
        $.ajax({
            url: '{{ url('settings/company/integrations') }}/' + id + '/sync', method: 'POST',
            data: { _token: '{{ csrf_token() }}', now: 1 }
        }).done(function(response) {
            alert(response.message || 'Connection test completed.');
            window.location.reload();
        }).fail(function(xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'The connection test failed. Check the saved credentials and setup instructions.');
        }).always(function() { button.prop('disabled', false).html(original); });
    });

    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>
@endpush

