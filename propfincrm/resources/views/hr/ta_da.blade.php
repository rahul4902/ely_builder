@section('page-title', 'TA / DA Claims')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')
@extends('layouts.master')

@section('styles')
<style>
    #tada-page { min-height: calc(100vh - 40px); background: #fff; font-family: 'Outfit', sans-serif; }
    #tada-page .tada-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 20px; border-bottom: 1px solid #e2e8f0; }
    #tada-page .ui-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 34px; padding: 0 12px; border: 1px solid #dbe3ee; border-radius: 6px; background: #fff; color: #475569; font-size: 12px; font-weight: 500; text-decoration: none; cursor: pointer; transition: all .15s ease; }
    #tada-page .ui-btn:hover { border-color: #cbd5e1; background: #f8fafc; color: #1e293b; }
    #tada-page .ui-btn.primary { border-color: #f97316; background: #f97316; color: #fff; font-weight: 600; }
    #tada-page .ui-btn.primary:hover { border-color: #ea580c; background: #ea580c; }
    #tada-page .dataTables_filter, #tada-page .dataTables_length, #tada-page .dt-search, #tada-page .dt-length { display: none !important; }
    #tada-page .dataTables_wrapper, #tada-page .dataTables_scroll { width: 100% !important; margin: 0 !important; }
    #tada-page table.dataTable { width: 100% !important; margin: 0 !important; border-collapse: collapse !important; }
    #tada-page .dataTables_scrollHead table thead th, #tada-page table.dataTable thead th { height: 36px; padding: 8px 16px !important; border: 0 !important; border-bottom: 1px solid #e2e8f0 !important; background: #f7f9fc !important; color: #94a3b8 !important; font-size: 10.5px !important; font-weight: 600 !important; letter-spacing: .05em; text-transform: uppercase; white-space: nowrap; vertical-align: middle; }
    #tada-page table.dataTable tbody td { height: 44px; padding: 8px 16px !important; border: 0 !important; border-bottom: 1px solid #f1f5f9 !important; color: #334155; font-size: 12.5px; vertical-align: middle; white-space: nowrap; }
    #tada-page table.dataTable tbody tr:hover td { background: #fafbfc; }
    #tada-page .tada-footer { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 20px; border-top: 1px solid #e2e8f0; }
    #tada-page .dataTables_info, #tada-page .dt-info { padding: 0 !important; color: #64748b; font-size: 12px; }
    #tada-page .dataTables_paginate, #tada-page .dt-paging { display: flex; align-items: center; gap: 3px; margin-left: auto; padding: 0 !important; }
    #tada-page .dataTables_paginate .paginate_button, #tada-page .dt-paging .dt-paging-button { box-sizing: border-box; display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 30px; margin: 0 !important; padding: 0 8px !important; border: 1px solid #dbe3ee !important; border-radius: 6px !important; background: #fff !important; color: #475569 !important; font-size: 12px; line-height: 16px; box-shadow: none !important; cursor: pointer; }
    #tada-page .dataTables_paginate .paginate_button.current, #tada-page .dataTables_paginate .paginate_button.current:hover, #tada-page .dt-paging .dt-paging-button.current, #tada-page .dt-paging .dt-paging-button.current:hover { border-color: #f97316 !important; background: #f97316 !important; color: #fff !important; -webkit-text-fill-color: #fff !important; font-weight: 600; }
    #tada-page .dataTables_paginate .paginate_button.disabled, #tada-page .dt-paging .dt-paging-button.disabled { opacity: .45; cursor: not-allowed; }
    #tada-page .dept-badge { display: inline-flex; align-items: center; padding: 2.5px 8px; border-radius: 6px; font-size: 11px; font-weight: 500; }
    .new-row-highlight { animation: tadaHighlight 4s ease forwards; }
    @keyframes tadaHighlight {
        0% { background-color: #fef3c7; }
        70% { background-color: #fef9c3; }
        100% { background-color: transparent; }
    }
    @media (max-width: 640px) {
        #tada-page .tada-toolbar { padding: 12px 14px; flex-direction: column; align-items: stretch; }
        #tada-page .tada-footer { flex-direction: column; align-items: flex-start; }
        #tada-page .dataTables_paginate { margin-left: 0; max-width: 100%; overflow-x: auto; }
    }
</style>
@endsection

@section('content')
<div id="tada-page">
    @php
        $claimsList = (isset($claims) && count($claims) > 0) ? $claims : [
            ['id' => 1, 'name' => 'John Doe', 'dept' => 'HR', 'date' => '2024-10-01', 'purpose' => 'Meeting', 'allowance' => 50],
            ['id' => 2, 'name' => 'Jane Smith', 'dept' => 'IT', 'date' => '2024-10-02', 'purpose' => 'Conference', 'allowance' => 75],
            ['id' => 3, 'name' => 'Mike Johnson', 'dept' => 'Sales', 'date' => '2024-10-03', 'purpose' => 'Client Visit', 'allowance' => 80],
            ['id' => 4, 'name' => 'Emily Davis', 'dept' => 'Marketing', 'date' => '2024-10-04', 'purpose' => 'Workshop', 'allowance' => 65],
            ['id' => 5, 'name' => 'Robert Brown', 'dept' => 'Finance', 'date' => '2024-10-05', 'purpose' => 'Training', 'allowance' => 55],
            ['id' => 6, 'name' => 'Linda Wilson', 'dept' => 'Operations', 'date' => '2024-10-06', 'purpose' => 'Audit', 'allowance' => 90],
            ['id' => 7, 'name' => 'James Taylor', 'dept' => 'Admin', 'date' => '2024-10-07', 'purpose' => 'Inspection', 'allowance' => 70],
            ['id' => 8, 'name' => 'Mary White', 'dept' => 'Legal', 'date' => '2024-10-08', 'purpose' => 'Legal Review', 'allowance' => 85],
            ['id' => 9, 'name' => 'Chris Miller', 'dept' => 'R&D', 'date' => '2024-10-09', 'purpose' => 'Research', 'allowance' => 95],
            ['id' => 10, 'name' => 'Patricia Garcia', 'dept' => 'Customer Service', 'date' => '2024-10-10', 'purpose' => 'Support', 'allowance' => 60],
            ['id' => 11, 'name' => 'David Lee', 'dept' => 'HR', 'date' => '2024-10-11', 'purpose' => 'Meeting', 'allowance' => 50],
            ['id' => 12, 'name' => 'Susan Clark', 'dept' => 'IT', 'date' => '2024-10-12', 'purpose' => 'Conference', 'allowance' => 75],
            ['id' => 13, 'name' => 'Brian Lewis', 'dept' => 'Sales', 'date' => '2024-10-13', 'purpose' => 'Client Visit', 'allowance' => 80],
            ['id' => 14, 'name' => 'Jessica Walker', 'dept' => 'Marketing', 'date' => '2024-10-14', 'purpose' => 'Workshop', 'allowance' => 65],
            ['id' => 15, 'name' => 'Thomas Hall', 'dept' => 'Finance', 'date' => '2024-10-15', 'purpose' => 'Training', 'allowance' => 55],
            ['id' => 16, 'name' => 'Angela Young', 'dept' => 'Operations', 'date' => '2024-10-16', 'purpose' => 'Audit', 'allowance' => 90],
            ['id' => 17, 'name' => 'Mark King', 'dept' => 'Admin', 'date' => '2024-10-17', 'purpose' => 'Inspection', 'allowance' => 70],
            ['id' => 18, 'name' => 'Melissa Hernandez', 'dept' => 'Legal', 'date' => '2024-10-18', 'purpose' => 'Legal Review', 'allowance' => 85],
            ['id' => 19, 'name' => 'Paul Perez', 'dept' => 'R&D', 'date' => '2024-10-19', 'purpose' => 'Research', 'allowance' => 95],
            ['id' => 20, 'name' => 'Anna Scott', 'dept' => 'Customer Service', 'date' => '2024-10-20', 'purpose' => 'Support', 'allowance' => 60],
            ['id' => 21, 'name' => 'Kevin Adams', 'dept' => 'HR', 'date' => '2024-10-21', 'purpose' => 'Training', 'allowance' => 50],
            ['id' => 22, 'name' => 'Donna Baker', 'dept' => 'IT', 'date' => '2024-10-22', 'purpose' => 'Conference', 'allowance' => 75],
            ['id' => 23, 'name' => 'Carlos Gonzalez', 'dept' => 'Sales', 'date' => '2024-10-23', 'purpose' => 'Client Visit', 'allowance' => 80],
            ['id' => 24, 'name' => 'Brenda Mitchell', 'dept' => 'Marketing', 'date' => '2024-10-24', 'purpose' => 'Workshop', 'allowance' => 65],
            ['id' => 25, 'name' => 'Eric Robinson', 'dept' => 'Finance', 'date' => '2024-10-25', 'purpose' => 'Training', 'allowance' => 55],
            ['id' => 26, 'name' => 'Laura Reed', 'dept' => 'Operations', 'date' => '2024-10-26', 'purpose' => 'Audit', 'allowance' => 90],
            ['id' => 27, 'name' => 'Scott Turner', 'dept' => 'Admin', 'date' => '2024-10-27', 'purpose' => 'Inspection', 'allowance' => 70],
            ['id' => 28, 'name' => 'Betty Phillips', 'dept' => 'Legal', 'date' => '2024-10-28', 'purpose' => 'Legal Review', 'allowance' => 85],
            ['id' => 29, 'name' => 'Harry Evans', 'dept' => 'R&D', 'date' => '2024-10-29', 'purpose' => 'Research', 'allowance' => 95],
            ['id' => 30, 'name' => 'Rachel Collins', 'dept' => 'Customer Service', 'date' => '2024-10-30', 'purpose' => 'Support', 'allowance' => 60],
            ['id' => 31, 'name' => 'Tyler Fisher', 'dept' => 'HR', 'date' => '2024-10-31', 'purpose' => 'Meeting', 'allowance' => 50],
            ['id' => 32, 'name' => 'Sophie Foster', 'dept' => 'IT', 'date' => '2024-11-01', 'purpose' => 'Conference', 'allowance' => 75],
            ['id' => 33, 'name' => 'Jack Moore', 'dept' => 'Sales', 'date' => '2024-11-02', 'purpose' => 'Client Visit', 'allowance' => 80],
            ['id' => 34, 'name' => 'Megan Howard', 'dept' => 'Marketing', 'date' => '2024-11-03', 'purpose' => 'Workshop', 'allowance' => 65],
            ['id' => 35, 'name' => 'Tim Harris', 'dept' => 'Finance', 'date' => '2024-11-04', 'purpose' => 'Training', 'allowance' => 55],
        ];
    @endphp

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 gap-3 border-b border-slate-200 bg-slate-50/60 p-4 sm:grid-cols-4 sm:px-6">
        <div class="rounded-lg border border-slate-200/80 bg-white p-3 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium uppercase tracking-wider text-slate-400">Total Claims</span>
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-slate-100 text-slate-600"><i data-lucide="receipt" class="h-3.5 w-3.5"></i></span>
            </div>
            <div class="mt-1 text-lg font-bold text-slate-800" id="stat-total">{{ $totalClaims ?? count($claimsList) }}</div>
        </div>
        <div class="rounded-lg border border-emerald-200/80 bg-white p-3 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium uppercase tracking-wider text-emerald-700">Total Allowance</span>
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-emerald-50 text-emerald-600"><i data-lucide="badge-dollar-sign" class="h-3.5 w-3.5"></i></span>
            </div>
            <div class="mt-1 text-lg font-bold text-emerald-600" id="stat-amount">${{ number_format($totalAllowance ?? 2510, 2) }}</div>
        </div>
        <div class="rounded-lg border border-blue-200/80 bg-white p-3 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium uppercase tracking-wider text-blue-700">Departments</span>
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-blue-50 text-blue-600"><i data-lucide="building-2" class="h-3.5 w-3.5"></i></span>
            </div>
            <div class="mt-1 text-lg font-bold text-blue-600" id="stat-dept">{{ $deptCount ?? 10 }}</div>
        </div>
        <div class="rounded-lg border border-purple-200/80 bg-white p-3 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium uppercase tracking-wider text-purple-700">Avg. Claim</span>
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-purple-50 text-purple-600"><i data-lucide="chart-column" class="h-3.5 w-3.5"></i></span>
            </div>
            <div class="mt-1 text-lg font-bold text-purple-600" id="stat-avg">${{ number_format($avgAllowance ?? 71.71, 2) }}</div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="tada-toolbar">
        <div class="flex items-center gap-2">
            <div class="relative w-64 max-w-full">
                <i data-lucide="search" class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400"></i>
                <input id="tada-search" type="search" placeholder="Search claims..." autocomplete="off" class="w-full rounded-lg border border-slate-200 bg-slate-50/70 py-1.5 pl-8 pr-3 text-xs text-slate-700 transition hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>
            <select id="dept-filter" class="h-[34px] rounded-lg border border-slate-200 bg-white px-2.5 text-xs text-slate-600 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                <option value="">All Departments</option>
                <option value="HR">HR</option>
                <option value="IT">IT</option>
                <option value="Sales">Sales</option>
                <option value="Marketing">Marketing</option>
                <option value="Finance">Finance</option>
                <option value="Operations">Operations</option>
                <option value="Admin">Admin</option>
                <option value="Legal">Legal</option>
                <option value="R&D">R&D</option>
                <option value="Customer Service">Customer Service</option>
            </select>
            <button type="button" id="tada-refresh" class="inline-flex h-[34px] w-[34px] items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800" title="Refresh Table">
                <i data-lucide="refresh-cw" id="tada-refresh-icon" class="h-3.5 w-3.5"></i>
            </button>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="ui-btn primary" data-bs-toggle="modal" data-bs-target="#claimModal">
                <i data-lucide="plus" class="h-3.5 w-3.5"></i> New Claim
            </button>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="w-full overflow-x-auto">
        <table id="taTable" class="w-full">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Date</th>
                    <th>Purpose</th>
                    <th>Allowance</th>
                    <th class="no-sort text-end" style="width: 70px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($claimsList as $claim)
                    @php
                        $cId = is_array($claim) ? $claim['id'] : $claim->id;
                        $cName = is_array($claim) ? $claim['name'] : $claim->name;
                        $cDept = is_array($claim) ? $claim['dept'] : $claim->dept;
                        $cDate = is_array($claim) ? $claim['date'] : (is_string($claim->date) ? $claim->date : $claim->date->format('Y-m-d'));
                        $cPurpose = is_array($claim) ? $claim['purpose'] : $claim->purpose;
                        $cAllowance = is_array($claim) ? $claim['allowance'] : $claim->allowance;

                        $nameParts = explode(' ', $cName);
                        $initials = (isset($nameParts[0][0]) ? $nameParts[0][0] : '') . (isset($nameParts[1][0]) ? $nameParts[1][0] : '');
                        $initials = strtoupper($initials ?: 'EM');

                        $deptStyle = match($cDept) {
                            'HR' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #dbeafe;',
                            'IT' => 'background:#f5f3ff; color:#6d28d9; border:1px solid #ede9fe;',
                            'Sales' => 'background:#ecfdf5; color:#047857; border:1px solid #d1fae5;',
                            'Marketing' => 'background:#fff7ed; color:#c2410c; border:1px solid #ffedd5;',
                            'Finance' => 'background:#fefce8; color:#a16207; border:1px solid #fef9c3;',
                            'Operations' => 'background:#f0fdfa; color:#0f766e; border:1px solid #ccfbf1;',
                            'Admin' => 'background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;',
                            'Legal' => 'background:#faf5ff; color:#7e22ce; border:1px solid #f3e8ff;',
                            'R&D' => 'background:#fdf2f8; color:#be185d; border:1px solid #fce7f3;',
                            default => 'background:#f8fafc; color:#64748b; border:1px solid #e2e8f0;'
                        };
                    @endphp
                    <tr>
                        <td class="text-slate-500 font-semibold text-xs">{{ $cId }}</td>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">{{ $initials }}</div>
                                <div>
                                    <span class="font-medium text-slate-800">{{ $cName }}</span>
                                    <span class="block text-[11px] text-slate-400">{{ strtolower(str_replace(' ', '', $cName)) }}@example.com</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="dept-badge" style="{{ $deptStyle }}">{{ $cDept }}</span>
                        </td>
                        <td>{{ $cDate }}</td>
                        <td>
                            <span class="inline-flex items-center gap-1.5 text-slate-700">
                                <i data-lucide="briefcase" class="h-3.5 w-3.5 text-slate-400"></i>
                                {{ $cPurpose }}
                            </span>
                        </td>
                        <td>
                            <span class="font-semibold text-slate-900">${{ number_format((float)$cAllowance, 2) }}</span>
                        </td>
                        <td class="text-end">
                            <div class="dropdown text-end">
                                <button type="button" class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition border-0 bg-transparent cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                    <i class="fa-solid fa-ellipsis text-sm"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 140px;">
                                    <li>
                                        <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-slate-700 hover:bg-slate-50 action-view" data-id="{{ $cId }}" data-name="{{ $cName }}" data-dept="{{ $cDept }}" data-date="{{ $cDate }}" data-purpose="{{ $cPurpose }}" data-allowance="${{ number_format((float)$cAllowance, 2) }}">
                                            <i class="fa-regular fa-eye me-2 text-slate-400"></i> View Details
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider my-1 border-slate-100"></li>
                                    <li>
                                        <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-rose-600 hover:bg-rose-50 action-delete" data-id="{{ $cId }}">
                                            <i class="fa-regular fa-trash-can me-2 text-rose-500"></i> Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- New Claim Modal using shared <x-form.input> components --}}
