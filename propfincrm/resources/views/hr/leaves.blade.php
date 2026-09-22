@section('page-title', 'Leaves')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')
@extends('layouts.master')

@section('styles')
<style>
    #leaves-page { min-height: calc(100vh - 40px); background: #fff; font-family: 'Outfit', sans-serif; }
    #leaves-page .leaves-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 20px; border-bottom: 1px solid #e2e8f0; }
    #leaves-page .ui-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 34px; padding: 0 12px; border: 1px solid #dbe3ee; border-radius: 6px; background: #fff; color: #475569; font-size: 12px; font-weight: 500; text-decoration: none; cursor: pointer; transition: all .15s ease; }
    #leaves-page .ui-btn:hover { border-color: #cbd5e1; background: #f8fafc; color: #1e293b; }
    #leaves-page .ui-btn.primary { border-color: #f97316; background: #f97316; color: #fff; font-weight: 600; }
    #leaves-page .ui-btn.primary:hover { border-color: #ea580c; background: #ea580c; }
    #leaves-page .dataTables_filter, #leaves-page .dataTables_length, #leaves-page .dt-search, #leaves-page .dt-length { display: none !important; }
    #leaves-page .dataTables_wrapper, #leaves-page .dataTables_scroll { width: 100% !important; margin: 0 !important; }
    #leaves-page table.dataTable { width: 100% !important; margin: 0 !important; border-collapse: collapse !important; }
    #leaves-page .dataTables_scrollHead table thead th, #leaves-page table.dataTable thead th { height: 36px; padding: 8px 16px !important; border: 0 !important; border-bottom: 1px solid #e2e8f0 !important; background: #f7f9fc !important; color: #94a3b8 !important; font-size: 10.5px !important; font-weight: 600 !important; letter-spacing: .05em; text-transform: uppercase; white-space: nowrap; vertical-align: middle; }
    #leaves-page table.dataTable tbody td { height: 44px; padding: 8px 16px !important; border: 0 !important; border-bottom: 1px solid #f1f5f9 !important; color: #334155; font-size: 12.5px; vertical-align: middle; white-space: nowrap; }
    #leaves-page table.dataTable tbody tr:hover td { background: #fafbfc; }
    #leaves-page .leaves-footer { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 20px; border-top: 1px solid #e2e8f0; }
    #leaves-page .dataTables_info, #leaves-page .dt-info { padding: 0 !important; color: #64748b; font-size: 12px; }
    #leaves-page .dataTables_paginate, #leaves-page .dt-paging { display: flex; align-items: center; gap: 3px; margin-left: auto; padding: 0 !important; }
    #leaves-page .dataTables_paginate .paginate_button, #leaves-page .dt-paging .dt-paging-button { box-sizing: border-box; display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 30px; margin: 0 !important; padding: 0 8px !important; border: 1px solid #dbe3ee !important; border-radius: 6px !important; background: #fff !important; color: #475569 !important; font-size: 12px; line-height: 16px; box-shadow: none !important; cursor: pointer; }
    #leaves-page .dataTables_paginate .paginate_button.current, #leaves-page .dataTables_paginate .paginate_button.current:hover, #leaves-page .dt-paging .dt-paging-button.current, #leaves-page .dt-paging .dt-paging-button.current:hover { border-color: #f97316 !important; background: #f97316 !important; color: #fff !important; -webkit-text-fill-color: #fff !important; font-weight: 600; }
    #leaves-page .dataTables_paginate .paginate_button.disabled, #leaves-page .dt-paging .dt-paging-button.disabled { opacity: .45; cursor: not-allowed; }
    #leaves-page .status-pill { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 9999px; font-size: 11.5px; font-weight: 500; line-height: 1.2; }
    #leaves-page .status-pill.pending { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
    #leaves-page .status-pill.approved { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
    #leaves-page .status-pill.rejected { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
    #leaves-page .status-dot { width: 6px; height: 6px; border-radius: 9999px; }
    #leaves-page .status-dot.pending { background: #f59e0b; }
    #leaves-page .status-dot.approved { background: #10b981; }
    #leaves-page .status-dot.rejected { background: #ef4444; }
    @media (max-width: 640px) {
        #leaves-page .leaves-toolbar { padding: 12px 14px; flex-direction: column; align-items: stretch; }
        #leaves-page .leaves-footer { flex-direction: column; align-items: flex-start; }
        #leaves-page .dataTables_paginate { margin-left: 0; max-width: 100%; overflow-x: auto; }
    }
