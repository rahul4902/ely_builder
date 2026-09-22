@section('page-title', 'Requirement Master')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')
@extends('layouts.master')

@section('styles')
<style>
    #requirement-master-page { min-height: calc(100vh - 40px); background: #fff; font-family: 'Outfit', sans-serif; }
    #requirement-master-page .master-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 20px; border-bottom: 1px solid #e2e8f0; }
    #requirement-master-page .master-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 34px; padding: 0 12px; border: 1px solid #dbe3ee; border-radius: 6px; background: #fff; color: #475569; font-size: 12px; font-weight: 500; text-decoration: none; cursor: pointer; transition: all .15s ease; }
    #requirement-master-page .master-btn:hover { border-color: #cbd5e1; background: #f8fafc; color: #1e293b; }
    #requirement-master-page .master-btn-primary { border-color: #f97316; background: #f97316; color: #fff; font-weight: 600; }
    #requirement-master-page .master-btn-primary:hover { border-color: #ea580c; background: #ea580c; color: #fff; }
    #requirement-master-page .dataTables_filter, #requirement-master-page .dataTables_length, #requirement-master-page .dt-search, #requirement-master-page .dt-length { display: none !important; }
    #requirement-master-page .dataTables_wrapper, #requirement-master-page .dataTables_scroll { width: 100% !important; margin: 0 !important; }
    #requirement-master-page table.dataTable { width: 100% !important; margin: 0 !important; border-collapse: collapse !important; }
    #requirement-master-page .dataTables_scrollHead table thead th, #requirement-master-page table.dataTable thead th { height: 36px; padding: 8px 16px !important; border: 0 !important; border-bottom: 1px solid #e2e8f0 !important; background: #f7f9fc !important; color: #94a3b8 !important; font-size: 10.5px !important; font-weight: 600 !important; letter-spacing: .05em; text-transform: uppercase; white-space: nowrap; vertical-align: middle; }
    #requirement-master-page table.dataTable tbody td { height: 44px; padding: 8px 16px !important; border: 0 !important; border-bottom: 1px solid #f1f5f9 !important; color: #334155; font-size: 12.5px; vertical-align: middle; white-space: nowrap; }
    #requirement-master-page table.dataTable tbody tr:hover td { background: #fafbfc; }
    #requirement-master-page .master-footer { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 20px; border-top: 1px solid #e2e8f0; }
    #requirement-master-page .dataTables_info, #requirement-master-page .dt-info { padding: 0 !important; color: #64748b; font-size: 12px; }
    #requirement-master-page .dataTables_paginate, #requirement-master-page .dt-paging { display: flex; align-items: center; gap: 3px; margin-left: auto; padding: 0 !important; }
    #requirement-master-page .dataTables_paginate .paginate_button, #requirement-master-page .dt-paging .dt-paging-button { box-sizing: border-box; display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 30px; margin: 0 !important; padding: 0 8px !important; border: 1px solid #dbe3ee !important; border-radius: 6px !important; background: #fff !important; color: #475569 !important; font-size: 12px; line-height: 16px; box-shadow: none !important; cursor: pointer; }
    #requirement-master-page .dataTables_paginate .paginate_button.current, #requirement-master-page .dataTables_paginate .paginate_button.current:hover, #requirement-master-page .dt-paging .dt-paging-button.current, #requirement-master-page .dt-paging .dt-paging-button.current:hover { border-color: #f97316 !important; background: #f97316 !important; color: #fff !important; -webkit-text-fill-color: #fff !important; font-weight: 600; }
    #requirement-master-page .dataTables_paginate .paginate_button.disabled, #requirement-master-page .dt-paging .dt-paging-button.disabled { opacity: .45; cursor: not-allowed; }
    @media (max-width: 640px) {
        #requirement-master-page .master-toolbar { padding: 12px 14px; flex-direction: column; align-items: stretch; }
        #requirement-master-page .master-footer { flex-direction: column; align-items: flex-start; }
        #requirement-master-page .dataTables_paginate { margin-left: 0; max-width: 100%; overflow-x: auto; }
    }
</style>
@endsection