<div class="modal fade" id="claimModal" tabindex="-1" aria-labelledby="claimModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-xl">
            <div class="modal-header border-b border-slate-200 px-5 py-4">
                <h5 class="modal-title text-sm font-semibold text-slate-800" id="claimModalLabel">Submit TA / DA Claim</h5>
                <button type="button" class="btn-close text-xs" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="claimForm">
                @csrf
                <div class="modal-body p-5 space-y-3">
                    <x-form.input
                        layout="standard"
                        label="Employee Name *"
                        name="name"
                        id="claim_name"
                        type="text"
                        placeholder="e.g. Alex Morgan"
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                        required
                    />

                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input
                            layout="standard"
                            label="Department *"
                            name="department"
                            id="claim_dept"
                            type="select"
                            :options="[
                                'HR' => 'HR',
                                'IT' => 'IT',
                                'Sales' => 'Sales',
                                'Marketing' => 'Marketing',
                                'Finance' => 'Finance',
                                'Operations' => 'Operations',
                                'Admin' => 'Admin',
                                'Legal' => 'Legal',
                                'R&D' => 'R&D',
                                'Customer Service' => 'Customer Service'
                            ]"
                            class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                            required
                        />

                        <x-form.input
                            layout="standard"
                            label="Date *"
                            name="date"
                            id="claim_date"
                            type="date"
                            class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                            required
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <x-form.input
                            layout="standard"
                            label="Purpose *"
                            name="purpose"
                            id="claim_purpose"
                            type="text"
                            placeholder="e.g. Client Visit"
                            class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                            required
                        />

                        <x-form.input
                            layout="standard"
                            label="Allowance Amount ($) *"
                            name="allowance"
                            id="claim_allowance"
                            type="number"
                            step="0.01"
                            placeholder="e.g. 75"
                            class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                            min="0.01"
                            required
                        />
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-200 px-5 py-3">
                    <button type="button" class="ui-btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="ui-btn primary">Save Claim</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Details Modal --}}
