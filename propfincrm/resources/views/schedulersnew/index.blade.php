@php
    $pageTitle = 'Scheduler List';
@endphp
@section('page-title')
    {{ $pageTitle }}
@endsection
@extends('layouts.master')
@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/5.0.3/css/fixedColumns.dataTables.css">
@endsection
@section('content')
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
        <div id="schedulerToast" class="toast align-items-center border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fs-6" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
    <div class="leads">
        <div class="row">
            <div class="col-12 col-md-2 pe-0">
                <div class="sidebar-filter shadow">
                    <h5 class="mt-0">Filters</h5>

                    <div class="col-12 mb-0">
                        <div class="form-group">
                            <label>From</label>
                            <input type="date" id="filter_from_date" class="form-control">
                        </div>
                    </div>

                    <div class="col-12 mb-0">
                        <div class="form-group">
                            <label>To</label>
                            <input type="date" id="filter_to_date" class="form-control">
                        </div>
                    </div>

                    <div class="col-12 mb-0">
                        <div class="form-group">
                            <label>Assign</label>
                            <select class="form-control" id="filter_user_id">
                                <option value="">All</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 mb-0">
                        <div class="form-group">
                            <label>Project</label>
                            <select class="form-control" id="filter_project">
                                <option value="">All</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->project_name }}">{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-12 mb-0">
                        <button type="button" class="btn btn-primary w-100" id="filterBtn">
                            Submit
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-10 table-content">
                <div class="col-md-12 p-0">

                    <div class="d-flex justify-content-between gap-4 w-100 new-lead-bulk mb-3">
                        <div class="col-md-6 d-flex gap-3 p-0">
                            <a href="{{ route('scheduler.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus me-2"></i>New Scheduler
                            </a>
                        </div>

                        <div class="col-md-6 d-flex gap-3 justify-content-end">
                            <a class="filter-box d-flex align-items-center" id="toggleSidebarBtn">
                                <i class="fa fa-filter"></i>
                            </a>
                        </div>

                    </div>

                    <div class="card main-container p-5">
                        <table id="schedulerTable" class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Assigned Projects</th>
                                    <th>Assigned Users</th>
                                    <th>Total Users</th>
                                    <th>Created At</th>
                                    <th class="action-column">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            var schedulerTable = $('#schedulerTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                fixedColumns: {
                    start: 1
                },
                ajax: {
                    url: base_url + '/scheduler/list/ajax',
                    data: function(d) {
                        d.filter_from_date = $('#filter_from_date').val();
                        d.filter_to_date = $('#filter_to_date').val();
                        d.filter_user_id = $('#filter_user_id').val();
                        d.filter_project = $('#filter_project').val();
                    }
                },
                order: [
                    [7, 'desc']
                ],
                columns: [
                    {
                        data: 'from_date',
                        name: 'from_date'
                    },
                    {
                        data: 'to_date',
                        name: 'to_date'
                    },
                    {
                        data: 'start_time',
                        name: 'start_time'
                    },
                    {
                        data: 'end_time',
                        name: 'end_time'
                    },
                    {
                        data: 'projects_names',
                        name: 'projects_names',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_names',
                        name: 'user_names',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_users',
                        name: 'total_users',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                language: {
                    lengthMenu: '_MENU_', 
                    search: '', 
                    searchPlaceholder: 'Search schedulers...', 
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    paginate: {
                        previous: 'Prev',
                        next: 'Next'
                    }
                }

            });

            $(document).on('click', '#filterBtn', function() {
                schedulerTable.draw();
            });

            $('#toggleSidebarBtn').click(function() {
                $('.sidebar-filter').toggle();
                $('.table-content').toggleClass("col-md-12");
                $('.table-content').toggleClass("col-md-10");

                const icon = $(this).find('i');
                icon.toggleClass('fa-filter fa-times');
            });

            $(document).on('click', '.deleteScheduler', function(e) {
                e.preventDefault();
                if (!confirm('Are you sure you want to delete this scheduler?')) return;

                let thisBtn = $(this);
                let url = thisBtn.data('url');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        thisBtn.html('<i class="fa fa-spinner fa-spin me-2"></i>Deleting...');
                    },
                    success: function(res) {
                        if (res.status === 200) {
                            schedulerTable.draw();
                            showToast(res.message, 'success'); 
                        } else {
                            showToast(res.message || 'Something went wrong.',
                            'error'); 
                        }
                    },
                    error: function(xhr) {
                        showToast('Server error: ' + xhr.status, 'error'); 
                    },
                    complete: function() {
                        thisBtn.html('<i class="fa fa-trash me-2"></i>Delete');
                    }
                });
            });

            function showToast(message, type) {
                var toast = $('#schedulerToast');
                var bgClass = type === 'success' ? 'bg-success text-white' : 'bg-danger text-white';
                toast.removeClass('bg-success bg-danger text-white').addClass(bgClass);
                $('#toastMessage').text(message);

                var bsToast = new bootstrap.Toast(toast[0], {
                    delay: 3000
                });
                bsToast.show();
            }
        });
    </script>
@endsection
