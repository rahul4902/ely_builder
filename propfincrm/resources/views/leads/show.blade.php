@extends('layouts.master')
@section('page-title', 'All leads View')

@section('styles')
    <style>
        .movedown {
            outline: none !important;
        }
    </style>
@endsection

@section('content')
    <div class="row ps-3">
        <div class="col-12 d-flex">
            <div class="pull-left me-3 mb-3">
                <a href="{{ url('all_lead_list') }}" type="button" class="crm-btn crm-btn-primary">
                    <i class="fa fa-code-branch me-2"></i> All leads
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                @include('partials.clientheader', ['lead_info' => $lead])
            </div>
            @if (isset($contact) && $contact)
                <div class="col-12 col-md-6 mb-3">
                    <div class="card">
                        @include('partials.userheader')
                    </div>
                </div>
            @endif
        </div>

        <div class="row mb-3">
            <div class="col-md-9">
                @include('partials.comments', ['subject' => $lead])
                @if (Session::has('message'))
                    <p class="alert alert-info">{{ Session::get('message') }}</p>
                @endif
                @if (Auth::user()->userRole->userRoleDetails->name == 'employee')
                    <button class="crm-btn crm-btn-primary float-end mb-3" type="button"
                        data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Next Follow Up
                    </button>
                @endif

                <div class="card">
                    <table id="registration" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Follow Up Date</th>
                                <th>Followed Date &amp; Time</th>
                                <th>Comment</th>
                                <th>Meeting Date</th>
                                <th>Senior Visit</th>
                                <th>Meeting Type</th>
                                <th>Follow_up Status</th>
                                @if (Auth::user()->userRole->userRoleDetails->name == 'employee')
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($data as $follow_data)
                                <tr>
                                    <td>
                                        @foreach ($name as $nm)
                                            @if ($follow_data->user_id == $nm->id)
                                                {{ $nm->name }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>{{ date('d/m/Y h:i', strtotime($follow_data->follow_up_date)) }}</td>
                                    <td>{{ date('d/m/Y h:i', strtotime($follow_data->action_date)) }}</td>
                                    <td>{{ $follow_data->comment }}</td>
                                    @if ($follow_data->meeting_date != null)
                                        <td>{{ date('d/m/Y h:i', strtotime($follow_data->meeting_date)) }}</td>
                                    @else
                                        <td></td>
                                    @endif
                                    <td>{{ $follow_data->senior_visit }}</td>
                                    <td>{{ $follow_data->meeting_type }}</td>
                                    <td>{{ $follow_data->follow_up_status }}</td>
                                    @if (Auth::user()->userRole->userRoleDetails->name == 'employee')
                                        <td>
                                            @if ($follow_data->meeting_date == null)
                                                <a href="#"
                                                    class="crm-btn crm-btn-primary idget"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#meeting"
                                                    data-follow-id="{{ $follow_data->id }}">
                                                    Meeting
                                                </a>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="col-md-3">
                <div class="crm-info-panel">
                    <div class="crm-info-panel-header text-center">
                        <h5 class="m-2"> {{ __('Lead information') }}</h5>
                    </div>
                    @if ($lead->user)
                        <div class="crm-info-panel-body p-3">
                            <div class="crm-info-panel-row">
                                <span>{{ __('Assigned to') }}:</span>
                                <a href="{{ route('leads.show', $lead->user->id) }}">{{ $lead->user->name }}</a>
                            </div>
                            <div class="crm-info-panel-row">
                                {{ __('Created at') }}: {{ date('d F, Y, H:i', strtotime($lead->created_at)) }}
                            </div>
                            @if ($lead->days_until_contact < 2)
                                <div class="crm-info-panel-row">
                                    {{ __('Follow up') }}:
                                    <span class="text-danger">{{ strTotime($lead->contact_date) ? date('d, F Y, H:i', strTotime($lead->contact_date)) : '' }}
                                        @if ($lead->status == 1)
                                            ({{ $lead->days_until_contact }})
                                        @endif
                                    </span>
                                    <i class="fas fa-calendar-alt ms-1" data-bs-toggle="modal" data-bs-target="#ModalFollowUp" style="cursor:pointer;"></i>
                                </div>
                            @else
                                <div class="crm-info-panel-row">
                                    {{ __('Follow up') }}:
                                    <span class="text-green-600">{{ date('d, F Y, H:i', strTotime($lead->contact_date)) }}
                                        @if ($lead->status == 1)
                                            ({{ $lead->days_until_contact }})<i class="fas fa-calendar-alt ms-1" data-bs-toggle="modal"
                                                data-bs-target="#ModalFollowUp" style="cursor:pointer;"></i>
                                        @endif
                                    </span>
                                </div>
                            @endif
                            <div class="crm-info-panel-row">
                                {{ __('Status') }}: <strong>{{ $lead->status_label }}</strong>
                            </div>
                        </div>
                    @endif
                    <div class="p-3">
                        @if ($lead->status == 1)
                            @if (Auth::user()->userRole->userRoleDetails->name != 'employee')
                                <form class="mb-0" method="POST" action="{{ url('leads/updateassign', $lead->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="user_assigned_id" class="crm-form-control mb-2">
                                        @foreach ($users as $uid => $uname)
                                            <option value="{{ $uid }}" {{ $lead->user_assigned_id == $uid ? 'selected' : '' }}>{{ $uname }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="crm-btn crm-btn-primary w-100 mt-2">{{ __('Assign new user') }}</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ url('leads/updatestatus', $lead->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="crm-btn crm-btn-primary w-100 mt-2 movedown">{{ __('Complete Lead') }}</button>
                            </form>
                        @endif
                    </div>

                    <div class="crm-activity-feed movedown">
                        @foreach ($lead->activity as $activity)
                            <div class="crm-feed-item">
                                <div class="crm-feed-date">{{ date('d, F Y H:i', strTotime($activity->created_at)) }}</div>
                                <div class="crm-feed-text">{{ $activity->text }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>


        {{-- ModalFollowUp --}}
        <div class="modal fade" id="ModalFollowUp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">{{ __('Change deadline') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <form method="POST" action="{{ route('leads.followup', $lead->id) }}">
                            @csrf
                            @method('PATCH')
                            <div class="crm-form-group">
                                <label class="crm-form-label" for="contact_date">{{ __('Next follow up') }}</label>
                                <input type="date" name="contact_date" id="contact_date"
                                    value="{{ \Carbon\Carbon::now()->addDays(7)->format('Y-m-d') }}"
                                    class="crm-form-control">
                            </div>
                            <div class="crm-form-group mt-2">
                                <input type="time" name="contact_time" value="11:00" class="crm-form-control">
                            </div>
                            <div class="crm-form-group mt-2">
                                <label class="crm-form-label" for="comment_followup">Comment</label>
                                <textarea name="comment" id="comment_followup" class="crm-form-control" rows="4"></textarea>
                            </div>

                            <div class="modal-footer px-0">
                                <button type="button" class="crm-btn" data-bs-dismiss="modal">{{ __('Close') }}</button>
                                <button type="submit" class="crm-btn crm-btn-primary">{{ __('Update follow up') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Next Follow Up Modal --}}
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('leads.nextfollowup', $lead->id) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Next Follow Up</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="crm-form-group mb-2">
                                <label class="crm-form-label">{{ __('Lead Status') }}</label>
                                <select name="follow_up_status" class="crm-form-control">
                                    <option value="In Process Lead">In Process Lead</option>
                                    <option value="Closed Lead">Closed Lead</option>
                                    <option value="Number Not Valid">Number Not Valid</option>
                                    <option value="Broker">Broker</option>
                                    <option value="Not Interested">Not Interested</option>
                                </select>
                            </div>

                            <div class="crm-form-group mb-2">
                                <label class="crm-form-label">{{ __('Next follow up') }}</label>
                                <input type="date" name="follow_up_date"
                                    value="{{ \Carbon\Carbon::now()->addDays(7)->format('Y-m-d') }}"
                                    class="crm-form-control">
                            </div>
                            <div class="crm-form-group mb-2">
                                <label class="crm-form-label">{{ __('Next follow Time') }}</label>
                                <input type="time" name="follow_up_time" value="11:00" class="crm-form-control">
                            </div>
                            <div class="crm-form-group mb-2">
                                <label class="crm-form-label">Comment</label>
                                <textarea name="comment" class="crm-form-control" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="crm-btn" data-bs-dismiss="modal">{{ __('Close') }}</button>
                            <button type="submit" class="crm-btn crm-btn-primary">{{ __('Update follow up') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Meeting Modal --}}
        <div class="modal fade" id="meeting" tabindex="-1" role="dialog" aria-labelledby="meetingModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('leads.StoreMeeting', $lead->id) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="meetingModalLabel">Meeting</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="lead_follow_up_id" id="lead_follow_up_id" value="">

                            <div class="crm-form-group mb-2">
                                <label class="crm-form-label">{{ __('Select Seniors') }}</label>
                                <select name="seniors" class="crm-form-control" id="seniors"></select>
                            </div>

                            <div class="crm-form-group mb-2">
                                <label class="crm-form-label">{{ __('Lead Status') }}</label>
                                <select name="lead_status" class="crm-form-control" id="stataus"></select>
                            </div>

                            <div class="crm-form-group mb-2">
                                <label class="crm-form-label">{{ __('Meeting Type') }}</label>
                                <select name="meeting_type" class="crm-form-control" id="MeetingType"></select>
                            </div>

                            <div class="crm-form-group mb-2">
                                <label class="crm-form-label">Comment</label>
                                <textarea name="comment" class="crm-form-control" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="crm-btn" data-bs-dismiss="modal">{{ __('Close') }}</button>
                            <button type="submit" class="crm-btn crm-btn-primary">{{ __('Submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            initDataTable("#registration", "", [], null, null, null);
        });
    </script>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Bootstrap 5 tooltip init
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Populate lead_follow_up_id when Meeting button is clicked
            $(document).on('click', '.idget', function () {
                var id = $(this).data('follow-id');
                $('#lead_follow_up_id').val(id);
            });

            // Load seniors
            $.ajax({
                type: 'get',
                url: '{{ route("leads.GetSeniors") }}',
                data: { '_token': '{{ csrf_token() }}' },
                success: function (data) {
                    var opt = '';
                    $.each(data, function (index, value) {
                        opt += '<option value="' + value.name + '">' + value.name + '</option>';
                    });
                    $('#seniors').html(opt);
                },
                error: function () {
                    alert('Error in getting Seniors');
                }
            });

            // Load meeting types
            $.ajax({
                type: 'get',
                url: '{{ route("leads.MeetingType") }}',
                data: { '_token': '{{ csrf_token() }}' },
                success: function (data) {
                    var opt = '';
                    $.each(data, function (index, value) {
                        opt += '<option value="' + value.type + '">' + value.type + '</option>';
                    });
                    $('#MeetingType').html(opt);
                },
                error: function () {
                    alert('Error in getting Meeting Types');
                }
            });

            // Load lead statuses
            $.ajax({
                type: 'get',
                url: '{{ route("leads.Leadstatus") }}',
                data: { '_token': '{{ csrf_token() }}' },
                success: function (data) {
                    var opt = '';
                    $.each(data, function (index, value) {
                        opt += '<option value="' + value.status + '">' + value.status + '</option>';
                    });
                    $('#stataus').html(opt);
                },
                error: function () {
                    alert('Error in getting Lead Statuses');
                }
            });
        });
    </script>
@endpush
