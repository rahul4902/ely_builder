@extends('layouts.master')

@section('page-title', 'Attendance')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')

@section('styles')
<style>
    #attendance-page { min-height:calc(100vh - 40px); background:#fff; font-family:'Outfit',sans-serif; }
    #attendance-page .attendance-toolbar { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px; min-height:52px; padding:9px 20px; border-bottom:1px solid #e8edf3; }
    #attendance-page .attendance-filter { display:flex; align-items:center; gap:8px; }
    #attendance-page .attendance-month { height:32px; border:1px solid #dbe3ee; border-radius:6px; padding:0 9px; color:#475569; background:#fff; font-size:12px; box-shadow:none; }
    #attendance-page .attendance-action { display:inline-flex; align-items:center; justify-content:center; gap:6px; height:32px; padding:0 11px; border:1px solid #f97316; border-radius:6px; background:#f97316; color:#fff; font-size:12px; font-weight:600; cursor:pointer; }
    #attendance-page .attendance-action:hover { border-color:#ea580c; background:#ea580c; }
    #attendance-page .dataTables_filter, #attendance-page .dataTables_length, #attendance-page .dt-search, #attendance-page .dt-length { display:none !important; }
    #attendance-page .dataTables_wrapper, #attendance-page .dataTables_scroll { width:100% !important; margin:0 !important; }
    #attendance-page .dataTables_scrollHead { overflow:hidden !important; border-bottom:1px solid #e2e8f0; }
    #attendance-page table.dataTable { width:100% !important; margin:0 !important; border-collapse:collapse !important; }
    #attendance-page .dataTables_scrollHead table thead th, #attendance-page table.dataTable thead th { height:36px; padding:8px 12px !important; border:0 !important; border-bottom:1px solid #e2e8f0 !important; background:#f7f9fc !important; color:#94a3b8 !important; font-size:10px !important; font-weight:600 !important; letter-spacing:.05em; text-transform:uppercase; white-space:nowrap; vertical-align:middle; }
    #attendance-page table.dataTable tbody td { height:40px; padding:8px 12px !important; border:0 !important; border-bottom:1px solid #f1f5f9 !important; color:#334155; font-size:12px; vertical-align:middle; white-space:nowrap; }
    #attendance-page table.dataTable tbody tr:hover td { background:#fafbfc; }
    #attendance-page .attendance-footer { display:flex; align-items:center; justify-content:space-between; gap:12px; min-height:48px; padding:8px 16px; border-top:1px solid #e2e8f0; }
    #attendance-page .dataTables_info, #attendance-page .dt-info { padding:0 !important; color:#64748b; font-size:12px; }
    #attendance-page .dataTables_paginate, #attendance-page .dt-paging { display:flex; align-items:center; gap:3px; margin-left:auto; padding:0 !important; }
    #attendance-page .dataTables_paginate .paginate_button, #attendance-page .dt-paging .dt-paging-button { display:inline-flex; align-items:center; justify-content:center; min-width:28px; height:28px; margin:0 !important; padding:5px 8px !important; border:1px solid #dbe3ee !important; border-radius:6px !important; background:#fff !important; color:#475569 !important; font-size:12px; line-height:16px; box-shadow:none !important; }
    #attendance-page .dataTables_paginate .paginate_button.current, #attendance-page .dataTables_paginate .paginate_button.current:hover, #attendance-page .dt-paging .dt-paging-button.current, #attendance-page .dt-paging .dt-paging-button.current:hover { border-color:#f97316 !important; background:#f97316 !important; color:#fff !important; -webkit-text-fill-color:#fff !important; }
    @media (max-width:640px) { #attendance-page .attendance-toolbar { padding:10px 12px; } #attendance-page .attendance-footer { align-items:flex-start; flex-direction:column; } #attendance-page .dataTables_paginate, #attendance-page .dt-paging { margin-left:0; max-width:100%; overflow-x:auto; } }
</style>
@endsection

@section('content')
<div id="attendance-page">
    <div class="attendance-toolbar">
        <div class="attendance-filter">
            <label for="month" class="sr-only">Month</label>
            <input type="month" id="month" class="attendance-month" value="{{ date('Y-m') }}" aria-label="Attendance month">
            <button type="button" id="filterBtn" class="attendance-action"><i data-lucide="search" class="h-3.5 w-3.5"></i>Apply</button>
        </div>
    </div>
    <div class="w-full overflow-x-auto">
        <table id="attendanceTable" class="w-full"><thead><tr>
            <th>S.No.</th><th>Check in date</th><th>Check in time</th><th>Check in latitude</th><th>Check in longitude</th><th>Check in remark</th><th>Check in address</th><th>Check out latitude</th><th>Check out longitude</th><th>Check out address</th><th>Check out remark</th><th>Check out date</th><th>Check out time</th><th>Work number</th><th>User name</th>
        </tr></thead></table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    var attendanceTable = $('#attendanceTable').DataTable({
        processing: true, serverSide: true, scrollX: true, autoWidth: false, pageLength: 10, order: [[1, 'desc']], pagingType: 'full_numbers',
        dom: '<"w-full overflow-x-auto"t><"attendance-footer"ip>',
        ajax: { url: '{{ url('attendance') }}', type: 'POST', headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, data: function (data) { data.month = $('#month').val(); } },
        columns: [
            { data: null, orderable: false, searchable: false, render: function (_data, _type, _row, meta) { return meta.settings._iDisplayStart + meta.row + 1; } },
            { data: 'check_in_date', name: 'check_in_date' }, { data: 'check_in_time', name: 'check_in_time' }, { data: 'check_in_lat', name: 'check_in_lat' }, { data: 'check_in_lng', name: 'check_in_lng' }, { data: 'check_in_rem', name: 'check_in_rem' }, { data: 'check_in_address', name: 'check_in_address' }, { data: 'check_out_lat', name: 'check_out_lat' }, { data: 'check_out_lng', name: 'check_out_lng' }, { data: 'check_out_address', name: 'check_out_address' }, { data: 'check_out_rem', name: 'check_out_rem' }, { data: 'check_out_date', name: 'check_out_date' }, { data: 'check_out_time', name: 'check_out_time' }, { data: 'work_number', name: 'users.work_number' }, { data: 'name', name: 'users.name' }
        ],
        language: { emptyTable: 'No attendance records found', info: 'Showing _START_ to _END_ of _TOTAL_ entries', infoEmpty: 'Showing 0 to 0 of 0 entries', paginate: { previous: 'Previous', next: 'Next' } }
    });
    $('#filterBtn').on('click', function () { attendanceTable.draw(); });
    $('#month').on('change', function () { attendanceTable.draw(); });
});
</script>
@endpush
