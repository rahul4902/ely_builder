<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 text-slate-800 text-sm font-semibold">{{ __('All Leads') }}</h5>
    <a href="{{ route('leads.create', ['client' => $client->id]) }}" class="crm-btn crm-btn-primary">
        <i class="fas fa-plus"></i> {{ __('New Lead') }}
    </a>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Assigned user') }}</th>
                <th>{{ __('Contact Date') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($client->leads as $lead)
                <tr>
                    <td>
                        <a href="{{ route('leads.show', $lead->id) }}" class="fw-semibold text-dark text-decoration-none">
                            {{ $lead->title }}
                        </a>
                    </td>
                    <td>
                        @if($lead->user)
                            <a href="{{ route('users.show', $lead->user->id) }}" class="text-secondary text-decoration-none">
                                <i class="fas fa-user-circle me-1"></i>{{ $lead->user->name }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        {{ $lead->contact_date ? date('d, M Y, H:i', strtotime($lead->contact_date)) : '—' }}
                        @if($lead->status == 1 && $lead->days_until_contact)
                            <small class="text-muted">({{ $lead->days_until_contact }})</small>
                        @endif
                    </td>
                    <td>
                        @if ($lead->status == 1)
                            <span class="crm-badge crm-badge-orange">{{ __('Open') }}</span>
                        @else
                            <span class="crm-badge crm-badge-green">{{ __('Completed') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">
                        {{ __('No leads found for this client.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>