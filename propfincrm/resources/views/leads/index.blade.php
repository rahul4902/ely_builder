@php
    $currentType = $type ?? request()->route('type') ?? request()->route('id') ?? '';
    $pageTitle = $currentType ? ucfirst($currentType) : __('All leads');
@endphp

@section('page-title', 'Leads')
@section('page-count', isset($leadMetrics['total']) ? '(' . number_format($leadMetrics['total']) . ')' : '')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')

@extends('layouts.master')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/5.0.3/css/fixedColumns.dataTables.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <style>
        /* Target DataTables scrollhead orphan scrollbars */
        div.dataTables_scrollHead,
        div.dt-scroll-head {
            overflow: hidden !important;
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        div.dataTables_scrollHead::-webkit-scrollbar,
        div.dt-scroll-head::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        /* Scoped DataTable Styles */
        #registration_wrapper .dataTables_filter,
        .dt-buttons,
        .dataTables_wrapper .dt-buttons {
            display: none !important;
        }

        /* 1. Eliminate unwanted space between Table Header and Body */
        .dataTables_scroll,
        .dataTables_scrollHead,
        .dataTables_scrollHeadInner,
        .dataTables_scrollHead table,
        .dataTables_scrollBody,
        .dataTables_scrollBody table,
        #registration {
            margin: 0 !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            border-spacing: 0 !important;
            border-collapse: collapse !important;
        }

        div.dataTables_scrollHead {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        div.dataTables_scrollBody {
            margin-top: 0 !important;
            padding-top: 0 !important;
            border-top: none !important;
        }

        /* Safe zero-height dummy header for DataTables (synchronizes column widths without showing duplicate header or unwanted space) */
        div.dataTables_scrollBody thead,
        div.dataTables_scrollBody thead tr {
            height: 0px !important;
            min-height: 0px !important;
            max-height: 0px !important;
            border: none !important;
            padding: 0px !important;
            margin: 0px !important;
        }

        div.dataTables_scrollBody thead th,
        div.dataTables_scrollBody thead td {
            height: 0px !important;
            min-height: 0px !important;
            max-height: 0px !important;
            padding-top: 0px !important;
            padding-bottom: 0px !important;
            margin: 0px !important;
            border: none !important;
            border-top: none !important;
            border-bottom: none !important;
            line-height: 0px !important;
            font-size: 0px !important;
            background: transparent !important;
            background-color: transparent !important;
            color: transparent !important;
            visibility: hidden !important;
        }

        div.dataTables_scrollBody thead * {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            width: 0px !important;
            height: 0px !important;
            content: "" !important;
        }

                /* Immediate styling for #registration before DataTables JS initializes */
        #registration {
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
        }
        #registration.dt-initialized,
        .dataTables_wrapper #registration {
            opacity: 1 !important;
        }

        /* Header styling specifically for the visible scrollHead (and un-initialized registration) */
        div.dataTables_scrollHead table thead th,
        div.dt-scroll-head table thead th,
        #registration:not(.dataTable) thead th {
            background: #f7f9fc !important;
            background-color: #f7f9fc !important;
            color: #94a3b8 !important;
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif !important;
            font-size: 11px !important;
            font-weight: 500 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            line-height: 1.2 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 8px 12px !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
            box-shadow: none !important;
            user-select: none;
        }

        div.dataTables_scrollHead table thead th *,
        div.dt-scroll-head table thead th *,
        #registration:not(.dataTable) thead th * {
            font-size: 11px !important;
            font-weight: 500 !important;
            color: #94a3b8 !important;
            letter-spacing: 0.05em !important;
            text-transform: uppercase !important;
        }

        /* Compact Table Rows */
        #registration tbody td {
            background-color: #ffffff !important;
            color: #334155 !important;
            font-size: 12.5px !important;
            padding: 6px 12px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            vertical-align: middle !important;
            white-space: nowrap !important;
            line-height: 1.35 !important;
        }

        #registration tbody tr:hover td {
            background-color: #fafbfc !important;
        }

        /* Column sorting indicators ONLY in the visible scrollHead table */
        div.dataTables_scrollHead table thead > tr > th.dt-orderable-asc,
        div.dataTables_scrollHead table thead > tr > th.dt-orderable-desc {
            position: relative !important;
            cursor: pointer !important;
            padding-right: 20px !important;
        }

        div.dataTables_scrollHead span.dt-column-order {
            display: inline-flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            position: absolute !important;
            right: 5px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            width: 8px !important;
            height: 11px !important;
            opacity: 0.3 !important;
            visibility: visible !important;
            transition: opacity 0.15s ease-in-out !important;
        }

        div.dataTables_scrollHead table thead > tr > th:hover span.dt-column-order {
            opacity: 0.7 !important;
        }

        div.dataTables_scrollHead span.dt-column-order:before {
            content: "▲" !important;
            font-size: 6px !important;
            line-height: 6px !important;
            position: static !important;
            display: block !important;
            color: #94a3b8 !important;
        }

        div.dataTables_scrollHead span.dt-column-order:after {
            content: "▼" !important;
            font-size: 6px !important;
            line-height: 6px !important;
            position: static !important;
            display: block !important;
            color: #94a3b8 !important;
            margin-top: 1px !important;
        }

        div.dataTables_scrollHead table thead > tr > th.dt-ordering-asc span.dt-column-order {
            opacity: 1 !important;
        }
        div.dataTables_scrollHead table thead > tr > th.dt-ordering-asc span.dt-column-order:before {
            color: #f97316 !important;
            opacity: 1 !important;
        }
        div.dataTables_scrollHead table thead > tr > th.dt-ordering-asc span.dt-column-order:after {
            opacity: 0.15 !important;
        }

        div.dataTables_scrollHead table thead > tr > th.dt-ordering-desc span.dt-column-order {
            opacity: 1 !important;
        }
        div.dataTables_scrollHead table thead > tr > th.dt-ordering-desc span.dt-column-order:after {
            color: #f97316 !important;
            opacity: 1 !important;
        }
        div.dataTables_scrollHead table thead > tr > th.dt-ordering-desc span.dt-column-order:before {
            opacity: 0.15 !important;
        }

        div.dataTables_scrollHead th.checkbox-column span.dt-column-order,
        div.dataTables_scrollHead th.action-column span.dt-column-order,
        div.dataTables_scrollHead th.no-sort span.dt-column-order {
            display: none !important;
        }

        /* Custom Crisp Checkbox (Orange checked state matching screenshot) */
        input[type="checkbox"].lead-checkbox,
        #selectAllLeads {
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            width: 15px !important;
            height: 15px !important;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 4px !important;
            background-color: #ffffff !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative !important;
            vertical-align: middle !important;
            transition: all 0.15s ease-in-out !important;
            outline: none !important;
            margin: 0 !important;
            box-sizing: border-box !important;
        }

        input[type="checkbox"].lead-checkbox:hover,
        #selectAllLeads:hover {
            border-color: #f97316 !important;
        }

        input[type="checkbox"].lead-checkbox:checked,
        #selectAllLeads:checked {
            background-color: #f97316 !important;
            border-color: #f97316 !important;
        }

        input[type="checkbox"].lead-checkbox:checked::after,
        #selectAllLeads:checked::after {
            content: '' !important;
            position: absolute !important;
            left: 4px !important;
            top: 1px !important;
            width: 4px !important;
            height: 8px !important;
            border: solid #ffffff !important;
            border-width: 0 2px 2px 0 !important;
            transform: rotate(45deg) !important;
        }

        /* Theme-Matched Modern Pagination: keep info and controls together
           in the visual centre of the table footer. */
        .dt-page-nav {
            display: grid !important;
            grid-template-columns: max-content max-content !important;
            align-items: center !important;
            justify-content: center !important;
            column-gap: 24px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .dt-page-nav > * {
            float: none !important;
            grid-row: 1 !important;
            margin: 0 !important;
            max-width: none !important;
            width: auto !important;
        }

        .dt-page-nav .dataTables_info,
        .dt-page-nav .dt-info {
            margin: 0 !important;
            text-align: center !important;
            flex: 0 0 auto !important;
            width: auto !important;
        }

        .dt-page-nav .dt-pagination,
        .dt-page-nav .dataTables_paginate,
        .dt-page-nav .dt-paging,
        #registration_wrapper .dataTables_paginate,
        #registration_wrapper .dt-paging {
            margin: 0 !important;
            justify-content: center !important;
            display: flex !important;
            flex: 0 0 auto !important;
            width: auto !important;
        }
        .dt-page-nav .dataTables_info {
            color: #64748b !important;
            font-size: 12px !important;
            font-weight: 400 !important;
            padding: 0 !important;
            line-height: 1.5 !important;
        }

        .dataTables_paginate,
        .dt-paging,
        .dt-page-nav .dataTables_paginate {
            display: flex !important;
            align-items: center !important;
            gap: 3px !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .dataTables_paginate button,
        .dataTables_paginate a,
        .dataTables_paginate .paginate_button,
        .dataTables_paginate .dt-paging-button,
        .dt-paging button,
        .dt-paging a,
        .dt-paging .dt-paging-button,
        .dt-page-nav button,
        .dt-page-nav a,
        .dt-page-nav .paginate_button,
        .dt-page-nav .dt-paging-button {
            box-sizing: border-box !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 30px !important;
            height: 30px !important;
            padding: 0 9px !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            color: #475569 !important;
            background-color: #ffffff !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            cursor: pointer !important;
            text-decoration: none !important;
            transition: all 0.15s ease-in-out !important;
            margin: 0 1px !important;
            line-height: 1 !important;
            outline: none !important;
        }

        .dataTables_paginate button:hover:not(.current):not(.disabled),
        .dataTables_paginate a:hover:not(.current):not(.disabled),
        .dt-paging button:hover:not(.current):not(.disabled),
        .dt-page-nav button:hover:not(.current):not(.disabled) {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        .dataTables_paginate .current,
        .dt-paging .current,
        .dt-page-nav .current,
        button.dt-paging-button.current,
        .paginate_button.current {
            background-color: #f97316 !important;
            background: #f97316 !important;
            color: #ffffff !important;
            border-color: #ea580c !important;
            font-weight: 600 !important;
            box-shadow: 0 1px 2px rgba(249, 115, 22, 0.25) !important;
        }

        .dataTables_paginate .disabled,
        .dt-paging .disabled,
        .dt-page-nav .disabled,
        button.dt-paging-button.disabled,
        .paginate_button.disabled {
            opacity: 0.4 !important;
            cursor: not-allowed !important;
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #94a3b8 !important;
            box-shadow: none !important;
        }

        .dataTables_paginate .ellipsis,
        .dt-paging .ellipsis,
        .dt-page-nav .ellipsis {
            color: #94a3b8 !important;
            font-size: 12px !important;
            padding: 0 4px !important;
        }

        /* Dedicated lead footer wrapper: this avoids legacy DataTables footer
           rules that were overriding the generic .dt-page-nav styles. */
        #registration_wrapper .lead-table-footer {
            width: 100% !important;
            padding: 12px 24px !important;
            border-top: 1px solid #e2e8f0 !important;
            box-sizing: border-box !important;
        }
        #registration_wrapper .lead-footer-content {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 16px !important;
            width: 100% !important;
        }
        #registration_wrapper .lead-footer-content > * {
            float: none !important;
            flex: 0 0 auto !important;
            width: auto !important;
            margin: 0 !important;
        }
        #leadFilterOffcanvas {
            width: min(420px, 100vw);
        }
        #leadFilterOffcanvas .flatpickr-input,
        #leadFilterOffcanvas .form-select {
            min-height: 38px;
        }

        /* One unified workspace: hierarchy comes from quiet dividers, not
           separate cards for navigation, controls, and the table. */
        #lead-list-workspace {
            background: #ffffff;
            border: 0;
            border-radius: 0;
            box-shadow: none;
            overflow: visible;
        }
        #lead-list-workspace > .lead-toolbar {
            background: #ffffff;
            border: 0;
            border-bottom: 1px solid #e8edf3;
            border-radius: 0;
            box-shadow: none;
        }
        #lead-list-workspace #tableViewContainer {
            margin-top: 0;
            padding-right: 0 !important;
            background: #ffffff;
            border: 0;
            border-radius: 0;
            box-shadow: none;
            overflow: visible;
        }
        #lead-list-workspace .dataTables_scrollHead {
            background: #f8fafc;
        }
        #lead-list-workspace .dt-page-nav {
            background: #ffffff;
        }
    </style>
