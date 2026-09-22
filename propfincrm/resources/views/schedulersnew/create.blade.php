@section('page-title', 'Create Scheduler')
@extends('layouts.master')

@section('content')
    <style>
        .is-invalid {
            border-color: #dc3545;
        }

        .error-msg {
            font-size: 1.2rem;
            display: block;
            margin-top: 4px;
        }
    </style>
    <div class="col-12 d-flex">
        <div class="pull-left me-3 mb-3">
            <a href="{{ route('scheduler.index') }}" type="button" class="btn btn-primary "><i class="fa fa-code-branch me-2"></i> All Schedulers</a>
        </div>
    </div>
    <div class="card p-5">
        @if (isset($user))
            {!! Form::model($user, ['url' => 'update_lead', 'files' => true]) !!}
        @else
            {!! Form::open([
                'route' => 'scheduler.store',
                'onsubmit' => 'return formValidate()',
            ]) !!}
        @endif
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="form-group  {{ $errors->has('from_date') ? 'is-invalid' : '' }}">
                    {!! Form::label('from_date', __('From Date'), ['class' => 'control-label']) !!}
                    {!! Form::date('from_date', null, ['class' => 'form-control']) !!}

                    @if ($errors->has('from_date'))
                        <span class="text-danger">
                            {{ $errors->first('from_date') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group {{ $errors->has('to_date') ? 'is-invalid' : '' }}">
                    {!! Form::label('to_date', __('To Date'), ['class' => 'control-label']) !!}
                    {!! Form::date('to_date', null, ['class' => 'form-control']) !!}

                    @if ($errors->has('to_date'))
                        <span class="text-danger">
                            {{ $errors->first('to_date') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group {{ $errors->has('start_time') ? 'is-invalid' : '' }}">
                    {!! Form::label('start_time', __('Start Time'), ['class' => 'control-label']) !!}

                    {!! Form::time('start_time', null, [
                        'class' => 'form-control',
                        // 'min' => '09:00',
                        // 'max' => '20:00',
                    ]) !!}

                    @if ($errors->has('start_time'))
                        <span class="text-danger">
                            {{ $errors->first('start_time') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group {{ $errors->has('end_time') ? 'is-invalid' : '' }}">
                    {!! Form::label('end_time', __('End Time'), ['class' => 'control-label']) !!}

                    {!! Form::time('end_time', null, [
                        'class' => 'form-control',
                    ]) !!}

                    @if ($errors->has('end_time'))
                        <span class="text-danger">
                            {{ $errors->first('end_time') }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group {{ $errors->has('projects') ? 'is-invalid' : '' }}">
                    {!! Form::label('projects', __('Assign Project'), ['class' => 'control-label']) !!}

                    <select name="projects[]" class="selector form-control" id="projects" multiple>
                        @foreach ($projects as $key => $u)
                            <option value="{{ $u->project_name }}"
                                {{ collect(old('projects'))->contains($u->project_name) ? 'selected' : '' }}>
                                {{ $u->project_name }}
                            </option>
                        @endforeach
                    </select>

                    @if ($errors->has('projects'))
                        <span class="text-danger">
                            {{ $errors->first('projects') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group {{ $errors->has('user_ids') ? 'is-invalid' : '' }}">
                    {!! Form::label('user_ids', __('Assign Users'), ['class' => 'control-label']) !!}

                    <select name="user_ids[]" class="selector form-control" id="users" multiple>
                        @foreach ($users as $key => $u)
                            <option value="{{ $u->id }}"
                                {{ collect(old('user_ids'))->contains($u->id) ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>

                    @if ($errors->has('user_ids'))
                        <span class="text-danger">
                            {{ $errors->first('user_ids') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="text-end">
            {!! Form::submit(__('Create Scheduler'), ['class' => 'btn btn-primary']) !!}
        </div>

        {!! Form::close() !!}

    </div>
    <script>
        function formValidate() {
            let isValid = true;

            $('.form-control').removeClass('is-invalid');
            $('.error-msg').remove();

            let fromDate = $('input[name="from_date"]').val();
            let toDate = $('input[name="to_date"]').val();
            let startTime = $('input[name="start_time"]').val();
            let endTime = $('input[name="end_time"]').val();
            let users = $('select[name="user_ids[]"]').val();
            let projects = $('select[name="projects[]"]').val();

            if (!fromDate) {
                let el = $('input[name="from_date"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">From Date is required</span>');
                isValid = false;
            }

            if (!toDate) {
                let el = $('input[name="to_date"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">To Date is required</span>');
                isValid = false;
            }

            if (fromDate && toDate && fromDate > toDate) {
                let el = $('input[name="to_date"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">To Date must be after From Date</span>');
                isValid = false;
            }

            if (!startTime) {
                let el = $('input[name="start_time"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">Start Time is required</span>');
                isValid = false;
            } 
            

            if (!endTime) {
                let el = $('input[name="end_time"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">End Time is required</span>');
                isValid = false;
            } 
           
            if (fromDate && toDate && startTime && endTime) {

                let startDateTime = new Date(`${fromDate}T${startTime}`);
                let endDateTime = new Date(`${toDate}T${endTime}`);
                console.log(startDateTime , endDateTime)
                if (startDateTime >= endDateTime) {
                    let el = $('input[name="end_time"]');
                    el.addClass('is-invalid');
                    el.after(
                        '<span class="text-danger error-msg">End Date & Time must be greater than Start Date & Time</span>'
                    );
                    isValid = false;
                }
            }

            if (!users || users.length === 0) {
                let el = $('select[name="user_ids[]"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">Please select at least one user</span>');
                isValid = false;
            }
            if (!projects || projects.length === 0) {
                let el = $('select[name="projects[]"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">Please select at least one project</span>');
                isValid = false;
            }
            return isValid;
        }
        $(document).ready(function() {
            $('#users').select2({
                placeholder: "Select Users",
                allowClear: true,
                width: '100%',
                theme: 'bootstrap-5'
            });
        });
    </script>
@stop