@section('content')
<div id="requirement-master-page">
    @if(isset($msg))
        <div class="m-4 flex items-center justify-between rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-circle" class="h-4 w-4 text-amber-600"></i>
                <span>{{ $msg }}</span>
            </div>
            <button type="button" class="btn-close text-xs" onclick="this.parentElement.remove()"></button>
        </div>
    @endif

    <div class="master-toolbar">
        <div class="flex items-center gap-2">
            <div class="relative w-64 max-w-full">
                <i data-lucide="search" class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400"></i>
                <input id="requirement-search" type="search" placeholder="Search requirements..." autocomplete="off" class="w-full rounded-lg border border-slate-200 bg-slate-50/70 py-1.5 pl-8 pr-3 text-xs text-slate-700 transition hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>
            <button id="requirement-refresh" class="inline-flex h-[34px] w-[34px] items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800" title="Refresh Table">
                <i data-lucide="refresh-cw" id="requirement-refresh-icon" class="h-3.5 w-3.5"></i>
            </button>
        </div>
        <div class="flex items-center gap-2">
            <details class="relative">
                <summary class="master-btn list-none cursor-pointer">
                    <i data-lucide="layers" class="h-3.5 w-3.5"></i> Masters <i data-lucide="chevron-down" class="h-3 w-3 ms-1 text-slate-400"></i>
                </summary>
                <div class="absolute right-0 z-20 mt-1 w-40 overflow-hidden rounded-lg border border-slate-200 bg-white py-1 shadow-lg text-xs">
                    <a class="block px-3 py-2 text-slate-700 hover:bg-slate-50 no-underline" href="{{ url('project_list') }}">Project Master</a>
                    <a class="block px-3 py-2 text-slate-700 hover:bg-slate-50 no-underline" href="{{ url('source_list') }}">Source Master</a>
                    <a class="block px-3 py-2 text-slate-700 hover:bg-slate-50 no-underline" href="{{ url('budget_list') }}">Budget Master</a>
                </div>
            </details>
            <button class="master-btn master-btn-primary" data-bs-toggle="modal" data-bs-target="#requirementModal">
                <i data-lucide="plus" class="h-3.5 w-3.5"></i> Add Requirement
            </button>
        </div>
    </div>

    <div class="w-full overflow-x-auto">
        <table id="requirement-table" class="w-full">
            <thead>
                <tr>
                    <th>Requirement Name</th>
                    <th class="no-sort text-end" style="width: 70px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requirement_lists as $requirement)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-emerald-50 text-emerald-600">
                                    <i data-lucide="clipboard-list" class="h-3.5 w-3.5"></i>
                                </div>
                                <span class="font-medium text-slate-800">{{ $requirement->requirement_name }}</span>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="dropdown text-end">
                                <button type="button" class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition border-0 bg-transparent cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                    <i class="fa-solid fa-ellipsis text-sm"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 140px;">
                                    <li>
                                        <button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-slate-700 hover:bg-slate-50 btn-edit-requirement" data-id="{{ $requirement->id }}" data-name="{{ $requirement->requirement_name }}">
                                            <i class="fa-regular fa-pen-to-square me-2 text-slate-400"></i> Edit
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider my-1 border-slate-100"></li>
                                    <li>
                                        <form action="{{ url('delete_requirement') }}" method="post" class="m-0 p-0">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $requirement->id }}">
                                            <button type="submit" class="dropdown-item py-1.5 px-3 flex items-center text-rose-600 hover:bg-rose-50 w-full text-start border-0 bg-transparent" onclick="return confirm('Do you want to delete this requirement?')">
                                                <i class="fa-regular fa-trash-can me-2 text-rose-500"></i> Delete
                                            </button>
                                        </form>
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

{{-- Add Requirement Modal using shared <x-form.input> --}}
<div class="modal fade" id="requirementModal" tabindex="-1" aria-labelledby="requirementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-xl">
            <form action="{{ url('save_requirement_name') }}" method="post">
                @csrf
                <div class="modal-header border-b border-slate-200 px-5 py-4">
                    <h5 class="modal-title text-sm font-semibold text-slate-800" id="requirementModalLabel">Add Requirement</h5>
                    <button type="button" class="btn-close text-xs" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5 space-y-3">
                    <x-form.input
                        layout="standard"
                        label="Requirement Name *"
                        name="requirement_name"
                        id="add_requirement_name"
                        type="text"
                        placeholder="e.g. 2 BHK Apartment"
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                        required
                    />
                </div>
                <div class="modal-footer border-t border-slate-200 px-5 py-3">
                    <button type="button" class="master-btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="master-btn master-btn-primary">Save Requirement</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Requirement Modal using shared <x-form.input> --}}
<div class="modal fade" id="editRequirementModal" tabindex="-1" aria-labelledby="editRequirementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-xl">
            <form action="{{ url('save_edit_requirement') }}" method="post">
                @csrf
                <input type="hidden" name="requirement_edit_id" id="edit_requirement_id">
                <div class="modal-header border-b border-slate-200 px-5 py-4">
                    <h5 class="modal-title text-sm font-semibold text-slate-800" id="editRequirementModalLabel">Edit Requirement</h5>
                    <button type="button" class="btn-close text-xs" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5 space-y-3">
                    <x-form.input
                        layout="standard"
                        label="Requirement Name *"
                        name="requirement_name"
                        id="edit_requirement_name"
                        type="text"
                        placeholder="Requirement Name"
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-xs text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                        required
                    />
                </div>
                <div class="modal-footer border-t border-slate-200 px-5 py-3">
                    <button type="button" class="master-btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="master-btn master-btn-primary">Update Requirement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    var table = $('#requirement-table').DataTable({
        pagingType: 'full_numbers',
        order: [],
        pageLength: 10,
        autoWidth: false,
        columnDefs: [
            { targets: 'no-sort', orderable: false, searchable: false }
        ],
        dom: '<"w-full overflow-x-auto"t><"master-footer"<"master-footer-content"ip>>',
        language: {
            emptyTable: 'No requirements found',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 to 0 of 0 entries',
            paginate: { previous: 'Previous', next: 'Next' }
        }
    });

    $('#requirement-search').on('input', function() {
        table.search(this.value).draw();
    });

    $('#requirement-refresh').on('click', function() {
        var icon = $('#requirement-refresh-icon');
        icon.addClass('animate-spin');
        $('#requirement-search').val('');
        table.search('').draw();
        setTimeout(function() {
            icon.removeClass('animate-spin');
        }, 300);
    });

    $(document).on('click', '.btn-edit-requirement', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#edit_requirement_id').val(id);
        $('#edit_requirement_name').val(name);

        new bootstrap.Modal(document.getElementById('editRequirementModal')).show();
    });

    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>
@endpush