@endsection

@section('content')
<div id="lead-list-workspace" class="w-full font-sans relative">

    {{-- Action Toolbar --}}
    <div class="lead-toolbar px-6 py-3 flex flex-wrap items-center justify-between gap-3">
        {{-- Left: Search, Filter & Refresh controls --}}
        <div class="flex items-center gap-2">
            <div class="relative w-64">
                <i data-lucide="search" class="h-3.5 w-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" id="leadSearchInput" placeholder="Search"
                       class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50/70 hover:bg-slate-100/70 focus:bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500 text-slate-700 transition">
            </div>

            <button type="button" id="toggleSidebarBtn"
                    data-bs-toggle="offcanvas" data-bs-target="#leadFilterOffcanvas" aria-controls="leadFilterOffcanvas"
                    class="relative inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition cursor-pointer"
                    title="Open Filters">
                <i data-lucide="filter" class="h-3.5 w-3.5" id="filterIcon"></i>
                <span id="activeFilterIndicator" class="hidden absolute -right-1 -top-1 h-2 w-2 rounded-full bg-orange-500 ring-2 ring-white"></span>
            </button>

            <button type="button" id="refreshTableBtn"
                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition cursor-pointer"
                    title="Refresh Table">
                <i data-lucide="refresh-cw" class="h-3.5 w-3.5" id="refreshIcon"></i>
            </button>
        </div>

        {{-- Right: Import, Export, Add Lead --}}
        <div class="flex items-center gap-3">
            {{-- Import CTA --}}
            @if (Entrust::hasRole('administrator'))
                <a href="{{ route('leads.bulk.upload.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition no-underline">
                    <i data-lucide="cloud-upload" class="h-3.5 w-3.5 text-slate-400"></i> Import
                </a>
            @endif

            {{-- Export CTA with Excel & CSV options --}}
            <div class="relative dropdown">
                <button type="button" id="exportDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition cursor-pointer">
                    <i data-lucide="cloud-download" class="h-3.5 w-3.5 text-slate-400"></i> Export
                    <i data-lucide="chevron-down" class="h-3 w-3 text-slate-400 ms-0.5"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 150px;">
                    <li>
                        <a class="dropdown-item py-1.5 px-3 flex items-center text-slate-700 hover:bg-slate-50 cursor-pointer" id="exportExcelBtn" href="javascript:void(0)">
                            <i data-lucide="file-spreadsheet" class="me-2 h-3.5 w-3.5 text-emerald-600"></i> Excel (.xlsx)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-1.5 px-3 flex items-center text-slate-700 hover:bg-slate-50 cursor-pointer" id="exportCsvBtn" href="javascript:void(0)">
                            <i data-lucide="file-text" class="me-2 h-3.5 w-3.5 text-blue-600"></i> CSV (.csv)
                        </a>
                    </li>
                </ul>
            </div>

            {{-- New Lead Primary CTA --}}
            <a href="{{ route('leads.create') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-lg shadow-sm transition no-underline">
                <i data-lucide="plus" class="h-3.5 w-3.5"></i> Add Lead
            </a>
        </div>
    </div>

    {{-- Filters Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="leadFilterOffcanvas" aria-labelledby="leadFilterOffcanvasLabel">
        <div class="offcanvas-header border-bottom border-slate-200 px-4 py-3">
            <div>
                <h5 class="offcanvas-title text-sm font-semibold text-slate-800" id="leadFilterOffcanvasLabel">Filter Leads</h5>
                <p class="m-0 mt-0.5 text-[11px] text-slate-500">Narrow the list using one or more conditions.</p>
            </div>
            <button type="button" class="btn-close text-xs" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4">
        <input type="hidden" id="type" name="type" value="{{ $currentType }}">
        <div class="grid grid-cols-1 gap-3">
            <div>
                <label for="date" class="block text-[11px] font-medium text-slate-500 mb-1">From Date</label>
                <input type="text" id="date" name="date" value="{{ request('date') }}" placeholder="DD-MM-YYYY" autocomplete="off"
                       class="lead-date-picker w-full rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>
            <div>
                <label for="end_date" class="block text-[11px] font-medium text-slate-500 mb-1">To Date</label>
                <input type="text" id="end_date" name="end_date" value="{{ request('end_date') }}" placeholder="DD-MM-YYYY" autocomplete="off"
                       class="lead-date-picker w-full rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>
            <div>
                <label for="asign_id" class="block text-[11px] font-medium text-slate-500 mb-1">Assigned Agent</label>
                <select id="asign_id" name="asign_id"
                        class="w-full rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                    <option value="ALL">All Agents</option>
                    @foreach ($users as $key => $list)
                        <option @if (request('asign_id') == $key) selected @endif value="{{ $key }}">
                            {{ $list }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="meet" class="block text-[11px] font-medium text-slate-500 mb-1">Meeting Status</label>
                <select name="meet" id="meet"
                        class="w-full rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                    <option value="">Both</option>
                    <option value="yes" {{ request()->query('meet') == 'yes' ? 'selected' : '' }}>Yes</option>
                    <option value="no" {{ request()->query('meet') == 'no' ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div>
                <label for="action_date" class="block text-[11px] font-medium text-slate-500 mb-1">Action Date</label>
                <input type="text" id="action_date" name="action_date" value="{{ request('action_date') }}" placeholder="DD-MM-YYYY" autocomplete="off"
                       class="lead-date-picker w-full rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>
        </div>
        <div class="mt-3 flex items-center justify-end gap-2">
            <button type="button" id="resetFilterBtn"
                    class="px-3 py-1 text-xs font-medium text-slate-600 bg-white border border-slate-200 rounded-md hover:bg-slate-50 cursor-pointer">
                Reset
            </button>
            <button type="button" id="filterBtn"
                    class="px-4 py-1 text-xs font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-md shadow-sm cursor-pointer border-0">
                Apply Filters
            </button>
        </div>
        </div>
    </div>

    {{-- SECTION 3: Data Table View --}}
    <div id="tableViewContainer" class="w-full pe-2">
        <table id="registration" class="min-w-full w-full border-collapse" style="width:100% !important;">
            <thead>
                <tr>
                    <th class="no-sort checkbox-column" style="width: 38px; padding-left: 18px !important;">
                        <input type="checkbox" id="selectAllLeads" class="lead-checkbox">
                    </th>
                    <th data-col-name="name">NAME</th>
                    <th data-col-name="created_at">CREATED DATE</th>
                    <th data-col-name="contact_no">MOBILE</th>
                    <th data-col-name="next_follow_up" class="no-sort">NEXT FOLLOWUP</th>
                    <th data-col-name="last_comment" class="no-sort">LAST COMMENT</th>
                    <th data-col-name="location">LOCATION</th>
                    <th data-col-name="project">PROJECT</th>
                    <th data-col-name="source">SOURCE</th>
                    <th data-col-name="requirement">REQUIREMENT</th>
                    <th data-col-name="user_name">ASSIGNED</th>
                    <th data-col-name="user_assign_date">ASSIGNED DATE</th>
                    <th data-col-name="team_leader">TEAM LEADER</th>
                    <th data-col-name="meeting_date" class="no-sort">MEETING DATE</th>
                    <th data-col-name="status">STATUS</th>
                    <th data-col-name="lead_type">TYPE</th>
                    <th data-col-name="action_date">ACTION DATE</th>
                    @if ($currentType == 'ReverseLead')
                        <th data-col-name="reverse_remark">REVERSED REASON</th>
                        <th data-col-name="lead_rev_date">REVERSED DATE</th>
                    @endif
                    <th class="no-sort action-column text-end" style="width: 65px; min-width: 65px; padding-right: 28px !important;">ACTION</th>
                </tr>
            </thead>
        </table>
    </div>

    {{-- FLOATING BOTTOM ACTION BAR --}}
    <div id="floatingActionBar"
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center bg-[#181d27] text-white shadow-2xl rounded-full px-3 py-1.5 transition-all duration-300 transform translate-y-24 opacity-0 pointer-events-none border border-slate-700/60">
        <div class="flex items-center text-xs px-3 font-semibold text-slate-200">
            Selected: <span id="bottomSelectedCount" class="text-white font-bold ms-1.5">0</span>
        </div>
        <div class="h-4 w-[1px] bg-slate-700"></div>
        <button type="button" id="bottomAssignBtn"
                class="px-3.5 py-1.5 text-xs font-medium text-slate-200 hover:text-white flex items-center gap-1.5 hover:bg-slate-800/80 rounded-full transition cursor-pointer border-0 bg-transparent">
            <i data-lucide="send" class="h-3.5 w-3.5 text-slate-400"></i> Assign to
        </button>
        @if ($currentType != 'DumpLead')
            <div class="h-4 w-[1px] bg-slate-700"></div>
            <button type="button" id="bottomDumpBtn"
                    class="px-3.5 py-1.5 text-xs font-medium text-amber-300 hover:text-amber-200 flex items-center gap-1.5 hover:bg-slate-800/80 rounded-full transition cursor-pointer border-0 bg-transparent">
                <i data-lucide="archive" class="h-3.5 w-3.5 text-amber-400"></i> Dump Selected
            </button>
        @endif
        <div class="h-4 w-[1px] bg-slate-700"></div>
        <button type="button" id="bottomDiscardBtn"
                class="ms-2 px-3.5 py-1 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-white hover:bg-slate-100 rounded-full transition shadow-sm cursor-pointer border-0">
            Discard
        </button>
    </div>

</div>

{{-- Multiple Assign Modal (Bootstrap Modal) --}}
<div id="myModalassignmore" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-xl overflow-hidden" style="border-radius: 12px;">
            <div class="modal-header px-4 py-3 bg-slate-50 border-b border-slate-200">
                <h5 class="modal-title text-sm font-semibold text-slate-800">Assign Lead To New User</h5>
                <button type="button" class="btn-close text-xs" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @if ($currentType == 'DumpLead')
                    <form action="{{ route('dump_multiple_ass_lead') }}" method="post">
                @else
                    <form action="{{ route('multiple_ass_lead') }}" method="post">
                @endif
                {{ csrf_field() }}

                <label class="block text-xs font-medium text-slate-700 mb-1.5">Assign To User</label>
                {!! Form::select('user_assigned_id', $users, null, [
                    'class' => 'form-select text-xs w-full rounded-md border-slate-300 mb-3',
                    'id' => 'search-select',
                ]) !!}
                <div id="appendIds"></div>
                {!! Form::submit(__('Assign new user'), ['class' => 'w-full py-2 px-4 rounded-md bg-orange-500 hover:bg-orange-600 text-white font-medium text-xs shadow-sm transition border-0 cursor-pointer']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.3/js/dataTables.fixedColumns.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.3/js/fixedColumns.dataTables.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var pageType = "{{ $currentType }}";
            var updateFilterState = function() {
                var hasActiveFilter = Boolean(
                    $('#date').val() ||
                    $('#end_date').val() ||
                    $('#action_date').val() ||
                    ($('#asign_id').val() && $('#asign_id').val() !== 'ALL') ||
                    $('#meet').val()
                );

                $('#toggleSidebarBtn').toggleClass('bg-orange-50 border-orange-300 text-orange-600', hasActiveFilter);
                $('#activeFilterIndicator').toggleClass('hidden', !hasActiveFilter);
                $('#toggleSidebarBtn').attr('title', hasActiveFilter ? 'Filters active' : 'Open Filters');
            };

            if (window.flatpickr) {
                $('.lead-date-picker').each(function() {
                    flatpickr(this, {
                        allowInput: true,
                        altInput: true,
                        altFormat: 'd-m-Y',
                        dateFormat: 'Y-m-d',
                        disableMobile: true,
                        onValueUpdate: updateFilterState
                    });
                });
            }
            var outlineIcon = function(name, className) {
                var paths = {
                    ChevronsLeft: '<path d="m11 17-5-5 5-5"></path><path d="m18 17-5-5 5-5"></path>',
                    ChevronLeft: '<path d="m15 18-6-6 6-6"></path>',
                    ChevronRight: '<path d="m9 18 6-6-6-6"></path>',
                    ChevronsRight: '<path d="m6 17 5-5-5-5"></path><path d="m13 17 5-5-5-5"></path>',
                    LoaderCircle: '<path d="M21 12a9 9 0 1 1-6.22-8.56"></path>'
                };

                return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' + className + '" aria-hidden="true">' + (paths[name] || '') + '</svg>';
            };

            // Define DataTables columns matching original full column set
            let columns = [
                {
                    data: 'checkbox',
                    name: 'checkbox',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return '<div class="ps-1">' + data + '</div>';
                    }
                },
                { data: 'name', name: 'leads.name' },
                { data: 'created_at', name: 'leads.created_at' },
                { data: 'contact_no', name: 'leads.contact_no' },
                { data: 'next_follow_up', name: 'next_follow_up', orderable: false, searchable: false },
                { data: 'last_comment', name: 'last_comment', orderable: false, searchable: false },
                { data: 'location', name: 'leads.location' },
                { data: 'project', name: 'leads.project' },
                { data: 'source', name: 'leads.source' },
                { data: 'requirement', name: 'leads.requirement' },
                { data: 'user_name', name: 'users.name' },
                { data: 'user_assign_date', name: 'leads.user_assign_date' },
                { data: 'team_leader', name: 'team_leader' },
                { data: 'meeting_date', name: 'meeting_date', orderable: false, searchable: false },
                { data: 'status', name: 'Status' },
                { data: 'lead_type', name: 'leads.lead_type' },
                { data: 'action_date', name: 'leads.action_date' },
            ];

            if (pageType === 'ReverseLead') {
                columns.push({ data: 'reverse_remark', name: 'reverse_remark' });
                columns.push({ data: 'lead_rev_date', name: 'lead_rev_date' });
            }

            columns.push({
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<div class="pe-3 text-end">' + data + '</div>';
                }
            });

            let datas = {
                type: '#type',
                date: '#date',
                end_date: '#end_date',
                asign_id: '#asign_id',
                meet: '#meet',
                action_date: '#action_date',
            };

            let otherOptions = {
                order: [[2, 'desc']],
                scrollX: false,
                autoWidth: false,
                pagingType: 'full_numbers',
                dom: '<"w-full overflow-x-auto pe-3"t><"lead-table-footer text-xs text-slate-500"<"lead-footer-content"ip>>',
                language: {
                    emptyTable: "No leads found",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: outlineIcon('ChevronsLeft', 'h-3.5 w-3.5'),
                        previous: outlineIcon('ChevronLeft', 'me-1 h-3.5 w-3.5') + ' Previous',
                        next: 'Next ' + outlineIcon('ChevronRight', 'ms-1 h-3.5 w-3.5'),
                        last: outlineIcon('ChevronsRight', 'h-3.5 w-3.5')
                    }
                }
            };

            var leadTable = initDataTable("#registration", base_url + "/getLeadDataAjax", [
                [2, 'desc']
            ], columns, datas, otherOptions);

            $("#registration").addClass("dt-initialized");
            $("#tableViewContainer").removeClass("opacity-0");

            // Recalculate and synchronize column widths perfectly
            if (leadTable) {
                leadTable.on('init.dt draw.dt', function() {
                    setTimeout(function() {
                        leadTable.columns.adjust();
                    }, 50);
                });
                $(window).on('resize', function() {
                    leadTable.columns.adjust();
                });
            }

            // 1. SEARCH: Real-time debounced search on input
            var searchTimeout;
            $('#leadSearchInput').on('keyup input', function() {
                clearTimeout(searchTimeout);
                var val = this.value;
                searchTimeout = setTimeout(function() {
                    leadTable.search(val).draw();
                }, 300);
            });

            // 2. FILTER OFFCANVAS: indicate when the filter panel is open.
            $('#leadFilterOffcanvas').on('show.bs.offcanvas', function() {
                updateFilterState();
            }).on('hidden.bs.offcanvas', function() {
                updateFilterState();
            });

            // Apply filters
            $('#filterBtn').on('click', function() {
                updateFilterState();
                leadTable.ajax.reload(null, true);
                var filterOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('leadFilterOffcanvas'));
                if (filterOffcanvas) {
                    filterOffcanvas.hide();
                }
            });

            // Keep active state in sync; filters are applied only by the button.
            $('#date, #end_date, #asign_id, #meet, #action_date').on('change', function() {
                updateFilterState();
            });

            // Reset filters
            $('#resetFilterBtn').on('click', function() {
                $('.lead-date-picker').each(function() {
                    if (this._flatpickr) {
                        this._flatpickr.clear();
                    }
                });
                $('#asign_id').val('ALL');
                $('#meet').val('');
                $('#leadSearchInput').val('');
                updateFilterState();
                leadTable.search('');
                leadTable.ajax.reload(null, true);
            });

            updateFilterState();

            // 3. REFRESH: Reload table with subtle spin feedback
            $('#refreshTableBtn').on('click', function() {
                var icon = $('#refreshIcon');
                icon.addClass('animate-spin');
                leadTable.ajax.reload(function() {
                    setTimeout(function() {
                        icon.removeClass('animate-spin');
                    }, 400);
                }, false);
            });

            // Export Actions (Excel and CSV)
            $('#exportExcelBtn').on('click', function(e) {
                e.preventDefault();
                if ($('.buttons-excel').length) {
                    $('.buttons-excel').click();
                } else if (leadTable && leadTable.button && leadTable.button('.buttons-excel').length) {
                    leadTable.button('.buttons-excel').trigger();
                } else {
                    window.location.href = "{{ url('leads/bulk/export') }}";
                }
            });

            $('#exportCsvBtn').on('click', function(e) {
                e.preventDefault();
                if ($('.buttons-csv').length) {
                    $('.buttons-csv').click();
                } else if (leadTable && leadTable.button && leadTable.button('.buttons-csv').length) {
                    leadTable.button('.buttons-csv').trigger();
                } else {
                    window.location.href = "{{ url('leads/bulk/export') }}";
                }
            });

            // Select All Checkbox Handler
            $('#selectAllLeads').on('change', function() {
                var isChecked = this.checked;
                $('#registration tbody input[name="moreassign"]').prop('checked', isChecked);
                updateBulkBar();
            });

            // Individual Row Checkbox Handler
            $(document).on('change', '#registration tbody input[name="moreassign"]', function() {
                updateBulkBar();
            });

            // Update Floating Bottom Action Bar
            function updateBulkBar() {
                var checkedBoxes = $('#registration tbody input[name="moreassign"]:checked');
                var count = checkedBoxes.length;
                var totalBoxes = $('#registration tbody input[name="moreassign"]').length;

                if (count > 0 && count === totalBoxes) {
                    $('#selectAllLeads').prop('checked', true);
                } else {
                    $('#selectAllLeads').prop('checked', false);
                }

                if (count > 0) {
                    $('#bottomSelectedCount').text(count);
                    $('#floatingActionBar')
                        .removeClass('translate-y-24 opacity-0 pointer-events-none')
                        .addClass('translate-y-0 opacity-100 pointer-events-auto');
                } else {
                    $('#floatingActionBar')
                        .addClass('translate-y-24 opacity-0 pointer-events-none')
                        .removeClass('translate-y-0 opacity-100 pointer-events-auto');
                }
            }

            // Discard Selection Handler
            $(document).on('click', '#bottomDiscardBtn', function() {
                $('#selectAllLeads').prop('checked', false);
                $('#registration tbody input[name="moreassign"]').prop('checked', false);
                updateBulkBar();
            });

            // Trigger Bulk Assign
            $(document).on('click', '#bottomAssignBtn', function() {
                var selected = [];
                $('#registration tbody input[name="moreassign"]:checked').each(function() {
                    var v = $(this).val();
                    if (selected.indexOf(v) === -1) {
                        selected.push(v);
                    }
                });

                if (selected.length === 0) {
                    alert('Please select at least one lead.');
                    return;
                }

                $('#appendIds').html('');
                selected.forEach(function(id) {
                    $('#appendIds').append('<input type="hidden" name="lead_ids[]" value="' + id + '">');
                });

                var modalEl = document.getElementById('myModalassignmore');
                var bsModal = new bootstrap.Modal(modalEl);
                bsModal.show();
            });

            // Single Row Assign
            $(document).on('click', '.assignNewUserSingle', function(e) {
                e.preventDefault();
                var leadId = $(this).data('lead-id');
                $('#appendIds').html('<input type="hidden" name="lead_ids[]" value="' + leadId + '">');
                var modalEl = document.getElementById('myModalassignmore');
                var bsModal = new bootstrap.Modal(modalEl);
                bsModal.show();
            });

            // Trigger Bulk Dump
            $(document).on('click', '#bottomDumpBtn', function() {
                var input = [];
                $('#registration tbody input[name="moreassign"]:checked').each(function() {
                    var v = $(this).val();
                    if (input.indexOf(v) === -1) {
                        input.push(v);
                    }
                });

                if (input.length === 0) {
                    alert('Please select at least one lead.');
                    return;
                }

                if (!confirm('Are you sure you want to move the selected leads to dump?')) {
                    return;
                }

                var thisBtn = $(this);
                var originalHtml = thisBtn.html();
                thisBtn.html(outlineIcon('LoaderCircle', 'me-1 inline-block h-3.5 w-3.5 animate-spin') + ' Dumping...').prop('disabled', true);

                $.ajax({
                    url: '{{ url("send_to_dump_lead") }}',
                    method: 'POST',
                    data: {
                        inputs: input,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            alert(response.message || 'Leads successfully moved to dump.');
                            leadTable.draw();
                            $('#selectAllLeads').prop('checked', false);
                            updateBulkBar();
                        } else {
                            alert(response.message || 'Error occurred.');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Something went wrong. Please try again.');
                    },
                    complete: function() {
                        thisBtn.html(originalHtml).prop('disabled', false);
                    }
                });
            });

            // Whenever DataTables redraws, refresh Selection Bar
            leadTable.on('draw', function() {
                updateBulkBar();
            });
        });
    </script>
@endpush
