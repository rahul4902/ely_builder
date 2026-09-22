@section('page-title', 'Create Scheduler')
@section('main-class', 'p-0')
@extends('layouts.master')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <style>
        #scheduler-form-page { min-height:calc(100vh - 40px); background:#fff; }
        #scheduler-form-page .scheduler-form-toolbar { display:flex; align-items:center; min-height:62px; padding:14px 20px; border-bottom:1px solid #e2e8f0; }
        #scheduler-form-page .scheduler-back { display:inline-flex; align-items:center; gap:7px; height:34px; padding:0 11px; border:1px solid #dbe3ee; border-radius:6px; color:#475569; background:#fff; font-size:12px; font-weight:500; text-decoration:none; }
        #scheduler-form-page .scheduler-back:hover { border-color:#cbd5e1; color:#1e293b; background:#f8fafc; }
        #scheduler-form-page .scheduler-form-content { max-width:1240px; padding:16px 20px 24px; }
        #scheduler-form-page .scheduler-form-card { padding:0 !important; border:0; border-radius:0; box-shadow:none; background:#fff; }
        #scheduler-form-page .scheduler-form-heading { margin:0 0 14px; padding-bottom:10px; border-bottom:1px solid #e2e8f0; }
        #scheduler-form-page .scheduler-form-heading h1 { margin:0; color:#1e293b; font-size:15px; font-weight:600; }
        #scheduler-form-page .scheduler-form-heading p { margin:3px 0 0; color:#64748b; font-size:12px; }
        #scheduler-form-page .row { display:grid; grid-template-columns:minmax(0,1fr); gap:10px 12px; margin:0 !important; }
        #scheduler-form-page .row > [class*="col-"] { width:auto !important; min-width:0; max-width:none !important; margin:0 !important; padding:0 !important; }
        #scheduler-form-page .form-group { margin:0 !important; }
        #scheduler-form-page label.control-label { display:block; margin-bottom:4px; color:#334155; font-size:12px; font-weight:500; line-height:16px; text-align:left !important; }
        #scheduler-form-page .form-control, #scheduler-form-page .selector { box-sizing:border-box; width:100%; min-width:0; height:36px; min-height:36px; padding:8px 10px; border:1px solid #cbd5e1; border-radius:5px; color:#334155; font-size:12px; line-height:18px; box-shadow:none; }
        #scheduler-form-page .form-control:focus { border-color:#f97316; outline:0; box-shadow:0 0 0 2px rgba(249,115,22,.12); }
        #scheduler-form-page .select2-container { width:100% !important; }
        #scheduler-form-page .select2-selection { min-height:36px !important; border-color:#cbd5e1 !important; border-radius:5px !important; box-shadow:none !important; }
        #scheduler-form-page .select2-selection--single { height:36px !important; }
        #scheduler-form-page .select2-selection__rendered { color:#334155 !important; font-size:12px !important; line-height:34px !important; padding-left:10px !important; }
        #scheduler-form-page .select2-selection--multiple .select2-selection__rendered { display:flex; align-items:center; min-height:30px; padding:0 !important; line-height:normal !important; }
        #scheduler-form-page .select2-selection__choice { margin-top:1px !important; margin-bottom:1px !important; font-size:11px !important; }
        #scheduler-form-page .select2-selection__arrow { height:34px !important; }
        #scheduler-form-page .is-invalid { border-color:#dc2626 !important; }
        #scheduler-form-page .error-msg, #scheduler-form-page .text-danger { margin-top:3px; color:#dc2626 !important; font-size:11px; }
        #scheduler-form-page .btn-primary { margin-top:14px; border:0; border-radius:5px; padding:8px 14px; background:#f97316; font-size:12px; font-weight:600; box-shadow:none; }
        #scheduler-form-page .btn-primary:hover { background:#ea580c; color:#fff; }
        @media (max-width:640px) { #scheduler-form-page .scheduler-form-toolbar, #scheduler-form-page .scheduler-form-content { padding-left:12px; padding-right:12px; } }
        @media (min-width:768px) { #scheduler-form-page .row { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        @media (min-width:992px) { #scheduler-form-page .row { grid-template-columns:repeat(4,minmax(0,1fr)); } }
    </style>
@endsection

@section('content')
    <div id="scheduler-form-page">
    <div class="scheduler-form-toolbar"><a href="{{ route('scheduler.index') }}" class="scheduler-back"><i data-lucide="arrow-left" class="h-4 w-4"></i> All schedulers</a></div>
    <div class="scheduler-form-content"><div class="card scheduler-form-card">
        <div class="scheduler-form-heading"><h1>Scheduler details</h1><p>Set the active period, daily time window, projects and assigned users.</p></div>
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
                        // 'min' => '09:00',
                        // 'max' => '20:00',
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
                        <option value="">None</option>
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

    </div></div></div>
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

            // From Date
            if (!fromDate) {
                let el = $('input[name="from_date"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">From Date is required</span>');
                isValid = false;
            }

            // To Date
            if (!toDate) {
                let el = $('input[name="to_date"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">To Date is required</span>');
                isValid = false;
            }

            // Date check
            if (fromDate && toDate && fromDate > toDate) {
                let el = $('input[name="to_date"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">To Date must be after From Date</span>');
                isValid = false;
            }

            // Start Time
            if (!startTime) {
                let el = $('input[name="start_time"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">Start Time is required</span>');
                isValid = false;
            } 
            // else if (startTime < "09:00" || startTime > "20:00") {
            //     let el = $('input[name="start_time"]');
            //     el.addClass('is-invalid');
            //     el.after('<span class="text-danger error-msg">Start Time must be between 9 AM and 8 PM</span>');
            //     isValid = false;
            // }

            // End Time
            if (!endTime) {
                let el = $('input[name="end_time"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">End Time is required</span>');
                isValid = false;
            } 
            // else if (endTime < "09:00" || endTime > "20:00") {
            //     let el = $('input[name="end_time"]');
            //     el.addClass('is-invalid');
            //     el.after('<span class="text-danger error-msg">End Time must be between 9 AM and 8 PM</span>');
            //     isValid = false;
            // }

            // Time comparison (IMPORTANT)
            // if (startTime && endTime && endTime <= startTime) {
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

            // From Date
            if (!fromDate) {
                let el = $('input[name="from_date"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">From Date is required</span>');
                isValid = false;
            }

            // To Date
            if (!toDate) {
                let el = $('input[name="to_date"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">To Date is required</span>');
                isValid = false;
            }

            // Date check
            if (fromDate && toDate && fromDate > toDate) {
                let el = $('input[name="to_date"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">To Date must be after From Date</span>');
                isValid = false;
            }

            // Start Time
            if (!startTime) {
                let el = $('input[name="start_time"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">Start Time is required</span>');
                isValid = false;
            } 
            // else if (startTime < "09:00" || startTime > "20:00") {
            //     let el = $('input[name="start_time"]');
            //     el.addClass('is-invalid');
            //     el.after('<span class="text-danger error-msg">Start Time must be between 9 AM and 8 PM</span>');
            //     isValid = false;
            // }

            // End Time
            if (!endTime) {
                let el = $('input[name="end_time"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">End Time is required</span>');
                isValid = false;
            } 
            // else if (endTime < "09:00" || endTime > "20:00") {
            //     let el = $('input[name="end_time"]');
            //     el.addClass('is-invalid');
            //     el.after('<span class="text-danger error-msg">End Time must be between 9 AM and 8 PM</span>');
            //     isValid = false;
            // }

            // Time comparison (IMPORTANT)
            // if (startTime && endTime && endTime <= startTime) {
            //     let el = $('input[name="end_time"]');
            //     el.addClass('is-invalid');
            //     el.after('<span class="text-danger error-msg">End Time must be greater than Start Time</span>');
            //     isValid = false;
            // }
            if (fromDate && toDate && startTime && endTime) {

                let startDateTime = new Date(`${fromDate}T${startTime}`);
                let endDateTime = new Date(`${toDate}T${endTime}`);
                if (startDateTime >= endDateTime) {
                    let el = $('input[name="end_time"]');
                    el.addClass('is-invalid');
                    el.after(
                        '<span class="text-danger error-msg">End Date & Time must be greater than Start Date & Time</span>'
                    );
                    isValid = false;
                }
            }

            // Users
            if (!users || users.length === 0) {
                let el = $('select[name="user_ids[]"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">Please select at least one user</span>');
                isValid = false;
            }
            // projects
            // if (!projects || projects.length === 0) {
            //     let el = $('select[name="projects[]"]');
            //     el.addClass('is-invalid');
            //     el.after('<span class="text-danger error-msg">Please select at least one project</span>');
            //     isValid = false;
            // }
            return isValid;
        }
        $(document).ready(function() {
            if (window.initAppDatePickers) {
                window.initAppDatePickers($('#scheduler-form-page'));
            }
            function initialiseSchedulerSelects() {
                if (typeof $.fn.select2 !== 'function') return false;
                $('#projects, #users').each(function () {
                    var $select = $(this);
                    if ($select.hasClass('select2-hidden-accessible')) return;
                    $select.select2({ placeholder: $select.is('#users') ? 'Select users' : 'Select projects', allowClear: true, width: '100%', theme: 'bootstrap-5' });
                });
                return true;
            }
            if (!initialiseSchedulerSelects()) {
                var attempts = 0;
                var waiter = setInterval(function () { if (initialiseSchedulerSelects() || ++attempts >= 20) clearInterval(waiter); }, 250);
            }
        });
    </script>
@endsection
