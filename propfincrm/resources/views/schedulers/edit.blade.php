@section('page-title', 'Edit Scheduler')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')
@extends('layouts.master')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <style>
        #scheduler-edit-page { min-height:calc(100vh - 40px); background:#fff; }
        #scheduler-edit-page .scheduler-form-toolbar { display:flex; align-items:center; min-height:62px; padding:14px 20px; border-bottom:1px solid #e2e8f0; }
        #scheduler-edit-page .scheduler-back { display:inline-flex; align-items:center; gap:7px; height:34px; padding:0 11px; border:1px solid #dbe3ee; border-radius:6px; color:#475569; background:#fff; font-size:12px; font-weight:500; text-decoration:none; }
        #scheduler-edit-page .scheduler-back:hover { border-color:#cbd5e1; color:#1e293b; background:#f8fafc; }
        #scheduler-edit-page .scheduler-form-content { max-width:1240px; padding:16px 20px 24px; }
        #scheduler-edit-page .scheduler-form-card { padding:0 !important; border:0; border-radius:0; box-shadow:none; background:#fff; }
        #scheduler-edit-page .scheduler-form-heading { margin:0 0 14px; padding-bottom:10px; border-bottom:1px solid #e2e8f0; }
        #scheduler-edit-page .scheduler-form-heading h1 { margin:0; color:#1e293b; font-size:15px; font-weight:600; }
        #scheduler-edit-page .scheduler-form-heading p { margin:3px 0 0; color:#64748b; font-size:12px; }
        #scheduler-edit-page .row { display:grid; grid-template-columns:minmax(0,1fr); gap:10px 12px; margin:0 !important; }
        #scheduler-edit-page .row > [class*="col-"] { width:auto !important; min-width:0; max-width:none !important; margin:0 !important; padding:0 !important; }
        #scheduler-edit-page .form-group { margin:0 !important; }
        #scheduler-edit-page label.control-label { display:block; margin-bottom:4px; color:#334155; font-size:12px; font-weight:500; line-height:16px; text-align:left !important; }
        #scheduler-edit-page .form-control, #scheduler-edit-page .selector { box-sizing:border-box; width:100%; min-width:0; height:36px; min-height:36px; padding:8px 10px; border:1px solid #cbd5e1; border-radius:5px; color:#334155; font-size:12px; line-height:18px; box-shadow:none; }
        #scheduler-edit-page .form-control:focus { border-color:#f97316; outline:0; box-shadow:0 0 0 2px rgba(249,115,22,.12); }
        #scheduler-edit-page .select2-container { width:100% !important; }
        #scheduler-edit-page .select2-selection { min-height:36px !important; border-color:#cbd5e1 !important; border-radius:5px !important; box-shadow:none !important; }
        #scheduler-edit-page .select2-selection--single { height:36px !important; }
        #scheduler-edit-page .select2-selection__rendered { color:#334155 !important; font-size:12px !important; line-height:34px !important; padding-left:10px !important; }
        #scheduler-edit-page .select2-selection--multiple .select2-selection__rendered { display:flex; align-items:center; min-height:30px; padding:0 !important; line-height:normal !important; }
        #scheduler-edit-page .select2-selection__choice { margin-top:1px !important; margin-bottom:1px !important; font-size:11px !important; }
        #scheduler-edit-page .select2-selection__arrow { height:34px !important; }
        #scheduler-edit-page .is-invalid { border-color:#dc2626 !important; }
        #scheduler-edit-page .error-msg, #scheduler-edit-page .text-danger { margin-top:3px; color:#dc2626 !important; font-size:11px; }
        #scheduler-edit-page .btn-primary { margin-top:14px; border:0; border-radius:5px; padding:8px 14px; background:#f97316; font-size:12px; font-weight:600; box-shadow:none; }
        #scheduler-edit-page .btn-primary:hover { background:#ea580c; color:#fff; }
        @media (max-width:640px) { #scheduler-edit-page .scheduler-form-toolbar, #scheduler-edit-page .scheduler-form-content { padding-left:12px; padding-right:12px; } }
        @media (min-width:768px) { #scheduler-edit-page .row { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        @media (min-width:992px) { #scheduler-edit-page .row { grid-template-columns:repeat(4,minmax(0,1fr)); } }
    </style>
@endsection

@section('content')
    <div id="scheduler-edit-page">
        <div class="scheduler-form-toolbar">
            <a href="{{ route('scheduler.index') }}" class="scheduler-back">← All Schedulers</a>
        </div>
        <div class="scheduler-form-content">
            <div class="card scheduler-form-card">
                <div class="scheduler-form-heading">
                    <h1>Edit Scheduler</h1>
                    <p>Update the active period, daily time window, projects and assigned users.</p>
                </div>

                <form method="POST" action="{{ route('scheduler.update', $scheduler->id) }}" onsubmit="return formValidate()">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        {{-- From Date --}}
                        <div class="col-12 col-md-4">
                            <div class="form-group {{ $errors->has('from_date') ? 'is-invalid' : '' }}">
                                <label for="from_date" class="control-label">{{ __('From Date') }}</label>
                                <input type="date" name="from_date" id="from_date"
                                    value="{{ old('from_date', $scheduler->from_date) }}"
                                    class="form-control {{ $errors->has('from_date') ? 'is-invalid' : '' }}">
                                @if ($errors->has('from_date'))
                                    <span class="text-danger error-msg">{{ $errors->first('from_date') }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- To Date --}}
                        <div class="col-12 col-md-4">
                            <div class="form-group {{ $errors->has('to_date') ? 'is-invalid' : '' }}">
                                <label for="to_date" class="control-label">{{ __('To Date') }}</label>
                                <input type="date" name="to_date" id="to_date"
                                    value="{{ old('to_date', $scheduler->to_date) }}"
                                    class="form-control {{ $errors->has('to_date') ? 'is-invalid' : '' }}">
                                @if ($errors->has('to_date'))
                                    <span class="text-danger error-msg">{{ $errors->first('to_date') }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Start Time --}}
                        <div class="col-12 col-md-4">
                            <div class="form-group {{ $errors->has('start_time') ? 'is-invalid' : '' }}">
                                <label for="start_time" class="control-label">{{ __('Start Time') }}</label>
                                <input type="time" name="start_time" id="start_time"
                                    value="{{ old('start_time', \Carbon\Carbon::parse($scheduler->start_time)->format('H:i')) }}"
                                    class="form-control {{ $errors->has('start_time') ? 'is-invalid' : '' }}">
                                @if ($errors->has('start_time'))
                                    <span class="text-danger error-msg">{{ $errors->first('start_time') }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- End Time --}}
                        <div class="col-12 col-md-4">
                            <div class="form-group {{ $errors->has('end_time') ? 'is-invalid' : '' }}">
                                <label for="end_time" class="control-label">{{ __('End Time') }}</label>
                                <input type="time" name="end_time" id="end_time"
                                    value="{{ old('end_time', \Carbon\Carbon::parse($scheduler->end_time)->format('H:i')) }}"
                                    class="form-control {{ $errors->has('end_time') ? 'is-invalid' : '' }}">
                                @if ($errors->has('end_time'))
                                    <span class="text-danger error-msg">{{ $errors->first('end_time') }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Assign Project --}}
                        <div class="col-12 col-md-4">
                            <div class="form-group {{ $errors->has('projects') ? 'is-invalid' : '' }}">
                                <label for="projects" class="control-label">{{ __('Assign Project') }}</label>

                                @php
                                    $savedProjects = is_array($scheduler->projects)
                                        ? $scheduler->projects
                                        : json_decode($scheduler->projects, true) ?? [];
                                    $selectedProjects = old('projects') ?? $savedProjects;
                                @endphp

                                <select name="projects[]" class="selector form-control {{ $errors->has('projects') ? 'is-invalid' : '' }}" id="projects" multiple>
                                    <option value="">None</option>
                                    @foreach ($projects as $u)
                                        <option value="{{ $u->project_name }}"
                                            {{ in_array($u->project_name, array_map('strval', $selectedProjects)) ? 'selected' : '' }}>
                                            {{ $u->project_name }}
                                        </option>
                                    @endforeach
                                </select>

                                @if ($errors->has('projects'))
                                    <span class="text-danger error-msg">{{ $errors->first('projects') }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Assign Users --}}
                        <div class="col-12 col-md-4">
                            <div class="form-group {{ $errors->has('user_ids') ? 'is-invalid' : '' }}">
                                <label for="users" class="control-label">{{ __('Assign Users') }}</label>

                                @php
                                    $savedUserIds = is_array($scheduler->user_ids)
                                        ? $scheduler->user_ids
                                        : json_decode($scheduler->user_ids, true) ?? [];
                                    $selectedIds = old('user_ids') ?? $savedUserIds;
                                @endphp

                                <select name="user_ids[]" class="selector form-control {{ $errors->has('user_ids') ? 'is-invalid' : '' }}" id="users" multiple>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}"
                                            {{ in_array($u->id, array_map('strval', $selectedIds)) ? 'selected' : '' }}>
                                            {{ $u->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @if ($errors->has('user_ids'))
                                    <span class="text-danger error-msg">{{ $errors->first('user_ids') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">{{ __('Update Scheduler') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function formValidate() {
            let isValid = true;

            $('.form-control').removeClass('is-invalid');
            $('.error-msg').remove();

            let fromDate  = $('input[name="from_date"]').val();
            let toDate    = $('input[name="to_date"]').val();
            let startTime = $('input[name="start_time"]').val();
            let endTime   = $('input[name="end_time"]').val();
            let users     = $('select[name="user_ids[]"]').val();

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

            // Date range check
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

            // End Time
            if (!endTime) {
                let el = $('input[name="end_time"]');
                el.addClass('is-invalid');
                el.after('<span class="text-danger error-msg">End Time is required</span>');
                isValid = false;
            }

            // DateTime comparison
            if (fromDate && toDate && startTime && endTime) {
                let startDateTime = new Date(`${fromDate}T${startTime}`);
                let endDateTime   = new Date(`${toDate}T${endTime}`);
                if (startDateTime >= endDateTime) {
                    let el = $('input[name="end_time"]');
                    el.addClass('is-invalid');
                    el.after(
                        '<span class="text-danger error-msg">End Date &amp; Time must be greater than Start Date &amp; Time</span>'
                    );
                    isValid = false;
                }
            }

            // Users
            if (!users || users.length === 0) {
                let el = $('select[name="user_ids[]"]').next('.select2-container');
                el.after('<span class="text-danger error-msg">Please select at least one user</span>');
                isValid = false;
            }

            return isValid;
        }

        $(document).ready(function () {
            if (window.initAppSelects) {
                window.initAppSelects(document);
            }
        });
    </script>
@endpush
