@section('page-title', 'Task view')
@extends('layouts.master')

@section('heading')
@endsection

@section('content')

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="crm-card">
                @include('partials.clientheader_old')
            </div>
        </div>
        @if ($contact)
            <div class="col-md-6 mb-3">
                <div class="crm-card">
                    @include('partials.userheader')
                </div>
            </div>
        @endif
    </div>

    <div class="row">
        <div class="col-md-9">
            @include('partials.comments', ['subject' => $tasks])
        </div>
        <div class="col-md-3">

            <div class="crm-info-panel overflow-hidden">
                <div class="crm-info-panel-header text-center">
                    <p>{{ __('Task information') }}</p>
                </div>
                <div class="crm-info-panel-body p-3">
                    <p>{{ __('Assigned') }}:
                        @if ($tasks->user)
                            <a href="{{ route('users.show', $tasks->user->id) }}">
                                {{ $tasks->user->name }}</a>
                        @endif
                    </p>
                    <p>{{ __('Created at') }}: {{ date('d F, Y, H:i', strtotime($tasks->created_at)) }}</p>

                    @if ($tasks->days_until_deadline)
                        <p>{{ __('Deadline') }}: <span class="text-red-600">{{ date('d, F Y', strTotime($tasks->deadline)) }}
                                @if ($tasks->status == 1)
                                    ({{ $tasks->days_until_deadline }})
                                @endif
                            </span>
                        </p>
                    @else
                        <p>{{ __('Deadline') }}: <span class="text-green-600">{{ date('d, F Y', strTotime($tasks->deadline)) }}
                                @if ($tasks->status == 1)
                                    ({{ $tasks->days_until_deadline }})
                                @endif
                            </span>
                        </p>
                    @endif

                    @if ($tasks->status == 1)
                        {{ __('Status') }}: {{ __('Open') }}
                    @else
                        {{ __('Status') }}: {{ __('Closed') }}
                    @endif
                </div>

                <div class="p-3">
                    @if ($tasks->status == 1)
                        <form method="POST" action="{{ url('tasks/updateassign/' . $tasks->id) }}">
                            @csrf
                            @method('PATCH')
                            <select name="user_assigned_id" class="mb-2 crm-form-control">
                                @foreach ($users as $uid => $uname)
                                    <option value="{{ $uid }}" {{ $tasks->user_assigned_id == $uid ? 'selected' : '' }}>{{ $uname }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="mb-2 crm-btn crm-btn-primary w-100">
                                {{ __('Assign user') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ url('tasks/updatestatus/' . $tasks->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="crm-btn crm-btn-primary w-100">
                                {{ __('Close task') }}
                            </button>
                        </form>
                    @endif
                </div>

                <div class="crm-info-panel-header text-center">
                    <p>{{ __('Time management') }}</p>
                </div>
                <table class="table text-center mb-0">
                    <tr>
                        <th class="text-center">{{ __('Title') }}</th>
                        <th class="text-center">{{ __('Time') }}</th>
                    </tr>
                    <tbody>
                        @foreach ($tasktimes as $tasktime)
                            <tr>
                                <td class="py-1 px-2">{{ $tasktime->title }}</td>
                                <td class="py-1 px-2">{{ $tasktime->time }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-3 d-grid gap-2">
                    <button type="button" class="crm-btn crm-btn-primary w-100" data-bs-toggle="modal"
                        data-bs-target="#ModalTimer">
                        {{ __('Add time') }}
                    </button>

                    <button type="button" class="crm-btn crm-btn-primary w-100 mt-2" data-bs-toggle="modal"
                        data-bs-target="#myModal">
                        {{ __('Create invoice') }}
                    </button>
                </div>

                <div class="activity-feed mb-3 p-3">
                    @foreach ($tasks->activity as $activity)
                        <div class="feed-item">
                            <div class="activity-date">{{ date('d, F Y H:i', strTotime($activity->created_at)) }}</div>
                            <div class="activity-text">{{ $activity->text }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Add Time Modal --}}
            <div class="modal fade" id="ModalTimer" tabindex="-1" role="dialog" aria-labelledby="ModalTimerLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ModalTimerLabel">{{ __('Time management') }} ({{ $tasks->title }})</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ url('tasks/updatetime/' . $tasks->id) }}">
                            @csrf
                            <div class="modal-body crm-form-page">
                                <x-form.input
                                    layout="standard"
                                    field-class="crm-form-group"
                                    class="crm-form-control"
                                    name="title"
                                    label="{{ __('Title') }}"
                                    type="text"
                                    placeholder="Title"
                                />
                                <x-form.input
                                    layout="standard"
                                    field-class="crm-form-group"
                                    class="crm-form-control"
                                    name="comment"
                                    label="{{ __('Description') }}"
                                    type="textarea"
                                    placeholder="Description"
                                />
                                <x-form.input
                                    layout="standard"
                                    field-class="crm-form-group"
                                    class="crm-form-control"
                                    name="value"
                                    label="{{ __('Hourly price') }}"
                                    type="text"
                                    placeholder="300"
                                />
                                <x-form.input
                                    layout="standard"
                                    field-class="crm-form-group"
                                    class="crm-form-control"
                                    name="time"
                                    label="{{ __('Time spend') }}"
                                    type="text"
                                    placeholder="3"
                                />
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="crm-btn" data-bs-dismiss="modal">{{ __('Close') }}</button>
                                <button type="submit" class="crm-btn crm-btn-primary">{{ __('Register time') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Create Invoice Modal --}}
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel">{{ __('Create invoice') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ url('tasks/invoice/' . $tasks->id) }}">
                            @csrf
                            <div class="modal-body">
                                @if ($apiconnected)
                                    @foreach ($contacts as $key => $contact)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="invoiceContact" id="contact_{{ $key }}" value="{{ $contact['guid'] }}">
                                            <label class="form-check-label" for="contact_{{ $key }}">{{ $contact['name'] }}</label>
                                        </div>
                                    @endforeach
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="sendMail" id="sendMail" value="1">
                                        <label class="form-check-label" for="sendMail">{{ __('Send mail with invoice to Customer? (Checked = Yes)') }}</label>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="crm-btn" data-bs-dismiss="modal">{{ __('Close') }}</button>
                                <button type="submit" class="crm-btn crm-btn-primary">{{ __('Create') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush
