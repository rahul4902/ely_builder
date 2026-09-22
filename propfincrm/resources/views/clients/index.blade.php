@section('page-title', 'Clients')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')
@extends('layouts.master')

@section('styles')
<style>
#clients-page .dataTables_filter,
#clients-page .dataTables_length,
#clients-page .dt-search,
#clients-page .dt-length { display:none!important }
#clients-page .dataTables_wrapper,
#clients-page .dataTables_scroll { width:100%!important }
#clients-page table.dataTable { width:100%!important;margin:0!important;border-collapse:collapse!important }
#clients-page table.dataTable thead th { height:32px;padding:5px 14px!important;background:#f7f9fc!important;color:#94a3b8!important;border-top:0!important;border-bottom:1px solid #e2e8f0!important;font-size:10px!important;font-weight:600!important;letter-spacing:.05em;text-transform:uppercase;vertical-align:middle!important;white-space:nowrap }
#clients-page table.dataTable tbody td { height:34px;padding:5px 14px!important;color:#334155;font-size:12px;border-bottom:1px solid #f1f5f9!important;border-top:none!important;line-height:16px!important;vertical-align:middle }
#clients-page table.dataTable tbody tr:hover td { background:#fafbfc }
#clients-page table.dataTable tbody td:first-child a { font-weight:600!important;color:#334155!important }
#clients-page table.dataTable tbody td:first-child a:hover { color:#ea580c!important;text-decoration:none }
#clients-page .crm-dt-footer { padding:10px 16px;border-top:1px solid #e2e8f0 }
#clients-page .crm-dt-footer-inner { display:flex;align-items:center;justify-content:space-between;gap:16px;width:100% }
#clients-page .dataTables_info,
#clients-page .dt-info { padding:0!important;color:#64748b!important;font-size:12px!important;font-weight:400!important;line-height:18px!important;white-space:nowrap }
#clients-page .dataTables_paginate,
#clients-page .dt-paging { display:flex!important;align-items:center;gap:3px;margin:0!important;padding:0!important;float:none!important }
#clients-page .dataTables_paginate .paginate_button,
#clients-page .dt-paging .dt-paging-button { box-sizing:border-box;display:inline-flex!important;align-items:center!important;justify-content:center!important;min-width:30px;height:30px;margin:0 1px!important;padding:0 9px!important;border:1px solid #e2e8f0!important;border-radius:6px!important;background:#fff!important;color:#475569!important;font-size:12px!important;font-weight:500;line-height:1!important;box-shadow:none!important;cursor:pointer;text-decoration:none!important }
#clients-page .dataTables_paginate .paginate_button:hover:not(.current):not(.disabled),
#clients-page .dt-paging .dt-paging-button:hover:not(.current):not(.disabled) { background:#f8fafc!important;border-color:#cbd5e1!important;color:#0f172a!important }
#clients-page .dataTables_paginate .paginate_button.current,
#clients-page .dataTables_paginate .paginate_button.current:hover,
#clients-page .dt-paging .dt-paging-button.current,
#clients-page .dt-paging .dt-paging-button.current:hover { border-color:#f97316!important;background:#f97316!important;color:#fff!important;font-weight:600;box-shadow:0 1px 2px rgba(249,115,22,.25)!important }
#clients-page .dataTables_paginate .paginate_button.disabled,
#clients-page .dt-paging .dt-paging-button.disabled { opacity:.4;cursor:not-allowed;background:#f8fafc!important;color:#94a3b8!important }
</style>
@endsection

@section('content')
<div id="clients-page">

    {{-- Toolbar --}}
    <div class="crm-toolbar">
        <div class="crm-toolbar-left">
            <div class="crm-search-wrap">
                <i class="fas fa-search"></i>
                <input type="search" id="clients-search" class="crm-search" placeholder="{{ __('Search clients…') }}">
            </div>
        </div>
        <div class="crm-toolbar-right">
            @if(Entrust::can('client-create'))
            <a href="{{ route('clients.create') }}" class="crm-btn crm-btn-primary">
                <i class="fas fa-plus"></i>
                {{ __('New Client') }}
            </a>
            @endif
        </div>
    </div>

    {{-- DataTable --}}
    <table id="clients-table" class="w-full">
        <thead>
            <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Company') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Number') }}</th>
                <th class="no-sort text-end"></th>
            </tr>
        </thead>
    </table>

    {{-- Footer injected by DataTable dom --}}
</div>
@endsection

@push('scripts')
<script>
$(function () {
    var columns = [
        { data: 'namelink',        name: 'name' },
        { data: 'company_name',    name: 'company_name' },
        { data: 'email',           name: 'email' },
        { data: 'primary_number',  name: 'primary_number' },
        { data: 'action',          name: 'action', orderable: false, searchable: false }
    ];

    var table = $('#clients-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url:  '{!! route('clients.data') !!}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        },
        columns: columns,
        order: [],
        scrollX: false,
        autoWidth: false,
        pagingType: 'full_numbers',
        dom: '<"w-full"t><"crm-dt-footer"><"crm-dt-footer-inner"ip>',
        language: {
            emptyTable:  '{{ __('No clients found') }}',
            info:        '{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}',
            infoEmpty:   '{{ __('Showing 0 to 0 of 0 entries') }}',
            paginate: {
                previous: '{{ __('Previous') }}',
                next:     '{{ __('Next') }}'
            }
        }
    });

    $('#clients-search').on('input', function () {
        table.search(this.value).draw();
    });
});
</script>
@endpush