<div class="modal fade" id="claimDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-xl">
            <div class="modal-header border-b border-slate-200 px-5 py-4">
                <h5 class="modal-title text-sm font-semibold text-slate-800">Claim Details</h5>
                <button type="button" class="btn-close text-xs" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5 space-y-3 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-400">Claim ID</span>
                    <span class="font-semibold text-slate-800" id="detail-id"></span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-400">Employee</span>
                    <span class="font-semibold text-slate-800" id="detail-name"></span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-400">Department</span>
                    <span class="font-semibold text-slate-800" id="detail-dept"></span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-400">Date</span>
                    <span class="font-semibold text-slate-800" id="detail-date"></span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-400">Purpose</span>
                    <span class="font-semibold text-slate-800" id="detail-purpose"></span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-400">Allowance Amount</span>
                    <span class="font-bold text-emerald-600 text-sm" id="detail-allowance"></span>
                </div>
            </div>
            <div class="modal-footer border-t border-slate-200 px-5 py-3">
                <button type="button" class="ui-btn" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
$(document).ready(function() {
    // Toast notification helper
    function showToast(title, message, type) {
        var bgClass = type === 'success' ? 'bg-emerald-600 text-white' : (type === 'danger' ? 'bg-rose-600 text-white' : 'bg-slate-800 text-white');
        var toastId = 'toast_' + Date.now();
        var toastHtml = `
            <div id="${toastId}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg shadow-xl text-xs font-medium ${bgClass} transition-all duration-300 transform translate-y-2 opacity-0" style="min-width: 260px; max-width: 400px;">
                <div class="flex-1">
                    <strong class="font-semibold">${title}:</strong> ${message}
                </div>
                <button type="button" class="text-white/80 hover:text-white ms-2 text-base leading-none bg-transparent border-0 cursor-pointer" onclick="$('#${toastId}').remove()">&times;</button>
            </div>`;
        var $container = $('#tada-toast-container');
        if (!$container.length) {
            $('body').append('<div id="tada-toast-container" class="fixed bottom-5 right-5 z-[99999] flex flex-col gap-2"></div>');
            $container = $('#tada-toast-container');
        }
        var $el = $(toastHtml).appendTo($container);
        setTimeout(function() {
            $el.removeClass('translate-y-2 opacity-0').addClass('translate-y-0 opacity-100');
        }, 50);
        setTimeout(function() {
            $el.removeClass('opacity-100').addClass('opacity-0');
            setTimeout(function() { $el.remove(); }, 300);
        }, 4000);
    }

    // Initialize DataTable ordered by ID descending so newest claims appear at the TOP
    var table = $('#taTable').DataTable({
        pagingType: 'full_numbers',
        order: [[0, 'desc']],
        pageLength: 10,
        autoWidth: false,
        columnDefs: [
            { targets: 0, type: 'num' },
            { targets: 'no-sort', orderable: false, searchable: false }
        ],
        dom: '<"w-full overflow-x-auto"t><"tada-footer"<"tada-footer-inner"ip>>',
        language: {
            emptyTable: 'No TA / DA claims found',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 to 0 of 0 entries',
            paginate: { previous: 'Previous', next: 'Next' }
        }
    });

    // Custom toolbar search
    $('#tada-search').on('input', function() {
        table.search(this.value).draw();
    });

    // Department filter dropdown
    $('#dept-filter').on('change', function() {
        var dept = $(this).val();
        table.column(2).search(dept ? '^' + dept + '$' : '', true, false).draw();
    });

    // Refresh button
    $('#tada-refresh').on('click', function() {
        var icon = $('#tada-refresh-icon');
        icon.addClass('animate-spin');
        $('#tada-search').val('');
        $('#dept-filter').val('');
        table.search('').columns().search('').order([[0, 'desc']]).draw();
        setTimeout(function() {
            icon.removeClass('animate-spin');
        }, 300);
    });

    // View Details Modal Trigger
    $(document).on('click', '.action-view', function() {
        $('#detail-id').text('#' + $(this).data('id'));
        $('#detail-name').text($(this).data('name'));
        $('#detail-dept').text($(this).data('dept'));
        $('#detail-date').text($(this).data('date'));
        $('#detail-purpose').text($(this).data('purpose'));
        $('#detail-allowance').text($(this).data('allowance'));
        new bootstrap.Modal(document.getElementById('claimDetailsModal')).show();
    });

    // Delete Action Trigger
    $(document).on('click', '.action-delete', function() {
        var $btn = $(this);
        var claimId = $btn.data('id');
        var $row = $btn.closest('tr');

        if (confirm('Are you sure you want to delete this claim?')) {
            if (claimId) {
                $.ajax({
                    url: "{{ url('ta-da') }}/" + claimId,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        table.row($row).remove().draw(false);
                        if (res.stats) {
                            $('#stat-total').text(res.stats.total);
                            $('#stat-amount').text('$' + res.stats.amount);
                            $('#stat-avg').text('$' + res.stats.avg);
                            if (res.stats.departments !== undefined) {
                                $('#stat-dept').text(res.stats.departments);
                            }
                        }
                        showToast('Deleted', 'Claim #' + claimId + ' deleted successfully.', 'info');
                    },
                    error: function() {
                        table.row($row).remove().draw(false);
                        $('#stat-total').text(table.rows().count());
                    }
                });
            } else {
                table.row($row).remove().draw(false);
                $('#stat-total').text(table.rows().count());
            }
        }
    });

    // New Claim Form Handler with server persistence and instant top-row display
    $('#claimForm').on('submit', function(e) {
        e.preventDefault();

        var $submitBtn = $(this).find('button[type="submit"]');
        var originalBtnText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Saving...');

        var name = $('#claim_name').val().trim();
        var dept = $('#claim_dept').val();
        var date = $('#claim_date').val();
        var purpose = $('#claim_purpose').val().trim();
        var allowance = $('#claim_allowance').val();

        var postData = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            name: name,
            department: dept,
            date: date,
            purpose: purpose,
            allowance: allowance
        };

        $.ajax({
            url: "{{ route('ta_da.store') }}",
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(res) {
                $submitBtn.prop('disabled', false).html(originalBtnText);

                if (res.success && res.claim) {
                    var c = res.claim;
                    var nameParts = c.name.split(' ');
                    var initials = (nameParts[0] ? nameParts[0][0] : '') + (nameParts[1] ? nameParts[1][0] : '') || 'EM';
                    initials = initials.toUpperCase();

                    var deptStyles = {
                        'HR': 'background:#eff6ff; color:#1d4ed8; border:1px solid #dbeafe;',
                        'IT': 'background:#f5f3ff; color:#6d28d9; border:1px solid #ede9fe;',
                        'Sales': 'background:#ecfdf5; color:#047857; border:1px solid #d1fae5;',
                        'Marketing': 'background:#fff7ed; color:#c2410c; border:1px solid #ffedd5;',
                        'Finance': 'background:#fefce8; color:#a16207; border:1px solid #fef9c3;',
                        'Operations': 'background:#f0fdfa; color:#0f766e; border:1px solid #ccfbf1;',
                        'Admin': 'background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;',
                        'Legal': 'background:#faf5ff; color:#7e22ce; border:1px solid #f3e8ff;',
                        'R&D': 'background:#fdf2f8; color:#be185d; border:1px solid #fce7f3;'
                    };
                    var deptStyle = deptStyles[c.dept] || 'background:#f8fafc; color:#64748b; border:1px solid #e2e8f0;';

                    // Add new row to DataTable
                    var newRow = table.row.add([
                        `<span class="text-slate-500 font-semibold text-xs">${c.id}</span>`,
                        `<div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-orange-100 text-xs font-semibold text-orange-700">${initials}</div>
                            <div>
                                <span class="font-medium text-slate-800">${c.name}</span>
                                <span class="block text-[11px] text-slate-400">${c.name.toLowerCase().replace(/\\s+/g, '')}@example.com</span>
                            </div>
                        </div>`,
                        `<span class="dept-badge" style="${deptStyle}">${c.dept}</span>`,
                        c.date,
                        `<span class="inline-flex items-center gap-1.5 text-slate-700">
                            <i data-lucide="briefcase" class="h-3.5 w-3.5 text-slate-400"></i>
                            ${c.purpose}
                        </span>`,
                        `<span class="font-semibold text-slate-900">$${parseFloat(c.allowance).toFixed(2)}</span>`,
                        `<div class="dropdown text-end">
                            <button type="button" class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition border-0 bg-transparent cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                <i class="fa-solid fa-ellipsis text-sm"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 140px;">
                                <li>
                                    <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-slate-700 hover:bg-slate-50 action-view" data-id="${c.id}" data-name="${c.name}" data-dept="${c.dept}" data-date="${c.date}" data-purpose="${c.purpose}" data-allowance="$${parseFloat(c.allowance).toFixed(2)}">
                                        <i class="fa-regular fa-eye me-2 text-slate-400"></i> View Details
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider my-1 border-slate-100"></li>
                                <li>
                                    <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-rose-600 hover:bg-rose-50 action-delete" data-id="${c.id}">
                                        <i class="fa-regular fa-trash-can me-2 text-rose-500"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>`
                    ]).draw(false).node();

                    $(newRow).find('td:eq(6)').addClass('text-end');

                    // Order by ID descending and move to Page 1 so new row is at the very top
                    table.order([[0, 'desc']]).page('first').draw(false);

                    // Add smooth highlight animation to the newly created row
                    $(newRow).addClass('new-row-highlight');

                    // Close modal and reset form
                    $('#claimModal').modal('hide');
                    $('#claimForm')[0].reset();

                    // Update summary statistics cards
                    if (res.stats) {
                        $('#stat-total').text(res.stats.total);
                        $('#stat-amount').text('$' + res.stats.amount);
                        $('#stat-avg').text('$' + res.stats.avg);
                        if (res.stats.departments !== undefined) {
                            $('#stat-dept').text(res.stats.departments);
                        }
                    }

                    if (window.lucide) {
                        window.lucide.createIcons();
                    }

                    showToast('Claim Created', 'Claim #' + c.id + ' for ' + c.name + ' has been saved and added to the list.', 'success');
                }
            },
            error: function(xhr) {
                $submitBtn.prop('disabled', false).html(originalBtnText);
                var message = 'Failed to submit claim. Please check your inputs.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    var firstKey = Object.keys(errors)[0];
                    message = errors[firstKey][0];
                }
                showToast('Error', message, 'danger');
            }
        });
    });

    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>
@endpush
