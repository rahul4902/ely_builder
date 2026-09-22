@section('page-title', 'Departments')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')
@extends('layouts.master')

@section('styles')
<style>
    #departments-page { min-height:calc(100vh - 40px); font-family:'Outfit',sans-serif; }
    #departments-page .dataTables_filter,
    #departments-page .dataTables_length,
    #departments-page .dt-search,
    #departments-page .dt-length { display:none !important; }
    #departments-page .dataTables_wrapper,
    #departments-page .dataTables_scroll { width:100% !important; }
    #departments-page table.dataTable { width:100% !important; margin:0 !important; border-collapse:collapse !important; }
    #departments-page table.dataTable thead th { height:32px; padding:5px 14px !important; background:#f7f9fc !important; color:#94a3b8 !important; border-top:0 !important; border-bottom:1px solid #e2e8f0 !important; font-size:10px !important; font-weight:600 !important; letter-spacing:.05em; text-transform:uppercase; vertical-align:middle !important; white-space:nowrap; }
    #departments-page table.dataTable tbody td { height:34px; padding:5px 14px !important; color:#334155; font-size:12px; border-bottom:1px solid #f1f5f9 !important; border-top:none !important; line-height:16px !important; vertical-align:middle; }
    #departments-page table.dataTable tbody tr:hover td { background:#fafbfc; }
    #departments-page table.dataTable tbody td:first-child { font-weight:600; color:#1e293b; }
    #departments-page .dataTables_info,
    #departments-page .dt-info { padding:0 !important; color:#64748b !important; font-size:12px !important; font-weight:400 !important; line-height:18px !important; white-space:nowrap; }
    #departments-page .dataTables_paginate,
    #departments-page .dt-paging { display:flex !important; align-items:center; gap:3px; margin:0 !important; padding:0 !important; float:none !important; }
    #departments-page .dataTables_paginate .paginate_button,
    #departments-page .dt-paging .dt-paging-button { box-sizing:border-box; display:inline-flex !important; align-items:center !important; justify-content:center !important; min-width:30px; height:30px; margin:0 1px !important; padding:0 9px !important; border:1px solid #e2e8f0 !important; border-radius:6px !important; background:#fff !important; color:#475569 !important; font-size:12px !important; font-weight:500; line-height:1 !important; box-shadow:none !important; cursor:pointer; text-decoration:none !important; }
    #departments-page .dataTables_paginate .paginate_button:hover:not(.current):not(.disabled),
    #departments-page .dt-paging .dt-paging-button:hover:not(.current):not(.disabled) { background:#f8fafc !important; border-color:#cbd5e1 !important; color:#0f172a !important; }
    #departments-page .dataTables_paginate .paginate_button.current,
    #departments-page .dataTables_paginate .paginate_button.current:hover,
    #departments-page .dt-paging .dt-paging-button.current { border-color:#f97316 !important; background:#f97316 !important; color:#fff !important; font-weight:600; box-shadow:0 1px 2px rgba(249,115,22,.25) !important; }
    #departments-page .dataTables_paginate .paginate_button.disabled,
    #departments-page .dt-paging .dt-paging-button.disabled { opacity:.4; cursor:not-allowed; background:#f8fafc !important; color:#94a3b8 !important; }
    @media(max-width:640px) {
        #departments-page .crm-toolbar { padding:10px 14px; }
        #departments-page .crm-search-wrap { width:100%; }
    }
</style>
@endsection

@section('content')
<div id="departments-page">

    {{-- Toolbar --}}
    <div class="crm-toolbar">
        <div class="crm-toolbar-left">
            <div class="crm-search-wrap">
                <i class="fa fa-search"></i>
                <input id="dept-search" class="crm-search" type="search" placeholder="Search departments">
            </div>
        </div>
        <div class="crm-toolbar-right">
            <a href="{{ route('departments.create') }}" class="crm-btn crm-btn-primary">
                <i class="fa fa-plus"></i> Add Department
            </a>
        </div>
    </div>

    {{-- Table --}}
    <table id="departments-table" class="w-full">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th class="no-sort" style="width:50px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($department as $dep)
                <tr>
                    <td>{{ $dep->name }}</td>
                    <td>{{ Str_limit($dep->description, 80) ?: '—' }}</td>
                    <td>
                        <div class="crm-row-actions">
                            <div class="dropdown">
                                <button type="button"
                                        class="crm-ellipsis-btn dropdown-toggle"
                                        data-bs-toggle="dropdown"
                                        data-bs-auto-close="true"
                                        aria-expanded="false">
                                    <i class="fa fa-ellipsis"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end crm-action-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('departments.edit', $dep->id) }}">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST"
                                              action="{{ route('departments.destroy', $dep->id) }}"
                                              onsubmit="return confirm('Delete department \'{{ addslashes($dep->name) }}\'? This cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-slate-400" style="padding:24px!important;">
                        No departments found.
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
    var table = $('#departments-table').DataTable({
        pagingType: 'full_numbers',
        order: [],
        autoWidth: false,
        columnDefs: [{ orderable: false, targets: 2 }],
        dom: '<"w-full overflow-x-auto"t><"crm-dt-footer"><"crm-dt-footer-inner"ip>',
        language: {
            emptyTable:  'No departments found.',
            info:        'Showing _START_ to _END_ of _TOTAL_ departments',
            infoEmpty:   'Showing 0 to 0 of 0 departments',
            paginate:    { previous: 'Previous', next: 'Next' }
        }
    });

    $('#dept-search').on('input', function () {
        table.search(this.value).draw();
    });
});
</script>
@endpush
