@section('page-title', 'Create Task')
@extends('layouts.master')

@section('content')
<div class="crm-form-page-header">
    <a href="{{ route('tasks.index') }}" class="crm-btn">
        <i class="fa fa-arrow-left"></i> {{ __('Back') }}
    </a>
    <h1 class="crm-form-page-title">{{ __('Create task') }}</h1>
</div>

<div class="crm-form-page">
    <div class="crm-card">
        <div class="crm-form-body">
            <form method="POST" action="{{ route('tasks.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-12">
                        <x-form.input
                            layout="standard"
                            field-class="crm-form-group"
                            class="crm-form-control"
                            name="title"
                            label="{{ __('Title') }}"
                            type="text"
                            required
                        />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <x-form.input
                            layout="standard"
                            field-class="crm-form-group"
                            class="crm-form-control"
                            name="description"
                            label="{{ __('Description') }}"
                            type="textarea"
                        />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <x-form.input
                            layout="standard"
                            field-class="crm-form-group"
                            class="crm-form-control"
                            name="deadline"
                            label="{{ __('Deadline') }}"
                            type="date"
                            :value="\Carbon\Carbon::now()->addDays(3)->format('Y-m-d')"
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form.input
                            layout="standard"
                            field-class="crm-form-group"
                            class="crm-form-control"
                            name="status"
                            id="status"
                            label="{{ __('Status') }}"
                            type="select"
                            :options="['1' => 'Open', '2' => 'Completed']"
                        />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <x-form.input
                            layout="standard"
                            field-class="crm-form-group"
                            class="crm-form-control"
                            name="user_assigned_id"
                            id="user_assigned_id"
                            label="{{ __('Assign user') }}"
                            type="select"
                            :options="$users"
                        />
                    </div>
                    <div class="col-md-6">
                        @if(Request::get('client') != '')
                            <input type="hidden" name="client_id" value="{{ Request::get('client') }}">
                        @else
                            <x-form.input
                                layout="standard"
                                field-class="crm-form-group"
                                class="crm-form-control"
                                name="client_id"
                                id="client_id"
                                label="{{ __('Assign client') }}"
                                type="select"
                                :options="$clients"
                            />
                        @endif
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="crm-btn crm-btn-primary">
                            {{ __('Create task') }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    if (typeof $.fn.select2 === 'function') {
        $('#status, #user_assigned_id, #client_id').each(function () {
            var $el = $(this);
            if (!$el.hasClass('select2-hidden-accessible')) {
                $el.select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });
            }
        });
    }
});
</script>
@endpush
