@extends('layouts.master')
@section('heading')
    <h1>
        @if ($type != null)
            {{ ucfirst($type) }}
        @endif{{ __('All leads') }} (
        <b>{{ count($leads) }}</b>)
    </h1>
@stop


@section('content')
    <style type="text/css">
      
    </style>
    @if ($type != null)
        <form action="{{ route('lead_list', $type) }}" method="get">
        @else
            <form action="{{ url('get_lead_date_wise') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="date">From:</label>
                <input type="date" id="date" class="form-control" placeholder="Enter Date" name="date">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="date">To:</label>
                <input type="date" id="date" class="form-control" placeholder="Enter Date" name="end_date">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary center-block">Submit</button>

    </form>
    <table id="registration" class="table table-striped table-bordered border-none" cellspacing="0" width="100%">

        <thead>
            <tr>
                <th>Name</th>
                <th>Mobile</th>
                <th>Last Comment</th>
                <th>Project</th>
                <th>Requirement</th>
                <th style="width: 90px!important">Date</th>
                <th>Assigned</th>
                <th>Team Leader</th>
                <th style="width: 90px!important">Meeting Date</th>
                <th>Status</th>
                <th>Action</th>

            </tr>
        </thead>

        <tbody>
            @foreach ($leads as $lead)
                <?php
                
                $follow1 = null;
                $follow1meet = null;
                if ($lead->getfollowup != null && !empty($lead->getfollowup)) {
                    $follow1 = $lead->getfollowup->first();
                    $follow1meet = $lead->getfollowup->where('meeting_date', '!=', null)->first();
                }
                
                // $follow1meet =
                
                ?>
                <tr>
                    <td><a
                            href="@if ($type != null) ../leads/{{ $lead->id }} @else leads/{{ $lead->id }} @endif">{{ $lead->name }}</a>
                    </td>
                    <td>{{ $lead->contact_no }}</td>
                    <td>
                        @if ($follow1 != null)
                            {{ $follow1->comment }}
                        @endif
                    </td>
                    <td>{{ $lead->project }}</td>
                    <td>{{ $lead->requirement }}</td>
                    <td>{{ date('d-m-Y', strtotime($lead->updated_at)) }}</td>
                    <td>{{ $lead->user_name }}</td>
                    <td>{{ !is_null($lead->user->getLeader) ? $lead->user->getLeader->name : '' }}</td>
                    <td>
                        @if ($follow1meet != null)
                            {{ date('d-m-Y', strtotime($follow1meet->meeting_date)) }}
                        @endif
                    </td>
                    <td>
                        @if ($lead->status == 1)
                            open
                        @else
                            close
                        @endif
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu p-0">
                                <li>
                                    <a href="{{ url('edit_lead?id=' . $lead->id) }}">Edit</a>
                                </li>
                                <li>
                                    <form action="{{ url('delete_lead') }}" method="post" class="inline">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $lead->id }}">
                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('are you sure to delete')">Delete</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>

@stop

@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.4.1/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#registration').DataTable({
                "order": [
                    [5, 'desc']
                ],
                "lengthMenu": [
                    [100, 200, 500, 1000],
                    [100, 200, 500, "All"]
                ],
                "dom": 'Bfrtip',
                "paging": false,
                "processing": true,
                "ordering": false,
                "autoWidth": false,
                buttons: [{

                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        text: 'Export in CSV',
                        footer: true,
                        filename: 'Leads'
                    },

                ],
            });
        });
    </script>
@endpush
