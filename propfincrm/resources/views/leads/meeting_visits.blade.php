@section('page-title', 'Meetings & Visits')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')
@extends('layouts.master')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <style>
        #meeting-list-page { min-height: calc(100vh - 40px); background: #fff; }
        #meeting-list-page .meeting-toolbar { display:flex; flex-wrap:wrap; align-items:center; justify-content:flex-start; gap:12px; padding:14px 20px; border-bottom:1px solid #e2e8f0; }
        #meeting-list-page .meeting-search { display:flex; align-items:center; width:260px; max-width:100%; height:34px; gap:8px; padding:0 10px; border:1px solid #dbe3ee; border-radius:6px; color:#64748b; background:#fff; }
        #meeting-list-page .meeting-search:focus-within { border-color:#f97316; box-shadow:0 0 0 2px rgba(249,115,22,.12); }
        #meeting-list-page .meeting-search input { width:100%; min-width:0; border:0; outline:0; box-shadow:none; color:#334155; font-size:12px; background:transparent; }
        #meeting-list-page .meeting-action { display:inline-flex; align-items:center; justify-content:center; gap:6px; height:34px; padding:0 11px; border:1px solid #dbe3ee; border-radius:6px; color:#475569; background:#fff; font-size:12px; font-weight:500; cursor:pointer; }
        #meeting-list-page .meeting-action:hover { border-color:#cbd5e1; color:#1e293b; background:#f8fafc; }
        #meeting-list-page .meeting-filter-dot { position:absolute; top:5px; right:5px; width:6px; height:6px; border-radius:999px; background:#f97316; }
        #meeting-list-page .dataTables_filter, #meeting-list-page .dataTables_length, #meeting-list-page .dt-search, #meeting-list-page .dt-length { display:none !important; }
        #meeting-list-page .dataTables_scrollHead table thead th, #meeting-list-page table.dataTable thead th { padding:8px 12px !important; border-bottom:1px solid #e2e8f0 !important; color:#94a3b8 !important; background:#f7f9fc !important; font-size:11px !important; font-weight:500 !important; letter-spacing:.05em; text-transform:uppercase; white-space:nowrap; }
        #meeting-list-page table.dataTable tbody td { padding:8px 12px !important; border-bottom:1px solid #f1f5f9 !important; color:#334155; font-size:12px; line-height:1.35; white-space:nowrap; vertical-align:middle; }
        #meeting-list-page table.dataTable tbody tr:hover td { background:#fafbfc; }
        #meeting-list-page .meeting-table-footer { padding:10px 16px; border-top:1px solid #e2e8f0; }
        #meeting-list-page .meeting-footer-content { display:flex; align-items:center; justify-content:space-between; gap:12px; width:100%; }
        #meeting-list-page .dataTables_info, #meeting-list-page .dt-info { padding:0 !important; color:#64748b; font-size:12px; }
        #meeting-list-page .dataTables_paginate, #meeting-list-page .dt-paging { display:flex; align-items:center; gap:3px; margin-left:auto; padding:0 !important; white-space:nowrap; }
        #meeting-list-page .dataTables_paginate .paginate_button, #meeting-list-page .dt-paging .dt-paging-button { display:inline-flex; align-items:center; justify-content:center; min-width:30px; height:30px; margin:0 !important; padding:6px 9px !important; border:1px solid #dbe3ee !important; border-radius:6px !important; color:#475569 !important; background:#fff !important; font-size:12px; line-height:16px; box-shadow:none !important; }
        #meeting-list-page .dataTables_paginate .paginate_button.current, #meeting-list-page .dataTables_paginate .paginate_button.current:hover, #meeting-list-page .dt-paging .dt-paging-button.current, #meeting-list-page .dt-paging .dt-paging-button.current:hover { border-color:#f97316 !important; background:#f97316 !important; color:#fff !important; -webkit-text-fill-color:#fff !important; }
        #meeting-list-page .dataTables_paginate .paginate_button.disabled, #meeting-list-page .dt-paging .dt-paging-button.disabled { opacity:.45; cursor:not-allowed; }
        #meeting-filter-offcanvas { width:min(400px, 100vw); }
        #meeting-filter-offcanvas .offcanvas-header { padding:16px 18px; border-bottom:1px solid #e2e8f0; }
        #meeting-filter-offcanvas .offcanvas-body { padding:18px; background:#f8fafc; }
        #meeting-filter-offcanvas label { display:block; margin-bottom:5px; color:#475569; font-size:12px; font-weight:500; }
        #meeting-filter-offcanvas .form-control, #meeting-filter-offcanvas .form-select { height:36px; border-color:#cbd5e1; border-radius:6px; color:#334155; font-size:12px; box-shadow:none; }
        #meeting-filter-offcanvas .form-control:focus, #meeting-filter-offcanvas .form-select:focus { border-color:#f97316; box-shadow:0 0 0 2px rgba(249,115,22,.12); }
        #meeting-filter-offcanvas .apply-filter { border:0; border-radius:6px; background:#f97316; font-size:12px; font-weight:600; }
        @media (max-width:640px) { #meeting-list-page .meeting-toolbar { padding:12px; } #meeting-list-page .meeting-search { width:100%; } #meeting-list-page .meeting-footer-content { flex-direction:column; align-items:flex-start; } #meeting-list-page .dataTables_paginate, #meeting-list-page .dt-paging { margin-left:0; max-width:100%; overflow-x:auto; } }
    </style>
@endsection

@section('content')
    <div id="meeting-list-page">
        <div class="meeting-toolbar">
            <div class="flex items-center gap-2">
                <div class="relative w-64">
                    <i data-lucide="search" class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400"></i>
                    <input id="meeting-table-search" type="search" placeholder="Search" autocomplete="off" class="w-full rounded-lg border border-slate-200 bg-slate-50/70 py-1.5 pl-8 pr-3 text-xs text-slate-700 transition hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                </div>
                <button type="button" id="meeting-filter-toggle" data-bs-toggle="offcanvas" data-bs-target="#meeting-filter-offcanvas" aria-controls="meeting-filter-offcanvas" class="relative inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800" title="Open Filters"><i data-lucide="filter" class="h-3.5 w-3.5"></i><span id="meeting-filter-indicator" class="hidden absolute -right-1 -top-1 h-2 w-2 rounded-full bg-orange-500 ring-2 ring-white"></span></button>
                <button type="button" id="meeting-table-refresh" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800" title="Refresh Table"><i data-lucide="refresh-cw" class="h-3.5 w-3.5" id="meeting-refresh-icon"></i></button>
            </div>
        </div>
        <div class="w-full overflow-hidden">
            <table id="leadsTable" class="w-full border-collapse">
                <thead><tr>
                    <th>Updated Date</th><th>Name</th><th>Contact No</th><th>Next Follow-up</th><th>Last Comment</th><th>Location</th><th>Project</th><th>Source</th><th>Requirement</th><th>User Name</th><th>Team Leader</th><th>Meeting Date</th><th>Status</th><th>Lead Type</th><th>Action Date</th>
                </tr></thead>
            </table>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="meeting-filter-offcanvas" aria-labelledby="meeting-filter-title">
        <div class="offcanvas-header"><h5 class="mb-0 text-sm font-semibold text-slate-800" id="meeting-filter-title">Filter meetings & visits</h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button></div>
        <div class="offcanvas-body">
            <div class="mb-3"><label for="meeting_type">Status</label><select id="meeting_type" class="form-select"><option value="meeting">Meeting Done</option><option value="visit">Visit Done</option></select></div>
            <div class="mb-3"><label for="date">From date</label><input type="text" id="date" class="form-control meeting-date" value="{{ date('Y-m-d') }}" autocomplete="off"></div>
            <div class="mb-4"><label for="end_date">To date</label><input type="text" id="end_date" class="form-control meeting-date" value="{{ date('Y-m-d') }}" autocomplete="off"></div>
            <div class="flex justify-end gap-2"><button type="button" id="meeting-filter-reset" class="meeting-action">Reset</button><button type="button" id="filterBtn" class="apply-filter px-3 py-2 text-white">Apply filters</button></div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13"></script>
    <script>
        $(function () {
            $('.meeting-date').flatpickr({ altInput:true, altFormat:'d-m-Y', dateFormat:'Y-m-d', allowInput:true });
            var filters = { type:'meeting', from:'{{ date('Y-m-d') }}', to:'{{ date('Y-m-d') }}' };
            function updateIndicator() { var active = filters.type !== 'meeting' || filters.from !== '{{ date('Y-m-d') }}' || filters.to !== '{{ date('Y-m-d') }}'; $('#meeting-filter-indicator').toggleClass('hidden', !active); $('#meeting-filter-toggle').toggleClass('bg-orange-50 border-orange-300 text-orange-600', active).attr('title', active ? 'Filters active' : 'Open Filters'); }
            var meetingTable = $('#leadsTable').DataTable({
                processing:true, serverSide:true, autoWidth:false, scrollX:true, scrollCollapse:true, pageLength:window.app_tables_pagination_limit || 10, pagingType:'full_numbers', order:[],
                dom:'<"w-full overflow-x-auto"t><"meeting-table-footer"<"meeting-footer-content"ip>>',
                ajax:{ url:'{{ url('all_meeting_and_visits') }}', type:'POST', headers:{ 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, data:function (request) { request.type=filters.type; request.from=filters.from; request.to=filters.to; } },
                columns:[
                    {data:'updated_at',name:'updated_at'}, {data:'name',name:'name'}, {data:'contact_no',name:'contact_no'}, {data:'nextfollowup',name:'nextfollowup',searchable:false}, {data:'last_comment',name:'last_comment'}, {data:'location',name:'location'}, {data:'project',name:'project'}, {data:'source',name:'source'}, {data:'requirement',name:'requirement'}, {data:'user_name',name:'user_name',searchable:false}, {data:'team_leader',name:'team_leader'}, {data:'meetingdate',name:'meetingdate',searchable:false}, {data:'status',name:'status'}, {data:'lead_type',name:'lead_type'}, {data:'action_date',name:'action_date'}
                ],
                language:{ emptyTable:'No meetings or visits found', info:'Showing _START_ to _END_ of _TOTAL_ entries', infoEmpty:'Showing 0 to 0 of 0 entries', processing:'<span class="text-xs text-slate-500">Loading...</span>', paginate:{next:'Next',previous:'Previous'} },
                drawCallback:function () { this.api().columns.adjust(); }
            });
            $('#meeting-table-search').on('input', function () { meetingTable.search(this.value).draw(); });
            $('#meeting-table-refresh').on('click', function () { var icon = $('#meeting-refresh-icon'); icon.addClass('animate-spin'); meetingTable.ajax.reload(function () { setTimeout(function () { icon.removeClass('animate-spin'); }, 350); }, false); });
            $('#filterBtn').on('click', function () { filters.type=$('#meeting_type').val(); filters.from=$('#date').val(); filters.to=$('#end_date').val(); updateIndicator(); meetingTable.ajax.reload(null, true); bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('meeting-filter-offcanvas')).hide(); });
            $('#meeting-filter-reset').on('click', function () { filters={type:'meeting',from:'{{ date('Y-m-d') }}',to:'{{ date('Y-m-d') }}'}; $('#meeting_type').val(filters.type); $('.meeting-date').each(function () { this._flatpickr.setDate(filters.from, true); }); updateIndicator(); meetingTable.ajax.reload(null, true); });
            updateIndicator();
        });
    </script>
@endpush
