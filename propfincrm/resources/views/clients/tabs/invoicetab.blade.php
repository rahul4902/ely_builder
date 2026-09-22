<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 text-slate-800 text-sm font-semibold">{{ __('All Invoices') }}</h5>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>{{ __('Invoice #') }}</th>
                <th>{{ __('Hours') }}</th>
                <th>{{ __('Total Amount') }}</th>
                <th>{{ __('Sent') }}</th>
                <th>{{ __('Payment Received') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
                <tr>
                    <td>
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="fw-semibold text-dark text-decoration-none">
                            #{{ $invoice->id }}
                        </a>
                    </td>
                    <td>
                        @php
                            $totalHours = 0;
                            foreach($invoice->tasktime as $t) { $totalHours += $t->time; }
                        @endphp
                        {{ $totalHours }} hrs
                    </td>
                    <td>
                        @php
                            $totalAmount = 0;
                            foreach($invoice->tasktime as $p) { $totalAmount += $p->value; }
                        @endphp
                        {{ number_format($totalAmount, 2) }},-
                    </td>
                    <td>
                        @if($invoice->sent)
                            <span class="crm-badge crm-badge-green">{{ __('Yes') }}</span>
                        @else
                            <span class="crm-badge crm-badge-slate">{{ __('No') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($invoice->received)
                            <span class="crm-badge crm-badge-green">{{ __('Received') }}</span>
                        @else
                            <span class="crm-badge crm-badge-red">{{ __('Pending') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        {{ __('No invoices found for this client.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>