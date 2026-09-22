@section('page-title', 'Scheduler List')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')
@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<style>
    #scheduler-page { min-height:calc(100vh - 40px); background:#fff; }
    #scheduler-page .scheduler-toolbar { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px; padding:14px 20px; border-bottom:1px solid #e2e8f0; }
    #scheduler-page .scheduler-action { display:inline-flex; align-items:center; justify-content:center; gap:7px; height:34px; padding:0 11px; border:1px solid #dbe3ee; border-radius:6px; color:#475569; background:#fff; font-size:12px; font-weight:500; text-decoration:none; cursor:pointer; }
    #scheduler-page .scheduler-action:hover { border-color:#cbd5e1; color:#1e293b; background:#f8fafc; }
    #scheduler-page .scheduler-action-primary { border-color:#f97316; background:#f97316; color:#fff; }
    #scheduler-page .scheduler-action-primary:hover { border-color:#ea580c; background:#ea580c; color:#fff; }
    #scheduler-page .dataTables_filter, #scheduler-page .dataTables_length, #scheduler-page .dt-search, #scheduler-page .dt-length { display:none !important; }
    #scheduler-page .dataTables_scrollHead table thead th, #scheduler-page table.dataTable thead th { padding:8px 12px !important; border-bottom:1px solid #e2e8f0 !important; color:#94a3b8 !important; background:#f7f9fc !important; font-size:11px !important; font-weight:500 !important; letter-spacing:.05em; text-transform:uppercase; white-space:nowrap; }
    #scheduler-page table.dataTable tbody td { padding:8px 12px !important; border-bottom:1px solid #f1f5f9 !important; color:#334155; font-size:12px; white-space:nowrap; vertical-align:middle; }
    #scheduler-page table.dataTable tbody tr:hover td { background:#fafbfc; }
    #scheduler-page .scheduler-row-actions { display:flex; align-items:center; gap:6px; }
    #scheduler-page .scheduler-row-link, #scheduler-page .scheduler-row-delete { display:inline-flex; align-items:center; justify-content:center; height:28px; padding:0 9px; border:1px solid #dbe3ee; border-radius:5px; color:#475569; background:#fff; font-size:11px; font-weight:500; text-decoration:none; cursor:pointer; }
    #scheduler-page .scheduler-row-link:hover { border-color:#cbd5e1; background:#f8fafc; color:#1e293b; }
    #scheduler-page .scheduler-row-delete { border-color:#fecaca; color:#dc2626; }
    #scheduler-page .scheduler-row-delete:hover { background:#fef2f2; }
    #scheduler-page .scheduler-table-footer { padding:10px 16px; border-top:1px solid #e2e8f0; }
    #scheduler-page .scheduler-footer-content { display:flex; align-items:center; justify-content:space-between; gap:12px; width:100%; }
    #scheduler-page .dataTables_info, #scheduler-page .dt-info { padding:0 !important; color:#64748b; font-size:12px; }
    #scheduler-page .dataTables_paginate, #scheduler-page .dt-paging { display:flex; align-items:center; gap:3px; margin-left:auto; padding:0 !important; white-space:nowrap; }
    #scheduler-page .dataTables_paginate .paginate_button, #scheduler-page .dt-paging .dt-paging-button { display:inline-flex; align-items:center; justify-content:center; min-width:30px; height:30px; margin:0 !important; padding:6px 9px !important; border:1px solid #dbe3ee !important; border-radius:6px !important; color:#475569 !important; background:#fff !important; font-size:12px; line-height:16px; box-shadow:none !important; }
    #scheduler-page .dataTables_paginate .paginate_button.current, #scheduler-page .dataTables_paginate .paginate_button.current:hover, #scheduler-page .dt-paging .dt-paging-button.current, #scheduler-page .dt-paging .dt-paging-button.current:hover { border-color:#f97316 !important; background:#f97316 !important; color:#fff !important; -webkit-text-fill-color:#fff !important; }
    #scheduler-filter-offcanvas { width:min(400px,100vw); }
    #scheduler-filter-offcanvas .offcanvas-header { padding:16px 18px; border-bottom:1px solid #e2e8f0; }
    #scheduler-filter-offcanvas .offcanvas-body { padding:18px; background:#f8fafc; }
    #scheduler-filter-offcanvas label { display:block; margin-bottom:5px; color:#475569; font-size:12px; font-weight:500; }
    #scheduler-filter-offcanvas .form-control { height:36px; border-color:#cbd5e1; border-radius:6px; color:#334155; font-size:12px; box-shadow:none; }
    #scheduler-filter-offcanvas .select2-container { width:100% !important; }
    #scheduler-filter-offcanvas .select2-selection { height:36px !important; min-height:36px !important; border-color:#cbd5e1 !important; border-radius:6px !important; }
    #scheduler-filter-offcanvas .select2-selection__rendered { line-height:34px !important; padding-left:10px !important; color:#334155 !important; font-size:12px !important; }
    #scheduler-filter-offcanvas .select2-selection__arrow { height:34px !important; }
    #scheduler-filter-offcanvas .scheduler-apply { border:0; border-radius:6px; background:#f97316; color:#fff; font-size:12px; font-weight:600; }
    #schedulerToast { z-index:9999; }
    @media (max-width:640px) { #scheduler-page .scheduler-toolbar { padding:12px; } #scheduler-page .scheduler-footer-content { flex-direction:column; align-items:flex-start; } #scheduler-page .dataTables_paginate, #scheduler-page .dt-paging { margin-left:0; max-width:100%; overflow-x:auto; } }
</style>
@endsection

@section('content')
<div id="schedulerToast" class="toast position-fixed end-0 top-0 m-3 border-0" role="alert"><div class="d-flex"><div class="toast-body" id="toastMessage"></div><button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button></div></div>
<div id="scheduler-page">
    <div class="scheduler-toolbar">
        <div class="flex items-center gap-2">
            <div class="relative w-64"><i data-lucide="search" class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400"></i><input id="scheduler-search" type="search" placeholder="Search" class="w-full rounded-lg border border-slate-200 bg-slate-50/70 py-1.5 pl-8 pr-3 text-xs text-slate-700 transition hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-orange-500"></div>
            <button type="button" id="scheduler-filter-toggle" data-bs-toggle="offcanvas" data-bs-target="#scheduler-filter-offcanvas" class="relative inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800" title="Open Filters"><i data-lucide="filter" class="h-3.5 w-3.5"></i><span id="scheduler-filter-dot" class="hidden absolute -right-1 -top-1 h-2 w-2 rounded-full bg-orange-500 ring-2 ring-white"></span></button>
            <button type="button" id="scheduler-refresh" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800" title="Refresh Table"><i data-lucide="refresh-cw" id="scheduler-refresh-icon" class="h-3.5 w-3.5"></i></button>
        </div>
        <a href="{{ route('scheduler.create') }}" class="scheduler-action scheduler-action-primary"><i data-lucide="plus" class="h-3.5 w-3.5"></i> New Scheduler</a>
    </div>
    <div class="w-full overflow-hidden"><table id="schedulerTable" class="w-full border-collapse"><thead><tr><th>From Date</th><th>To Date</th><th>Start Time</th><th>End Time</th><th>Assigned Projects</th><th>Assigned Users</th><th>Total Users</th><th>Created At</th><th>Action</th></tr></thead></table></div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="scheduler-filter-offcanvas"><div class="offcanvas-header"><h5 class="mb-0 text-sm font-semibold text-slate-800">Filter schedulers</h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button></div><div class="offcanvas-body">
    <div class="mb-3"><label for="filter_from_date">From date</label><input type="text" id="filter_from_date" class="form-control scheduler-date" autocomplete="off"></div>
    <div class="mb-3"><label for="filter_to_date">To date</label><input type="text" id="filter_to_date" class="form-control scheduler-date" autocomplete="off"></div>
    <div class="mb-3"><label for="filter_user_id">Assign user</label><select id="filter_user_id"><option value="">All users</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
    <div class="mb-4"><label for="filter_project">Project</label><select id="filter_project"><option value="">All projects</option>@foreach($projects as $project)<option value="{{ $project->project_name }}">{{ $project->project_name }}</option>@endforeach</select></div>
    <div class="flex justify-end gap-2"><button type="button" id="scheduler-filter-reset" class="scheduler-action">Reset</button><button type="button" id="filterBtn" class="scheduler-apply px-3 py-2">Apply filters</button></div>
</div></div>
@stop

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13"></script>
<script>
$(function () {
    $('.scheduler-date').flatpickr({ altInput:true, altFormat:'d-m-Y', dateFormat:'Y-m-d', allowInput:true });
    function setupSelects() { if (typeof $.fn.select2 !== 'function') return false; $('#filter_user_id,#filter_project').each(function () { if (!$(this).hasClass('select2-hidden-accessible')) $(this).select2({theme:'bootstrap-5',width:'100%',minimumResultsForSearch:0,placeholder:'Search and select'}); }); return true; }
    if (!setupSelects()) { var tries=0, waiter=setInterval(function(){ if(setupSelects() || ++tries>=20) clearInterval(waiter); },250); }
    var filters={from:'',to:'',user:'',project:''};
    function setFilterState() { var active=Boolean(filters.from||filters.to||filters.user||filters.project); $('#scheduler-filter-dot').toggleClass('hidden',!active); $('#scheduler-filter-toggle').toggleClass('bg-orange-50 border-orange-300 text-orange-600',active).attr('title',active?'Filters active':'Open Filters'); }
    var schedulerTable=$('#schedulerTable').DataTable({ processing:true,serverSide:true,autoWidth:false,scrollX:true,scrollCollapse:true,pageLength:window.app_tables_pagination_limit||10,pagingType:'full_numbers',order:[[7,'desc']],dom:'<"w-full overflow-x-auto"t><"scheduler-table-footer"<"scheduler-footer-content"ip>>',ajax:{url:base_url+'/scheduler/list/ajax',type:'POST',headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},data:function(d){d.filter_from_date=filters.from;d.filter_to_date=filters.to;d.filter_user_id=filters.user;d.filter_project=filters.project;}},columns:[{data:'from_date',name:'from_date'},{data:'to_date',name:'to_date'},{data:'start_time',name:'start_time'},{data:'end_time',name:'end_time'},{data:'projects_names',name:'projects_names',orderable:false,searchable:false},{data:'user_names',name:'user_names',orderable:false,searchable:false},{data:'total_users',name:'total_users',orderable:false,searchable:false},{data:'created_at',name:'created_at'},{data:'action',name:'action',orderable:false,searchable:false}],language:{emptyTable:'No schedulers found',info:'Showing _START_ to _END_ of _TOTAL_ entries',infoEmpty:'Showing 0 to 0 of 0 entries',processing:'<span class="text-xs text-slate-500">Loading...</span>',paginate:{previous:'Previous',next:'Next'}},drawCallback:function(){this.api().columns.adjust();} });
    $('#scheduler-search').on('input',function(){schedulerTable.search(this.value).draw();});
    $('#scheduler-refresh').on('click',function(){var icon=$('#scheduler-refresh-icon');icon.addClass('animate-spin');schedulerTable.ajax.reload(function(){setTimeout(function(){icon.removeClass('animate-spin');},350);},false);});
    $('#filterBtn').on('click',function(){filters={from:$('#filter_from_date').val(),to:$('#filter_to_date').val(),user:$('#filter_user_id').val(),project:$('#filter_project').val()};setFilterState();schedulerTable.ajax.reload(null,true);bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('scheduler-filter-offcanvas')).hide();});
    $('#scheduler-filter-reset').on('click',function(){filters={from:'',to:'',user:'',project:''};$('.scheduler-date').each(function(){this._flatpickr.clear();});$('#filter_user_id,#filter_project').val('').trigger('change');setFilterState();schedulerTable.ajax.reload(null,true);});
    $(document).on('click','.deleteScheduler',function(e){e.preventDefault();if(!confirm('Are you sure you want to delete this scheduler?'))return;var button=$(this),url=button.data('url');$.ajax({url:url,method:'POST',data:{_method:'DELETE',_token:$('meta[name="csrf-token"]').attr('content')},success:function(res){if(res.status===200){schedulerTable.draw();showToast(res.message,'success');}else showToast(res.message||'Something went wrong.','error');},error:function(xhr){showToast('Server error: '+xhr.status,'error');}});});
    function showToast(message,type){var toast=$('#schedulerToast');toast.removeClass('bg-success bg-danger text-white').addClass(type==='success'?'bg-success text-white':'bg-danger text-white');$('#toastMessage').text(message);new bootstrap.Toast(toast[0],{delay:3000}).show();}
    setFilterState();
});
</script>
@endpush
