@extends('layouts.master')
@section('page-title', __('Sell leads'))
@section('heading')

@stop

@section('content')
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>
    @endpush
    <div class="col-12 d-flex">
        <div class="pull-left me-3 mb-3">
            <a href="{{ url('all_sell_list') }}" type="button" class="btn btn-primary "><i class="fa fa-code-branch me-2"></i>
                All sell leads
            </a>
        </div>
    </div>
    <div class="row">
        @include('partials.sell_client_header', ['lead_info' => $get_view_data])
        {{-- @include('partials.userheader') --}}
    </div>
    @if (isset($lead) && $lead)
        <div class="row mt-3">
            <div class="col-md-9">
                @include('partials.comments', ['subject' => $lead])
            </div>
            <div class="col-md-3">
                <div class="card" style="padding: 0!important;overflow:hidden;">
                    <div class="sidebarheader m-0 text-center">
                        <h5 class="m-2"> {{ __('Lead information') }}</h5>
                    </div>
                    <div class="sidebarbox p-3">
                        <p>{{ __('Assigned to') }}:
                            @if ($lead->user)
                                <a href="{{ route('leads.show', $lead->user->id) }}">
                                    {{ $lead->user->name }}</a>
                            @endif
                        </p>
                        <p>{{ __('Created at') }}: {{ date('d F, Y, H:i', strtotime($lead->created_at)) }} </p>
                        @if ($lead->days_until_contact < 2)
                            <p>{{ __('Follow up') }}: <span
                                    style="color:red;">{{ strTotime($lead->contact_date) ? date('d, F Y, H:i', strTotime($lead->contact_date)) : '' }}

                                    @if ($lead->status == 1 && strTotime($lead->contact_date))
                                        ({!! $lead->days_until_contact !!})
                                    @endif
                                </span>
                                <i class="glyphicon glyphicon-calendar" data-toggle="modal"
                                    data-target="#ModalFollowUp"></i>
                            </p>
                        @else
                            <p>{{ __('Follow up') }}: <span
                                    style="color:green;">{{ date('d, F Y, H:i', strTotime($lead->contact_date)) }}

                                    @if ($lead->status == 1)
                                        ({!! $lead->days_until_contact !!})<i class="glyphicon glyphicon-calendar" data-toggle="modal"
                                            data-target="#ModalFollowUp"></i>
                                    @endif
                                </span></p>
                        @endif
                        <p>{{ __('Status') }}: <strong>{{ $lead->status_label }}</strong></p>

                    </div>

                    <div class="p-3">
                        @if ($lead->status == 1)
                            {!! Form::model($lead, [
                                'method' => 'PATCH',
                                'url' => ['leads/updateassign', $lead->id],
                            ]) !!}
                            {!! Form::select('user_assigned_id', $users, null, [
                                'class' => 'form-control ui search selection top right pointing search-select',
                                'id' => 'search-select',
                            ]) !!}
                            {!! Form::submit(__('Assign new user'), ['class' => 'btn btn-primary form-control closebtn mt-2']) !!}
                            {!! Form::close() !!}
                            {!! Form::model($lead, [
                                'method' => 'PATCH',
                                'url' => ['leads/updatestatus', $lead->id],
                            ]) !!}

                            {!! Form::submit(__('Complete Lead'), ['class' => 'btn btn-success form-control closebtn movedown mt-2']) !!}
                            {!! Form::close() !!}
                        @endif
                    </div>

                    <div class="activity-feed movedown">
                        @foreach ($lead->activity as $activity)
                            <div class="feed-item">
                                <div class="activity-date">{{ date('d, F Y H:i', strTotime($activity->created_at)) }}</div>
                                <div class="activity-text">{{ $activity->text }}</div>

                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ModalFollowUp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">{{ __('Change deadline') }}</h4>
                    </div>

                    <div class="modal-body">

                        {!! Form::model($lead, [
                            'method' => 'PATCH',
                            'route' => ['leads.followup', $lead->id],
                        ]) !!}
                        {!! Form::label('contact_date', __('Next follow up'), ['class' => 'control-label']) !!}
                        {!! Form::date('contact_date', \Carbon\Carbon::now()->addDays(7), ['class' => 'form-control']) !!}<br>
                        {!! Form::time('contact_time', '11:00', ['class' => 'form-control']) !!}<br>
                        <div class="form-group">
                            {!! Form::label('Comment', 'Comment') !!}
                            {!! Form::textarea('comment', null, ['class' => 'form-control', 'rows' => 4]) !!}
                        </div>



                        <div class="modal-footer">
                            <button type="button" class="btn btn-default col-lg-6"
                                data-dismiss="modal">{{ __('Close') }}</button>
                            <div class="col-lg-6">
                                {!! Form::submit(__('Update follow up'), ['class' => 'btn btn-success form-control closebtn']) !!}
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop
