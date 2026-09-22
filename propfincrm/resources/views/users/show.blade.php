@extends('layouts.master')
@section('page-title', 'Profile')
@section('main-class', 'p-4')

@section('styles')
<style>
#user-show-page .nav-pills { border-bottom: 1px solid #e2e8f0; gap: 2px; padding: 0 4px; flex-wrap: nowrap; overflow-x: auto; }
#user-show-page .nav-pills .nav-link { padding: 7px 14px; border: none; border-bottom: 2px solid transparent; border-radius: 0; color: #64748b; font-size: 12px; font-weight: 500; background: transparent; transition: color .15s, border-color .15s; white-space: nowrap; }
#user-show-page .nav-pills .nav-link:hover { color: #334155; }
#user-show-page .nav-pills .nav-link.active { color: #f97316; border-bottom-color: #f97316; font-weight: 600; }
#user-show-page .tab-content { padding: 0; }
/* DataTable chrome for tab tables */
#user-show-page .dataTables_filter, #user-show-page .dataTables_length,
#user-show-page .dt-search, #user-show-page .dt-length { display: none !important; }
#user-show-page table.dataTable { width: 100% !important; margin: 0 !important; border-collapse: collapse !important; }
#user-show-page table.dataTable thead th { height: 32px; padding: 5px 14px !important; background: #f7f9fc !important; color: #94a3b8 !important; border-top: 0 !important; border-bottom: 1px solid #e2e8f0 !important; font-size: 10px !important; font-weight: 600 !important; letter-spacing: .05em; text-transform: uppercase; vertical-align: middle !important; white-space: nowrap; }
#user-show-page table.dataTable tbody td { height: 34px; padding: 5px 14px !important; color: #334155; font-size: 12px; border-bottom: 1px solid #f1f5f9 !important; border-top: none !important; vertical-align: middle; }
#user-show-page table.dataTable tbody tr:hover td { background: #fafbfc; }
#user-show-page table.dataTable tbody td a { color: #334155; font-weight: 600; }
#user-show-page table.dataTable tbody td a:hover { color: #ea580c; text-decoration: none; }
/* Status filter select in th */
#user-show-page th select.form-select { height: 28px; padding: 2px 8px; border-color: #cbd5e1; border-radius: 5px; font-size: 11px; color: #334155; box-shadow: none; }
/* Pagination */
#user-show-page .dataTables_paginate, #user-show-page .dt-paging { display: flex !important; align-items: center; gap: 3px; margin: 0 !important; padding: 0 !important; float: none !important; }
#user-show-page .dataTables_paginate .paginate_button, #user-show-page .dt-paging .dt-paging-button { display: inline-flex !important; align-items: center !important; justify-content: center !important; min-width: 28px; height: 28px; margin: 0 1px !important; padding: 0 8px !important; border: 1px solid #e2e8f0 !important; border-radius: 5px !important; background: #fff !important; color: #475569 !important; font-size: 12px !important; box-shadow: none !important; text-decoration: none !important; }
#user-show-page .dataTables_paginate .paginate_button.current, #user-show-page .dt-paging .dt-paging-button.current { border-color: #f97316 !important; background: #f97316 !important; color: #fff !important; font-weight: 600; }
#user-show-page .dataTables_paginate .paginate_button:hover:not(.current):not(.disabled) { background: #f8fafc !important; border-color: #cbd5e1 !important; }
#user-show-page .dataTables_paginate .paginate_button.disabled { opacity: .4; cursor: not-allowed; }
#user-show-page .dataTables_info, #user-show-page .dt-info { font-size: 11px !important; color: #64748b !important; padding: 0 !important; }
#user-show-page .tab-table-footer { display: flex; align-items: center; justify-content: space-between; padding: 8px 14px; border-top: 1px solid #e2e8f0; gap: 10px; }
/* Card headings */
#user-show-page .card { border-color: #e2e8f0; border-radius: 8px; }
#user-show-page h4 { font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 10px; }
</style>
@endsection

@section('content')
<div id="user-show-page">


        <div class="row">
            <div class="col-12 col-md-9 col-sm-12 mb-3">
                <div class="col-12 mb-4">
                    <div class="card">
                        @include('partials.userheader')
                    </div>
                </div>
                <div class="col-12  mb-3">
                    <div class="card">

                        <ul class="nav nav-pills" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks"
                                    type="button" role="tab" aria-controls="tasks" aria-selected="true">
                                    <i class="fas fa-tasks me-2"></i> {{ __('Tasks') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="leads-tab" data-bs-toggle="tab" data-bs-target="#leads"
                                    type="button" role="tab" aria-controls="leads" aria-selected="false">
                                    <i class="fas fa-code-branch me-2"></i> {{ __('Leads') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="clients-tab" data-bs-toggle="tab" data-bs-target="#clients"
                                    type="button" role="tab" aria-controls="clients" aria-selected="false">
                                    <i class="fas fa-users"></i> {{ __('Clients') }}
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <!-- Tasks Tab -->
                            <div class="tab-pane show active" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
                                {{-- <  h3>{{ __('Tasks assigned') }}</h3> --}}

                                <table class="table table-striped table-bordered" id="tasks-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Title') }}</th>
                                            <th>{{ __('Client') }}</th>
                                            <th>{{ __('Created at') }}</th>
                                            <th>{{ __('Deadline') }}</th>
                                            <th>
                                                <select name="status" id="status-task" class="form-select">
                                                    <option value="" disabled selected>{{ __('Status') }}</option>
                                                    <option value="open">Open</option>
                                                    <option value="closed">Closed</option>
                                                    <option value="all">All</option>
                                                </select>
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <!-- Leads Tab -->
                            <div class="tab-pane" id="leads" role="tabpanel" aria-labelledby="leads-tab">
                                {{-- <h3>{{ __('Leads assigned') }}</h3> --}}
                                <div>
                                    <table id="leads-table" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Client') }}</th>
                                                <th>{{ __('Created at') }}</th>
                                                <th>{{ __('Deadline') }}</th>
                                                <th class="no-sort">
                                                    <select name="status" id="status-lead" class="form-select">
                                                        <option value="" disabled selected>{{ __('Status') }}
                                                        </option>
                                                        <option value="open">Open</option>
                                                        <option value="closed">Closed</option>
                                                        <option value="all">All</option>
                                                    </select>
                                                </th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            <!-- Clients Tab -->
                            <div class="tab-pane" id="clients" role="tabpanel" aria-labelledby="clients-tab">
                                {{-- <h3>{{ __('Clients assigned') }}</h3> --}}
                                <table class="table table-striped table-bordered" id="clients-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Company') }}</th>
                                            <th>{{ __('Primary number') }}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="col-12 col-md-3 col-sm-12 mb-3">
                <div class="card">
                    <div class="row">
                        <div class="col-12 col-md-12 col-sm-6">
                            <h4><i class="fas fa-tasks me-2 fa-sm"></i> Tasks</h4>
                            <div class="chart-container">
                                <canvas id="tasksChart" width="" height="100"></canvas>
                            </div>
                        </div>

                        <!-- Leads Chart Section -->
                        <div class="col-12 col-md-12 col-sm-6">
                            <h4><i class="fas fa-code-branch me-2 fa-sm"></i> Leads</h4>
                            <div class="chart-container">
                                <canvas id="leadsChart" width="" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $('#pagination a').on('click', function(e) {
            e.preventDefault();
            var url = $('#search').attr('action') + '?page=' + page;
            $.post(url, $('#search').serialize(), function(data) {
                $('#posts').html(data);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('button[data-bs-toggle="tab"]');
            const initializedTables = new Set();

            const taskColumns = [{
                    data: 'titlelink',
                    name: 'title'
                },
                {
                    data: 'client_id',
                    name: 'Client',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'deadline',
                    name: 'deadline'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false
                },
            ];

            const clientColumns = [{
                    data: 'clientlink',
                    name: 'name'
                },
                {
                    data: 'company_name',
                    name: 'company_name'
                },
                {
                    data: 'primary_number',
                    name: 'primary_number'
                },
            ];

            const leadColumns = [{
                    data: 'titlelink',
                    name: 'title'
                },
                {
                    data: 'client_id',
                    name: 'Client',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'contact_date',
                    name: 'contact_date'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false
                },
            ];
            tabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(event) {
                    const targetId = event.target.getAttribute('data-bs-target');

                    switch (targetId) {
                        case '#tasks':  
                            var taskTable = initDataTable('#tasks-table', "{!! route('users.taskdata', ['id' => $user->id]) !!}",
                                [], taskColumns, null, null,
                                "GET");
                            $('#status-task').change(function() {
                                selected = $("#status-task option:selected").val();
                                if (selected == 'open') {
                                    taskTable.columns(4).search(1).draw();
                                } else if (selected == 'closed') {
                                    taskTable.columns(4).search(2).draw();
                                } else {
                                    taskTable.columns(4).search('').draw();
                                }
                            });
                            break;
                        case '#clients':
                            var clientTable = initDataTable('#clients-table',
                                "{!! route('users.clientdata', ['id' => $user->id]) !!}", [], clientColumns, null,
                                null, "GET");
                            break;
                        case '#leads':
                            var leadsTable = initDataTable('#leads-table',
                                "{!! route('users.leaddata', ['id' => $user->id]) !!}", [], leadColumns, null, null,
                                "GET");
                            $('#status-lead').change(function() {
                                selected = $("#status-lead option:selected").val();
                                if (selected == 'open') {
                                    leadsTable.columns(4).search(1).draw();
                                } else if (selected == 'closed') {
                                    leadsTable.columns(4).search(2).draw();
                                } else {
                                    leadsTable.columns(4).search('').draw();
                                }
                            });
                            break;
                    }
                });
            });


        });

        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('button[data-bs-toggle="tab"]');
            const initializedTables = new Set();

            const taskColumns = [{
                    data: 'titlelink',
                    name: 'title'
                },
                {
                    data: 'client_id',
                    name: 'Client',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'deadline',
                    name: 'deadline'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false
                },
            ];

            const clientColumns = [{
                    data: 'clientlink',
                    name: 'name'
                },
                {
                    data: 'company_name',
                    name: 'company_name'
                },
                {
                    data: 'primary_number',
                    name: 'primary_number'
                },
            ];

            const leadColumns = [{
                    data: 'titlelink',
                    name: 'title'
                },
                {
                    data: 'client_id',
                    name: 'Client',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'contact_date',
                    name: 'contact_date'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false
                },
            ];


            function initializeTab(targetId) {
                switch (targetId) {
                    case '#tasks':
                        var taskTable = initDataTable('#tasks-table', "{!! route('users.taskdata', ['id' => $user->id]) !!}", [], taskColumns,
                            null, null,
                            "GET");
                        $('#status-task').change(function() {
                            const selected = $("#status-task option:selected").val();
                            if (selected == 'open') {
                                taskTable.columns(4).search(1).draw();
                            } else if (selected == 'closed') {
                                taskTable.columns(4).search(2).draw();
                            } else {
                                taskTable.columns(4).search('').draw();
                            }
                        });
                        break;
                    case '#clients':
                        var clientTable = initDataTable('#clients-table', "{!! route('users.clientdata', ['id' => $user->id]) !!}", [],
                            clientColumns, null,
                            null, "GET");

                        break;
                    case '#leads':
                        var leadsTable = initDataTable('#leads-table', "{!! route('users.leaddata', ['id' => $user->id]) !!}", [], leadColumns,
                            null, null,
                            "GET");
                        $('#status-lead').change(function() {
                            const selected = $("#status-lead option:selected").val();
                            if (selected == 'open') {
                                leadsTable.columns(4).search(1).draw();
                            } else if (selected == 'closed') {
                                leadsTable.columns(4).search(2).draw();
                            } else {
                                leadsTable.columns(4).search('').draw();
                            }
                        });
                        break;
                }
            }

            // Initialize the DataTable for the active tab on page load
            const activeTab = document.querySelector('.tab-pane.active');
            if (activeTab) {
                initializeTab('#' + activeTab.id);
            }

            // Initialize DataTables when tabs are clicked
            tabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(event) {
                    const targetId = event.target.getAttribute('data-bs-target');
                    initializeTab(targetId);
                });
            });
        });

        // $(document).ready(function() {

        //     let taskColumns = [

        //         {
        //             data: 'titlelink',
        //             name: 'title'
        //         },
        //         {
        //             data: 'client_id',
        //             name: 'Client',
        //             orderable: false,
        //             searchable: false
        //         },
        //         {
        //             data: 'created_at',
        //             name: 'created_at'
        //         },
        //         {
        //             data: 'deadline',
        //             name: 'deadline'
        //         },
        //         {
        //             data: 'status',
        //             name: 'status',
        //             orderable: false
        //         },
        //     ];
        //     let clientColumns = [

        //         {
        //             data: 'clientlink',
        //             name: 'name'
        //         },
        //         {
        //             data: 'company_name',
        //             name: 'company_name'
        //         },
        //         {
        //             data: 'primary_number',
        //             name: 'primary_number'
        //         },

        //     ];
        //     let leadColumns = [

        //         {
        //             data: 'titlelink',
        //             name: 'title'
        //         },
        //         {
        //             data: 'client_id',
        //             name: 'Client',
        //             orderable: false,
        //             searchable: false
        //         },
        //         {
        //             data: 'created_at',
        //             name: 'created_at'
        //         },
        //         {
        //             data: 'contact_date',
        //             name: 'contact_date'
        //         },
        //         {
        //             data: 'status',
        //             name: 'status',
        //             orderable: false
        //         },
        //     ];
        //     var taskTable = initDataTable('#tasks-table', "{!! route('users.taskdata', ['id' => $user->id]) !!}", [], taskColumns, null, null,
        //         "GET");
        //     var clientTable = initDataTable('#clients-table', "{!! route('users.clientdata', ['id' => $user->id]) !!}", [], clientColumns, null,
        //         null, "GET");
        //     var leadsTable = initDataTable('#leads-table', "{!! route('users.leaddata', ['id' => $user->id]) !!}", [], leadColumns, null, null,
        //         "GET");
        //     // var table = $('#tasks-table').DataTable({
        //     //     processing: true,
        //     //     serverSide: true,
        //     //     ajax: '{!! route('users.taskdata', ['id' => $user->id]) !!}',
        //     //     columns: [

        //     //         {
        //     //             data: 'titlelink',
        //     //             name: 'title'
        //     //         },
        //     //         {
        //     //             data: 'client_id',
        //     //             name: 'Client',
        //     //             orderable: false,
        //     //             searchable: false
        //     //         },
        //     //         {
        //     //             data: 'created_at',
        //     //             name: 'created_at'
        //     //         },
        //     //         {
        //     //             data: 'deadline',
        //     //             name: 'deadline'
        //     //         },
        //     //         {
        //     //             data: 'status',
        //     //             name: 'status',
        //     //             orderable: false
        //     //         },
        //     //     ]
        //     // });

        //     $('#status-task').change(function() {
        //         selected = $("#status-task option:selected").val();
        //         if (selected == 'open') {
        //             taskTable.columns(4).search(1).draw();
        //         } else if (selected == 'closed') {
        //             taskTable.columns(4).search(2).draw();
        //         } else {
        //             taskTable.columns(4).search('').draw();
        //         }
        //     });
        //     $('#status-lead').change(function() {
        //         selected = $("#status-lead option:selected").val();
        //         if (selected == 'open') {
        //             leadsTable.columns(4).search(1).draw();
        //         } else if (selected == 'closed') {
        //             leadsTable.columns(4).search(2).draw();
        //         } else {
        //             leadsTable.columns(4).search('').draw();
        //         }
        //     });


        // });
        // $(function() {
        // $('#clients-table').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     ajax: '{!! route('users.clientdata', ['id' => $user->id]) !!}',
        //     columns: [

        //         {
        //             data: 'clientlink',
        //             name: 'name'
        //         },
        //         {
        //             data: 'company_name',
        //             name: 'company_name'
        //         },
        //         {
        //             data: 'primary_number',
        //             name: 'primary_number'
        //         },

        //     ]
        // });
        // });

        // $(function() {

        // var table = $('#leads-table').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     ajax: '{!! route('users.leaddata', ['id' => $user->id]) !!}',
        //     columns: [

        //         {
        //             data: 'titlelink',
        //             name: 'title'
        //         },
        //         {
        //             data: 'client_id',
        //             name: 'Client',
        //             orderable: false,
        //             searchable: false
        //         },
        //         {
        //             data: 'created_at',
        //             name: 'created_at'
        //         },
        //         {
        //             data: 'contact_date',
        //             name: 'contact_date'
        //         },
        //         {
        //             data: 'status',
        //             name: 'status',
        //             orderable: false
        //         },
        //     ]
        // });


        // });
    </script>
@endpush
@section('js')
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Initialize Charts -->
    <script>
        // Example data for tasks and leads
        const taskStatistics = [1, 2]; // 1 closed, 2 open
        const leadStatistics = [2, 3]; // 2 closed, 3 open

        // Tasks Doughnut Chart
        const taskChartCtx = document.getElementById('tasksChart').getContext('2d');
        const tasksChart = new Chart(taskChartCtx, {
            type: 'doughnut',
            data: {
                labels: ['Closed', 'Open'],
                datasets: [{
                    data: taskStatistics,
                    backgroundColor: ['#ff6384', '#4caf50'], // Pink for closed, green for open
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true, // Maintain a square aspect ratio
                aspectRatio: 1 // Set aspect ratio to 1:1
            }
        });

        // Leads Doughnut Chart
        const leadChartCtx = document.getElementById('leadsChart').getContext('2d');
        const leadsChart = new Chart(leadChartCtx, {
            type: 'doughnut',
            data: {
                labels: ['Closed', 'Open'],
                datasets: [{
                    data: leadStatistics,
                    backgroundColor: ['#ff6384', '#4caf50'], // Pink for closed, green for open
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true, // Maintain a square aspect ratio
                aspectRatio: 1 // Set aspect ratio to 1:1
            }
        });
    </script>
@endsection
