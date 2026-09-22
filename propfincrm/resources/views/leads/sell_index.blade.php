@section('page-title', __('All Sell leads'))
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')
@extends('layouts.master')

@section('styles')
<style>
    #sell-leads-page { min-height: calc(100vh - 40px); background: #fff; }
    #sell-leads-page .sell-toolbar { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px; padding:14px 20px; border-bottom:1px solid #e2e8f0; }
    #sell-leads-page .sell-search { display:flex; align-items:center; width:260px; max-width:100%; height:34px; padding:0 10px; gap:8px; border:1px solid #dbe3ee; border-radius:6px; color:#64748b; background:#fff; }
    #sell-leads-page .sell-search:focus-within { border-color:#f97316; box-shadow:0 0 0 2px rgba(249,115,22,.12); }
    #sell-leads-page .sell-search input { width:100%; min-width:0; border:0; outline:0; box-shadow:none; color:#334155; font-size:12px; background:transparent; }
    #sell-leads-page .sell-action { display:inline-flex; height:34px; align-items:center; justify-content:center; gap:6px; border:1px solid #dbe3ee; border-radius:6px; padding:0 11px; color:#475569; background:#fff; font-size:12px; font-weight:500; text-decoration:none; cursor:pointer; }
    #sell-leads-page .sell-action:hover { border-color:#cbd5e1; background:#f8fafc; color:#1e293b; }
    #sell-leads-page .sell-action-primary { border-color:#f97316; background:#f97316; color:#fff; }
    #sell-leads-page .sell-action-primary:hover { border-color:#ea580c; background:#ea580c; color:#fff; }
    #sell-leads-page details > summary { list-style:none; }
    #sell-leads-page details > summary::-webkit-details-marker { display:none; }
    #sell-leads-page .sell-links-menu { position:absolute; z-index:20; right:0; top:calc(100% + 6px); width:168px; overflow:hidden; border:1px solid #e2e8f0; border-radius:7px; background:#fff; box-shadow:0 10px 22px rgba(15,23,42,.10); }
    #sell-leads-page .sell-links-menu a { display:block; padding:8px 10px; color:#475569; font-size:12px; text-decoration:none; }
    #sell-leads-page .sell-links-menu a:hover { background:#f8fafc; color:#1e293b; }
    #sell-leads-page .dataTables_wrapper { width:100%; }
    #sell-leads-page .dataTables_filter, #sell-leads-page .dataTables_length, #sell-leads-page .dt-search, #sell-leads-page .dt-length { display:none !important; }
    #sell-leads-page .dataTables_scrollHead table thead th, #sell-leads-page table.dataTable thead th { background:#f7f9fc !important; color:#94a3b8 !important; border-bottom:1px solid #e2e8f0 !important; font-size:11px !important; font-weight:500 !important; letter-spacing:.05em; text-transform:uppercase; white-space:nowrap; }
    #sell-leads-page table.dataTable tbody td { padding:8px 12px !important; color:#334155; font-size:12px; border-bottom:1px solid #f1f5f9 !important; white-space:nowrap; vertical-align:middle; }
    #sell-leads-page table.dataTable tbody tr:hover td { background:#fafbfc; }
    #sell-leads-page .sell-table-footer { padding:10px 16px; border-top:1px solid #e2e8f0; }
    #sell-leads-page .sell-footer-content { display:flex; align-items:center; justify-content:space-between; gap:12px; }
    #sell-leads-page .dataTables_info, #sell-leads-page .dt-info { padding:0 !important; color:#64748b; font-size:12px; }
    #sell-leads-page .dataTables_paginate, #sell-leads-page .dt-paging { display:flex; align-items:center; gap:3px; padding:0 !important; margin-left:auto; white-space:nowrap; }
    #sell-leads-page .dataTables_paginate .paginate_button, #sell-leads-page .dt-paging .dt-paging-button { box-sizing:border-box; display:inline-flex; align-items:center; justify-content:center; min-width:30px; height:30px; margin:0 !important; padding:6px 9px !important; border:1px solid #dbe3ee !important; border-radius:6px !important; color:#475569 !important; font-size:12px; line-height:16px; background:#fff !important; box-shadow:none !important; }
    #sell-leads-page .dataTables_paginate .paginate_button.current, #sell-leads-page .dataTables_paginate .paginate_button.current:hover, #sell-leads-page .dt-paging .dt-paging-button.current, #sell-leads-page .dt-paging .dt-paging-button.current:hover { border-color:#f97316 !important; background:#f97316 !important; color:#fff !important; -webkit-text-fill-color:#fff !important; }
    #sell-leads-page .dataTables_paginate .paginate_button.disabled, #sell-leads-page .dt-paging .dt-paging-button.disabled { opacity:.48; cursor:not-allowed; }
    #sell-leads-page .dt-paging .ellipsis { display:inline-flex; align-items:center; height:30px; padding:0 4px; color:#94a3b8; font-size:12px; }
    @media (max-width:640px) { #sell-leads-page .sell-toolbar { padding:12px; } #sell-leads-page .sell-search { width:100%; } #sell-leads-page .sell-footer-content { align-items:flex-start; flex-direction:column; } #sell-leads-page .dataTables_paginate, #sell-leads-page .dt-paging { margin-left:0; overflow-x:auto; max-width:100%; } }
</style>
@endsection

@section('content')
<div id="sell-leads-page">
    <div class="sell-toolbar">
        <label class="sell-search" for="sell-table-search"><i data-lucide="search" class="h-4 w-4 shrink-0"></i><input id="sell-table-search" type="search" placeholder="Search sell leads" autocomplete="off"></label>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" id="sell-table-refresh" class="sell-action" title="Refresh data"><i data-lucide="refresh-cw" class="h-3.5 w-3.5"></i> Refresh</button>
            <details class="relative"><summary class="sell-action"><i data-lucide="link" class="h-3.5 w-3.5"></i> More links <i data-lucide="chevron-down" class="h-3.5 w-3.5"></i></summary><div class="sell-links-menu"><a href="{{ url('lead_list') }}">All Leads</a><a href="{{ url('lead_list/ReverseLead') }}">Reverse Leads</a><a href="{{ url('lead_list/DumpLead') }}">Dump Leads</a></div></details>
            <a href="{{ url('new_sell_page') }}" class="sell-action sell-action-primary"><i data-lucide="plus" class="h-3.5 w-3.5"></i> New sell lead</a>
        </div>
    </div>
    <div class="w-full overflow-hidden">
        <table id="leads-table" class="w-full border-collapse">
            <thead><tr><th>{{ __('Name') }}</th><th>{{ __('Mobile') }}</th><th>{{ __('Email') }}</th><th>{{ __('Project') }}</th><th>{{ __('Property Type') }}</th><th>{{ __('Booking Price') }}</th><th>{{ __('Bedrooms') }}</th><th>{{ __('Date') }}</th></tr></thead>
        </table>
    </div>
</div>
@stop

@section('js')
<script>
$(function () {
    var sellTable = $('#leads-table').DataTable({
        processing: true, serverSide: true, autoWidth: false, scrollX: true, scrollCollapse: true,
        pageLength: window.app_tables_pagination_limit || 10, pagingType: 'full_numbers', order: [],
        dom: '<"w-full overflow-x-auto"t><"sell-table-footer"<"sell-footer-content"ip>>',
        ajax: { url: '{{ url('get_lead_data') }}', type: 'POST', headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } },
        columns: [
            { data:'name', name:'name' }, { data:'Contact_number', name:'Contact_number' }, { data:'email', name:'email' }, { data:'project', name:'project' },
            { data:'property_type', name:'property_type' }, { data:'booking_price', name:'booking_price' }, { data:'bedrooms', name:'bedrooms' }, { data:'created_at', name:'created_at' }
        ],
        language: { emptyTable:'No sell leads found', info:'Showing _START_ to _END_ of _TOTAL_ entries', infoEmpty:'Showing 0 to 0 of 0 entries', processing:'<span class="text-slate-500 text-xs">Loading...</span>', paginate:{ next:'Next', previous:'Previous' } },
        drawCallback: function () { this.api().columns.adjust(); }
    });
    $('#sell-table-search').on('input', function () { sellTable.search(this.value).draw(); });
    $('#sell-table-refresh').on('click', function () { sellTable.ajax.reload(null, false); });
});
</script>
@endsection
