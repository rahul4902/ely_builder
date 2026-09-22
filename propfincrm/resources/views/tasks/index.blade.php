@section('page-title', 'Tasks')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')
@extends('layouts.master')

@section('styles')
<style>
#tasks-page .dataTables_filter,#tasks-page .dataTables_length{display:none!important}#tasks-page .dataTables_wrapper,#tasks-page .dataTables_scroll{width:100%!important}#tasks-page table.dataTable{width:100%!important;margin:0!important;border-collapse:collapse!important}#tasks-page table.dataTable thead th{height:32px;padding:5px 12px!important;background:#f7f9fc!important;color:#94a3b8!important;border-top:0!important;border-bottom:1px solid #e2e8f0!important;font-size:10px!important;font-weight:600!important;letter-spacing:.05em;text-transform:uppercase;vertical-align:middle!important}#tasks-page table.dataTable tbody td{height:32px;padding:4px 12px!important;color:#334155;font-size:12px;border-bottom:1px solid #f1f5f9!important;line-height:16px!important;vertical-align:middle}#tasks-page table.dataTable tbody td:first-child a{font-weight:600!important;color:#334155!important}#tasks-page table.dataTable tbody td:first-child a:hover{color:#ea580c!important}#tasks-page table.dataTable tbody tr:hover td{background:#fafbfc}#tasks-page .crm-dt-footer{padding:12px 24px;border-top:1px solid #e2e8f0}#tasks-page .crm-dt-footer-inner{display:grid;grid-template-columns:max-content max-content;align-items:center;justify-content:center;column-gap:24px;min-height:30px}#tasks-page .crm-dt-footer-inner>*{float:none!important;margin:0!important;width:auto!important}#tasks-page .crm-dt-footer .dataTables_info,#tasks-page .crm-dt-footer .dt-info{padding:0!important;color:#64748b!important;font-size:12px!important;font-weight:400!important;line-height:18px!important}#tasks-page .dataTables_paginate{display:flex;align-items:center;gap:3px;margin:0!important;padding:0!important}#tasks-page .dataTables_paginate .paginate_button{box-sizing:border-box;display:inline-flex;align-items:center;justify-content:center;min-width:30px;height:30px;margin:0 1px!important;padding:0 9px!important;border:1px solid #e2e8f0!important;border-radius:6px!important;background:#fff!important;color:#475569!important;font-size:12px!important;font-weight:500;line-height:1!important;box-shadow:none!important}#tasks-page .dataTables_paginate .paginate_button:hover:not(.current):not(.disabled){background:#f8fafc!important;border-color:#cbd5e1!important;color:#0f172a!important}#tasks-page .dataTables_paginate .paginate_button.current{border-color:#ea580c!important;background:#f97316!important;color:#fff!important;font-weight:600;box-shadow:0 1px 2px rgba(249,115,22,.25)!important}#tasks-page .dataTables_paginate .paginate_button.disabled{opacity:.4;cursor:not-allowed;background:#f8fafc!important;color:#94a3b8!important}
</style>
@endsection

@section('content')
<div id="tasks-page" class="crm-dt-page">
    <div class="crm-toolbar">
        <div class="crm-toolbar-left">
            <x-form.input
                layout="standard"
                field-class="relative w-64"
                name="tasks_search"
                id="tasks-search"
                type="search"
                placeholder="Search tasks"
                class="w-full rounded-lg border border-slate-200 bg-slate-50/70 py-1.5 pl-3 pr-3 text-xs text-slate-700 focus:bg-white focus:outline-none focus:ring-1 focus:ring-orange-500"
            />
        </div>
        <div class="crm-toolbar-right">
            <a href="{{ route('tasks.create') }}" class="crm-btn crm-btn-primary">
                <i class="fa fa-plus"></i> {{ __('New Task') }}
            </a>
        </div>
    </div>

    <table id="tasks-table" class="w-full">
        <thead>
            <tr>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Created at') }}</th>
                <th>{{ __('Deadline') }}</th>
                <th>{{ __('Assigned') }}</th>
                <th class="no-sort">{{ __('Action') }}</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    var table = $('#tasks-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route("tasks.data") !!}',
        columns: [
            { data: 'titlelink',        name: 'title' },
            { data: 'created_at',       name: 'created_at' },
            { data: 'deadline',         name: 'deadline' },
            { data: 'user_assigned_id', name: 'user_assigned_id' },
            { data: 'action',           name: 'action', orderable: false, searchable: false },
        ],
        order: [],
        scrollX: false,
        autoWidth: false,
        pagingType: 'full_numbers',
        dom: '<"w-full"t><"crm-dt-footer"<"crm-dt-footer-inner"ip>>',
        language: {
            emptyTable: 'No tasks found',
            info:       'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty:  'Showing 0 to 0 of 0 entries',
            paginate:   { previous: 'Previous', next: 'Next' }
        }
    });

    $('#tasks-search').on('input', function () {
        table.search(this.value).draw();
    });
});
</script>
@endpush