</style>
@endsection

@section('content')
<div id="leaves-page">
    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 gap-3 border-b border-slate-200 bg-slate-50/60 p-4 sm:grid-cols-4 sm:px-6">
        <div class="rounded-lg border border-slate-200/80 bg-white p-3 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium uppercase tracking-wider text-slate-400">Total Requests</span>
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-slate-100 text-slate-600"><i data-lucide="calendar" class="h-3.5 w-3.5"></i></span>
            </div>
            <div class="mt-1 text-lg font-bold text-slate-800" id="stat-total">5</div>
        </div>
        <div class="rounded-lg border border-amber-200/80 bg-white p-3 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium uppercase tracking-wider text-amber-700">Pending</span>
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-amber-50 text-amber-600"><i data-lucide="clock" class="h-3.5 w-3.5"></i></span>
            </div>
            <div class="mt-1 text-lg font-bold text-amber-600" id="stat-pending">2</div>
        </div>
        <div class="rounded-lg border border-emerald-200/80 bg-white p-3 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium uppercase tracking-wider text-emerald-700">Approved</span>
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-emerald-50 text-emerald-600"><i data-lucide="check-circle-2" class="h-3.5 w-3.5"></i></span>
            </div>
            <div class="mt-1 text-lg font-bold text-emerald-600" id="stat-approved">2</div>
        </div>
        <div class="rounded-lg border border-rose-200/80 bg-white p-3 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium uppercase tracking-wider text-rose-700">Rejected</span>
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-rose-50 text-rose-600"><i data-lucide="x-circle" class="h-3.5 w-3.5"></i></span>
            </div>
            <div class="mt-1 text-lg font-bold text-rose-600" id="stat-rejected">1</div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="leaves-toolbar">
        <div class="flex items-center gap-2">
            <div class="relative w-64 max-w-full">
                <i data-lucide="search" class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400"></i>
                <input id="leaves-search" type="search" placeholder="Search leaves..." autocomplete="off" class="w-full rounded-lg border border-slate-200 bg-slate-50/70 py-1.5 pl-8 pr-3 text-xs text-slate-700 transition hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>
            <select id="status-filter" class="h-[34px] rounded-lg border border-slate-200 bg-white px-2.5 text-xs text-slate-600 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                <option value="">All Statuses</option>
                <option value="Pending">Pending</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
            </select>
            <button type="button" id="leaves-refresh" class="inline-flex h-[34px] w-[34px] items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800" title="Refresh Table">
                <i data-lucide="refresh-cw" id="leaves-refresh-icon" class="h-3.5 w-3.5"></i>
            </button>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="ui-btn primary" data-bs-toggle="modal" data-bs-target="#requestLeaveModal">
                <i data-lucide="plus" class="h-3.5 w-3.5"></i> Request Leave
            </button>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="w-full overflow-x-auto">
        <table id="taTable" class="w-full">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Total Days</th>
                    <th>Status</th>
                    <th class="no-sort text-end" style="width: 70px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr data-row-id="1">
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">JD</div>
                            <div>
                                <span class="font-medium text-slate-800">John Doe</span>
                                <span class="block text-[11px] text-slate-400">john@example.com</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">Annual Leave</span>
                    </td>
                    <td>2024-11-01</td>
                    <td>2024-11-05</td>
                    <td><span class="font-medium text-slate-700">5 days</span></td>
                    <td class="status-cell">
                        <span class="status-pill pending">
                            <span class="status-dot pending"></span>
                            <span class="status-text">Pending</span>
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="dropdown text-end">
                            <button type="button" class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition border-0 bg-transparent cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                <i class="fa-solid fa-ellipsis text-sm"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 140px;">
                                <li>
                                    <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-emerald-600 hover:bg-emerald-50 action-approve" data-id="1">
                                        <i class="fa-solid fa-check me-2 text-emerald-500"></i> Approve
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-rose-600 hover:bg-rose-50 action-reject" data-id="1">
                                        <i class="fa-solid fa-xmark me-2 text-rose-500"></i> Reject
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr data-row-id="2">
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">JS</div>
                            <div>
                                <span class="font-medium text-slate-800">Jane Smith</span>
                                <span class="block text-[11px] text-slate-400">jane@example.com</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="inline-flex items-center rounded-md bg-purple-50 px-2 py-0.5 text-xs font-medium text-purple-700">Sick Leave</span>
                    </td>
                    <td>2024-10-15</td>
                    <td>2024-10-18</td>
                    <td><span class="font-medium text-slate-700">4 days</span></td>
                    <td class="status-cell">
                        <span class="status-pill approved">
                            <span class="status-dot approved"></span>
                            <span class="status-text">Approved</span>
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="dropdown text-end">
                            <button type="button" class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition border-0 bg-transparent cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                <i class="fa-solid fa-ellipsis text-sm"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 140px;">
                                <li>
                                    <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-rose-600 hover:bg-rose-50 action-reject" data-id="2">
                                        <i class="fa-solid fa-xmark me-2 text-rose-500"></i> Reject
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr data-row-id="3">
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">MJ</div>
                            <div>
                                <span class="font-medium text-slate-800">Mike Johnson</span>
                                <span class="block text-[11px] text-slate-400">mike@example.com</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">Casual Leave</span>
                    </td>
                    <td>2024-10-22</td>
                    <td>2024-10-23</td>
                    <td><span class="font-medium text-slate-700">2 days</span></td>
                    <td class="status-cell">
                        <span class="status-pill pending">
                            <span class="status-dot pending"></span>
                            <span class="status-text">Pending</span>
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="dropdown text-end">
                            <button type="button" class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition border-0 bg-transparent cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                <i class="fa-solid fa-ellipsis text-sm"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 140px;">
                                <li>
                                    <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-emerald-600 hover:bg-emerald-50 action-approve" data-id="3">
                                        <i class="fa-solid fa-check me-2 text-emerald-500"></i> Approve
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-rose-600 hover:bg-rose-50 action-reject" data-id="3">
                                        <i class="fa-solid fa-xmark me-2 text-rose-500"></i> Reject
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr data-row-id="4">
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">ED</div>
                            <div>
                                <span class="font-medium text-slate-800">Emily Davis</span>
                                <span class="block text-[11px] text-slate-400">emily@example.com</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">Annual Leave</span>
                    </td>
                    <td>2024-09-10</td>
                    <td>2024-09-15</td>
                    <td><span class="font-medium text-slate-700">6 days</span></td>
                    <td class="status-cell">
                        <span class="status-pill approved">
                            <span class="status-dot approved"></span>
                            <span class="status-text">Approved</span>
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="dropdown text-end">
                            <button type="button" class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition border-0 bg-transparent cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                <i class="fa-solid fa-ellipsis text-sm"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 140px;">
                                <li>
                                    <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-rose-600 hover:bg-rose-50 action-reject" data-id="4">
                                        <i class="fa-solid fa-xmark me-2 text-rose-500"></i> Reject
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr data-row-id="5">
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">RB</div>
                            <div>
                                <span class="font-medium text-slate-800">Robert Brown</span>
                                <span class="block text-[11px] text-slate-400">robert@example.com</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="inline-flex items-center rounded-md bg-purple-50 px-2 py-0.5 text-xs font-medium text-purple-700">Sick Leave</span>
                    </td>
                    <td>2024-08-01</td>
                    <td>2024-08-03</td>
                    <td><span class="font-medium text-slate-700">3 days</span></td>
                    <td class="status-cell">
                        <span class="status-pill rejected">
                            <span class="status-dot rejected"></span>
                            <span class="status-text">Rejected</span>
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="dropdown text-end">
                            <button type="button" class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition border-0 bg-transparent cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                <i class="fa-solid fa-ellipsis text-sm"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 140px;">
                                <li>
                                    <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-emerald-600 hover:bg-emerald-50 action-approve" data-id="5">
                                        <i class="fa-solid fa-check me-2 text-emerald-500"></i> Approve
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Request Leave Modal using shared <x-form.input> components --}}
<div class="modal fade" id="requestLeaveModal" tabindex="-1" aria-labelledby="requestLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-xl">
            <div class="modal-header border-b border-slate-200 px-5 py-4">
                <h5 class="modal-title text-sm font-semibold text-slate-800" id="requestLeaveModalLabel">Request Leave</h5>
                <button type="button" class="btn-close text-xs" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="requestLeaveForm">
                <div class="modal-body p-5 space-y-3">
                    <x-form.input
                        layout="standard"
                        label="Employee Name *"
                        name="employee_name"
                        id="leave_employee_name"
                        type="text"
                        placeholder="e.g. Alex Morgan"
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                        required
                    />

                    <x-form.input
                        layout="standard"
                        label="Leave Type *"
                        name="leave_type"
                        id="leave_type"
                        type="select"
                        :options="[
                            'Annual Leave' => 'Annual Leave',
                            'Sick Leave' => 'Sick Leave',
                            'Casual Leave' => 'Casual Leave',
                            'Maternity / Paternity Leave' => 'Maternity / Paternity Leave',
                            'Unpaid Leave' => 'Unpaid Leave'
                        ]"
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                        required
                    />

                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input
                            layout="standard"
                            label="Start Date *"
                            name="start_date"
                            id="leave_start_date"
                            type="date"
                            class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                            required
                        />

                        <x-form.input
                            layout="standard"
                            label="End Date *"
                            name="end_date"
                            id="leave_end_date"
                            type="date"
                            class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                            required
                        />
                    </div>

                    <x-form.input
                        layout="standard"
                        label="Reason for Leave"
                        name="reason"
                        id="leave_reason"
                        type="textarea"
                        placeholder="Provide details about your leave request..."
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                        rows="3"
                    />
                </div>
                <div class="modal-footer border-t border-slate-200 px-5 py-3">
                    <button type="button" class="ui-btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="ui-btn primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable matching ElyLeads UI convention
    var table = $('#taTable').DataTable({
        pagingType: 'full_numbers',
        order: [],
        pageLength: 10,
        autoWidth: false,
        columnDefs: [
            { targets: 'no-sort', orderable: false, searchable: false }
        ],
        dom: '<"w-full overflow-x-auto"t><"leaves-footer"<"leaves-footer-inner"ip>>',
        language: {
            emptyTable: 'No leave requests found',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 to 0 of 0 entries',
            paginate: { previous: 'Previous', next: 'Next' }
        }
    });

    // Custom toolbar search
    $('#leaves-search').on('input', function() {
        table.search(this.value).draw();
    });

    // Status filter dropdown
    $('#status-filter').on('change', function() {
        var status = $(this).val();
        table.column(5).search(status ? '^' + status + '$' : '', true, false).draw();
    });

    // Refresh button
    $('#leaves-refresh').on('click', function() {
        var icon = $('#leaves-refresh-icon');
        icon.addClass('animate-spin');
        $('#leaves-search').val('');
        $('#status-filter').val('');
        table.search('').columns().search('').draw();
        setTimeout(function() {
            icon.removeClass('animate-spin');
        }, 300);
    });

    // Update Stats counters helper
    function updateStats() {
        var total = $('#taTable tbody tr').length;
        var pending = $('#taTable tbody .status-text:contains("Pending")').length;
        var approved = $('#taTable tbody .status-text:contains("Approved")').length;
        var rejected = $('#taTable tbody .status-text:contains("Rejected")').length;
        $('#stat-total').text(total);
        $('#stat-pending').text(pending);
        $('#stat-approved').text(approved);
        $('#stat-rejected').text(rejected);
    }

    // Approve leave action handler
    $(document).on('click', '.action-approve', function(e) {
        e.preventDefault();
        var row = $(this).closest('tr');
        var cell = row.find('.status-cell');
        cell.html(`
            <span class="status-pill approved">
                <span class="status-dot approved"></span>
                <span class="status-text">Approved</span>
            </span>
        `);
        var dropdownMenu = row.find('.dropdown-menu');
        dropdownMenu.html(`
            <li>
                <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-rose-600 hover:bg-rose-50 action-reject">
                    <i class="fa-solid fa-xmark me-2 text-rose-500"></i> Reject
                </button>
            </li>
        `);
        updateStats();
    });

    // Reject leave action handler
    $(document).on('click', '.action-reject', function(e) {
        e.preventDefault();
        var row = $(this).closest('tr');
        var cell = row.find('.status-cell');
        cell.html(`
            <span class="status-pill rejected">
                <span class="status-dot rejected"></span>
                <span class="status-text">Rejected</span>
            </span>
        `);
        var dropdownMenu = row.find('.dropdown-menu');
        dropdownMenu.html(`
            <li>
                <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-emerald-600 hover:bg-emerald-50 action-approve">
                    <i class="fa-solid fa-check me-2 text-emerald-500"></i> Approve
                </button>
            </li>
        `);
        updateStats();
    });

    // Handle new leave request form submission
    $('#requestLeaveForm').on('submit', function(e) {
        e.preventDefault();
        var name = $('#leave_employee_name').val();
        var type = $('#leave_type').val();
        var startDate = $('#leave_start_date').val();
        var endDate = $('#leave_end_date').val();

        // Calculate days
        var start = new Date(startDate);
        var end = new Date(endDate);
        var diffTime = Math.abs(end - start);
        var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        if (isNaN(diffDays) || diffDays <= 0) {
            diffDays = 1;
        }

        var initials = name.split(' ').map(function(n) { return n[0]; }).join('').substring(0, 2).toUpperCase() || 'EM';

        var typeBadgeClass = 'bg-blue-50 text-blue-700';
        if (type.indexOf('Sick') !== -1) typeBadgeClass = 'bg-purple-50 text-purple-700';
        else if (type.indexOf('Casual') !== -1) typeBadgeClass = 'bg-amber-50 text-amber-700';

        var newRow = table.row.add([
            `<div class="flex items-center gap-2.5">
                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">${initials}</div>
                <div>
                    <span class="font-medium text-slate-800">${name}</span>
                    <span class="block text-[11px] text-slate-400">${name.toLowerCase().replace(/\\s+/g, '')}@example.com</span>
                </div>
            </div>`,
            `<span class="inline-flex items-center rounded-md ${typeBadgeClass} px-2 py-0.5 text-xs font-medium">${type}</span>`,
            startDate,
            endDate,
            `<span class="font-medium text-slate-700">${diffDays} ${diffDays === 1 ? 'day' : 'days'}</span>`,
            `<span class="status-pill pending">
                <span class="status-dot pending"></span>
                <span class="status-text">Pending</span>
            </span>`,
            `<div class="dropdown text-end">
                <button type="button" class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition border-0 bg-transparent cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                    <i class="fa-solid fa-ellipsis text-sm"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 140px;">
                    <li>
                        <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-emerald-600 hover:bg-emerald-50 action-approve">
                            <i class="fa-solid fa-check me-2 text-emerald-500"></i> Approve
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-rose-600 hover:bg-rose-50 action-reject">
                            <i class="fa-solid fa-xmark me-2 text-rose-500"></i> Reject
                        </button>
                    </li>
                </ul>
            </div>`
        ]).draw(false).node();

        $(newRow).find('td:eq(5)').addClass('status-cell');
        $(newRow).find('td:eq(6)').addClass('text-end');

        $('#requestLeaveModal').modal('hide');
        $('#requestLeaveForm')[0].reset();
        updateStats();

        if (window.lucide) {
            window.lucide.createIcons();
        }
    });

    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>
@endpush
