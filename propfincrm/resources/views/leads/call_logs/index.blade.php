@section('page-title', 'User Call Logs')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')
@extends('layouts.master')

@section('styles')
<style>
    #call-logs-page { min-height:calc(100vh - 40px); background:#fff; }
    #call-logs-page .call-toolbar { display:flex; flex-wrap:wrap; align-items:center; justify-content:flex-start; gap:12px; padding:14px 20px; border-bottom:1px solid #e2e8f0; }
    #call-logs-page .dataTables_filter, #call-logs-page .dataTables_length, #call-logs-page .dt-search, #call-logs-page .dt-length { display:none !important; }
    #call-logs-page .dataTables_scrollHead table thead th, #call-logs-page table.dataTable thead th { padding:8px 12px !important; border-bottom:1px solid #e2e8f0 !important; color:#94a3b8 !important; background:#f7f9fc !important; font-size:11px !important; font-weight:500 !important; letter-spacing:.05em; text-transform:uppercase; white-space:nowrap; }
    #call-logs-page table.dataTable tbody td { padding:8px 12px !important; border-bottom:1px solid #f1f5f9 !important; color:#334155; font-size:12px; white-space:nowrap; vertical-align:middle; }
    #call-logs-page table.dataTable tbody tr:hover td { background:#fafbfc; }
    #call-logs-page .call-table-footer { padding:10px 16px; border-top:1px solid #e2e8f0; }
    #call-logs-page .call-footer-content { display:flex; align-items:center; justify-content:space-between; gap:12px; width:100%; }
    #call-logs-page .dataTables_info, #call-logs-page .dt-info { padding:0 !important; color:#64748b; font-size:12px; }
    #call-logs-page .dataTables_paginate, #call-logs-page .dt-paging { display:flex; align-items:center; gap:3px; margin-left:auto; padding:0 !important; white-space:nowrap; }
    #call-logs-page .dataTables_paginate .paginate_button, #call-logs-page .dt-paging .dt-paging-button { display:inline-flex; align-items:center; justify-content:center; min-width:30px; height:30px; margin:0 !important; padding:6px 9px !important; border:1px solid #dbe3ee !important; border-radius:6px !important; color:#475569 !important; background:#fff !important; font-size:12px; line-height:16px; box-shadow:none !important; }
    #call-logs-page .dataTables_paginate .paginate_button.current, #call-logs-page .dataTables_paginate .paginate_button.current:hover, #call-logs-page .dt-paging .dt-paging-button.current, #call-logs-page .dt-paging .dt-paging-button.current:hover { border-color:#f97316 !important; background:#f97316 !important; color:#fff !important; -webkit-text-fill-color:#fff !important; }
    #call-filter-offcanvas { width:min(400px,100vw); }
    #call-filter-offcanvas .offcanvas-header { padding:16px 18px; border-bottom:1px solid #e2e8f0; }
    #call-filter-offcanvas .offcanvas-body { padding:18px; background:#f8fafc; }
    #call-filter-offcanvas label { display:block; margin-bottom:5px; color:#475569; font-size:12px; font-weight:500; }
    #call-filter-offcanvas .form-control { height:36px; border-color:#cbd5e1; border-radius:6px; color:#334155; font-size:12px; box-shadow:none; }
    #call-filter-offcanvas .select2-container { width:100% !important; }
    #call-filter-offcanvas .select2-selection { height:36px !important; min-height:36px !important; border-color:#cbd5e1 !important; border-radius:6px !important; }
    #call-filter-offcanvas .select2-selection__rendered { line-height:34px !important; padding-left:10px !important; color:#334155 !important; font-size:12px !important; }
    #call-filter-offcanvas .select2-selection__arrow { height:34px !important; }
    #call-filter-offcanvas .call-apply { border:0; border-radius:6px; background:#f97316; color:#fff; font-size:12px; font-weight:600; }
    @media(max-width:640px) { #call-logs-page .call-toolbar { padding:12px; } #call-logs-page .call-footer-content { flex-direction:column; align-items:flex-start; } #call-logs-page .dataTables_paginate, #call-logs-page .dt-paging { margin-left:0; overflow-x:auto; max-width:100%; } }
</style>
@endsection

@section('content')
<div id="call-logs-page">
    <div class="call-toolbar">
        <div class="flex items-center gap-2">
            <div class="relative w-64"><i data-lucide="search" class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400"></i><input id="call-log-search" type="search" placeholder="Search" class="w-full rounded-lg border border-slate-200 bg-slate-50/70 py-1.5 pl-8 pr-3 text-xs text-slate-700 transition hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-orange-500"></div>
            <button type="button" id="call-filter-toggle" data-bs-toggle="offcanvas" data-bs-target="#call-filter-offcanvas" class="relative inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800" title="Open Filters"><i data-lucide="filter" class="h-3.5 w-3.5"></i><span id="call-filter-dot" class="hidden absolute -right-1 -top-1 h-2 w-2 rounded-full bg-orange-500 ring-2 ring-white"></span></button>
            <button type="button" id="call-refresh" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800" title="Refresh Table"><i data-lucide="refresh-cw" id="call-refresh-icon" class="h-3.5 w-3.5"></i></button>
        </div>
    </div>
    <div class="w-full overflow-hidden"><table id="callLogsTable" class="w-full border-collapse"><thead><tr><th>SN.</th><th>Call Start Time</th><th>Call End Time</th><th>Duration</th><th>Created At</th><th>Lead Name</th><th>Contact No</th><th>Call From User</th></tr></thead><tbody></tbody></table></div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="call-filter-offcanvas"><div class="offcanvas-header"><h5 class="mb-0 text-sm font-semibold text-slate-800">Filter call logs</h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button></div><div class="offcanvas-body">
    <div class="mb-3"><label for="userId">User</label><select id="userId"><option value="">All users</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>@endforeach</select></div>
    <div class="mb-3"><label for="date">From date</label><input type="date" id="date" class="form-control" value="{{ date('Y-m-01') }}"></div>
    <div class="mb-4"><label for="end_date">To date</label><input type="date" id="end_date" class="form-control" value="{{ date('Y-m-d') }}"></div>
    <div class="flex justify-end gap-2"><button type="button" id="call-filter-reset" class="inline-flex h-9 items-center justify-center rounded-md border border-slate-200 bg-white px-3 text-xs font-medium text-slate-600">Reset</button><button type="button" id="filterBtn" class="call-apply px-3 py-2">Apply filters</button></div>
</div></div>
@stop

@push('scripts')
<script>
$(function () {
    if (window.initAppSelects) window.initAppSelects(document);
    var defaults={user:'',from:'{{ date('Y-m-01') }}',to:'{{ date('Y-m-d') }}'}, filters=$.extend({},defaults);
    function updateFilterState(){var active=filters.user!==defaults.user||filters.from!==defaults.from||filters.to!==defaults.to;$('#call-filter-dot').toggleClass('hidden',!active);$('#call-filter-toggle').toggleClass('bg-orange-50 border-orange-300 text-orange-600',active).attr('title',active?'Filters active':'Open Filters');}
    var callTable=$('#callLogsTable').DataTable({processing:true,serverSide:true,autoWidth:false,scrollX:true,scrollCollapse:true,pageLength:window.app_tables_pagination_limit||10,pagingType:'full_numbers',order:[[1,'desc']],dom:'<"w-full overflow-x-auto"t><"call-table-footer"<"call-footer-content"ip>>',ajax:{url:'{{ url('call_logs') }}',type:'POST',headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},data:function(d){d.user=filters.user;d.from=filters.from;d.to=filters.to;}},columns:[{data:null,orderable:false,searchable:false,render:function(data,type,row,meta){return meta.settings._iDisplayStart+meta.row+1;}},{data:'call_start_datetime',name:'call_start_datetime'},{data:'call_end_datetime',name:'call_end_datetime'},{data:'duration',name:'duration'},{data:'created_at',name:'created_at'},{data:'lead_name',name:'leads.name'},{data:'contact_no',name:'leads.contact_no'},{data:'call_from_user',name:'users.name'}],language:{emptyTable:'No call logs found',info:'Showing _START_ to _END_ of _TOTAL_ entries',infoEmpty:'Showing 0 to 0 of 0 entries',processing:'<span class="text-xs text-slate-500">Loading...</span>',paginate:{previous:'Previous',next:'Next'}},drawCallback:function(){this.api().columns.adjust();}});
    $('#call-log-search').on('input',function(){callTable.search(this.value).draw();});
    $('#call-refresh').on('click',function(){var icon=$('#call-refresh-icon');icon.addClass('animate-spin');callTable.ajax.reload(function(){setTimeout(function(){icon.removeClass('animate-spin');},350);},false);});
    $('#filterBtn').on('click',function(){filters={user:$('#userId').val(),from:$('#date').val(),to:$('#end_date').val()};updateFilterState();callTable.ajax.reload(null,true);bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('call-filter-offcanvas')).hide();});
    $('#call-filter-reset').on('click',function(){filters=$.extend({},defaults);$('#userId').val('').trigger('change');['date','end_date'].forEach(function(id){var input=document.getElementById(id);if(input._flatpickr)input._flatpickr.setDate(filters[id==='date'?'from':'to'],true);else input.value=filters[id==='date'?'from':'to'];});updateFilterState();callTable.ajax.reload(null,true);});
    updateFilterState();
});
</script>
@endpush
