@section('page-title', 'Invoices')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')
@extends('layouts.master')

@section('styles')
<style>
#invoices-page { min-height: calc(100vh - 40px); font-family: 'Outfit', sans-serif; }
#invoices-page .dataTables_filter, #invoices-page .dataTables_length,
#invoices-page .dt-search, #invoices-page .dt-length { display: none !important; }
#invoices-page .dataTables_wrapper, #invoices-page .dataTables_scroll { width: 100% !important; }
#invoices-page table.dataTable { width: 100% !important; margin: 0 !important; border-collapse: collapse !important; }
#invoices-page table.dataTable thead th { height: 32px; padding: 5px 14px !important; background: #f7f9fc !important; color: #94a3b8 !important; border-top: 0 !important; border-bottom: 1px solid #e2e8f0 !important; font-size: 10px !important; font-weight: 600 !important; letter-spacing: .05em; text-transform: uppercase; vertical-align: middle !important; white-space: nowrap; }
#invoices-page table.dataTable tbody td { height: 34px; padding: 5px 14px !important; color: #334155; font-size: 12px; border-bottom: 1px solid #f1f5f9 !important; border-top: none !important; vertical-align: middle; }
#invoices-page table.dataTable tbody tr:hover td { background: #fafbfc; }
#invoices-page .dataTables_info, #invoices-page .dt-info { padding: 0 !important; color: #64748b !important; font-size: 12px !important; }
#invoices-page .dataTables_paginate, #invoices-page .dt-paging { display: flex !important; align-items: center; gap: 3px; margin: 0 !important; padding: 0 !important; float: none !important; }
#invoices-page .dataTables_paginate .paginate_button, #invoices-page .dt-paging .dt-paging-button { display: inline-flex !important; align-items: center !important; justify-content: center !important; min-width: 30px; height: 30px; margin: 0 1px !important; padding: 0 9px !important; border: 1px solid #e2e8f0 !important; border-radius: 6px !important; background: #fff !important; color: #475569 !important; font-size: 12px !important; text-decoration: none !important; }
#invoices-page .dataTables_paginate .paginate_button.current, #invoices-page .dt-paging .dt-paging-button.current { border-color: #f97316 !important; background: #f97316 !important; color: #fff !important; font-weight: 600; }
</style>
@endsection

@section('content')
<div id="invoices-page">
    <div class="crm-toolbar">
        <div class="crm-toolbar-left">
            <div class="crm-search-wrap">
                <i class="fa fa-search"></i>
                <input id="invoices-search" class="crm-search" type="search" placeholder="Search invoices">
            </div>
        </div>
        <div class="crm-toolbar-right">
        </div>
    </div>

    <table id="invoices-table" class="w-full">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Client</th>
                <th>Hours</th>
                <th>Total Amount</th>
                <th>Sent</th>
                <th>Payment</th>
                <th>Created At</th>
                <th class="no-sort text-end" style="width:50px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
                @php
                    $client = $invoice->clients->first();
                    $totalHours = 0;
                    $totalAmount = 0;
                    foreach($invoice->tasktime as $t) {
                        $totalHours += $t->time;
                        $totalAmount += ($t->time * $t->value);
                    }
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="font-semibold text-slate-800 hover:text-orange-600 no-underline">
                            #{{ $invoice->id }}
                        </a>
                    </td>
                    <td>
                        @if($client)
                            <a href="{{ route('clients.show', $client->id) }}" class="text-slate-700 hover:text-orange-600 no-underline font-medium">
                                {{ $client->name }}
                            </a>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td>{{ $totalHours }} hrs</td>
                    <td>{{ number_format($totalAmount, 2) }},-</td>
                    <td>
                        @if($invoice->sent)
                            <span class="crm-badge crm-badge-green">Sent</span>
                        @else
                            <span class="crm-badge crm-badge-slate">Unsent</span>
                        @endif
                    </td>
                    <td>
                        @if($invoice->received)
                            <span class="crm-badge crm-badge-green">Received</span>
                        @else
                            <span class="crm-badge crm-badge-red">Pending</span>
                        @endif
                    </td>
                    <td>{{ date('d-m-Y', strtotime($invoice->created_at)) }}</td>
                    <td>
                        <div class="crm-row-actions">
                            <div class="dropdown">
                                <button type="button" class="crm-ellipsis-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-ellipsis"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end crm-action-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('invoices.show', $invoice->id) }}">
                                            <i class="fa fa-eye"></i> View Invoice
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-slate-400" style="padding:24px!important;">
                        No invoices found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    var table = $('#invoices-table').DataTable({
        pagingType: 'full_numbers',
        order: [[0, 'desc']],
        autoWidth: false,
        columnDefs: [{ orderable: false, targets: 7 }],
        dom: '<"w-full overflow-x-auto"t><"crm-dt-footer"<"crm-dt-footer-inner"ip>>',
        language: {
            emptyTable:  'No invoices found.',
            info:        'Showing _START_ to _END_ of _TOTAL_ invoices',
            infoEmpty:   'Showing 0 to 0 of 0 invoices',
            paginate:    { previous: 'Previous', next: 'Next' }
        }
    });

    $('#invoices-search').on('input', function () {
        table.search(this.value).draw();
    });
});
</script>
@endpush
