@section('page-title', 'Settings')
@section('main-class', 'p-4')
@extends('layouts.master')

@section('content')
<div class="crm-form-page">
    <form method="POST" action="{{ url('settings/overall') }}">
        @csrf
        @method('PATCH')

        {{-- Task completion --}}
        <div class="crm-card mb-4">
            <div class="crm-card-header">{{ __('Task completion') }}</div>
            <div class="p-3">
                <p class="mb-2">
                    {{ __('If Allowed only user who are assigned the task & the admin can complete the task.') }}<br>
                    {{ __('If Not allowed anyone, can complete all tasks.') }}
                </p>
                <x-form.input
                    layout="standard"
                    field-class="crm-form-group"
                    class="crm-form-control"
                    name="task_complete_allowed"
                    id="task_complete_allowed"
                    type="select"
                    :options="[1 => __('Allowed'), 2 => __('Not allowed')]"
                    :value="$settings->task_complete_allowed"
                />
            </div>
        </div>

        {{-- Task assigning --}}
        <div class="crm-card mb-4">
            <div class="crm-card-header">{{ __('Task assigning') }}</div>
            <div class="p-3">
                <p class="mb-2">
                    {{ __('If Allowed only user who are assigned the task & the admin can assign another user.') }}<br>
                    {{ __('If Not allowed anyone, can assign another user.') }}
                </p>
                <x-form.input
                    layout="standard"
                    field-class="crm-form-group"
                    class="crm-form-control"
                    name="task_assign_allowed"
                    id="task_assign_allowed"
                    type="select"
                    :options="[1 => __('Allowed'), 2 => __('Not allowed')]"
                    :value="$settings->task_assign_allowed"
                />
            </div>
        </div>

        {{-- Lead completion --}}
        <div class="crm-card mb-4">
            <div class="crm-card-header">{{ __('Lead completion') }}</div>
            <div class="p-3">
                <p class="mb-2">
                    {{ __('If Allowed only user who are assigned the lead & the admin can complete the lead.') }}<br>
                    {{ __('If Not allowed anyone, can complete all leads.') }}
                </p>
                <x-form.input
                    layout="standard"
                    field-class="crm-form-group"
                    class="crm-form-control"
                    name="lead_complete_allowed"
                    id="lead_complete_allowed"
                    type="select"
                    :options="[1 => __('Allowed'), 2 => __('Not allowed')]"
                    :value="$settings->lead_complete_allowed"
                />
            </div>
        </div>

        {{-- Lead assigning --}}
        <div class="crm-card mb-4">
            <div class="crm-card-header">{{ __('Lead assigning') }}</div>
            <div class="p-3">
                <p class="mb-2">
                    {{ __('If Allowed only user who are assigned the lead & the admin can assign another user.') }}<br>
                    {{ __('If Not allowed anyone, can assign another user.') }}
                </p>
                <x-form.input
                    layout="standard"
                    field-class="crm-form-group"
                    class="crm-form-control"
                    name="lead_assign_allowed"
                    id="lead_assign_allowed"
                    type="select"
                    :options="[1 => __('Allowed'), 2 => __('Not allowed')]"
                    :value="$settings->lead_assign_allowed"
                />
            </div>
        </div>

        <div class="mb-3">
            <button type="submit" class="crm-btn crm-btn-primary">{{ __('Save Settings') }}</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    if (typeof $.fn.select2 === 'function') {
        $('#task_complete_allowed, #task_assign_allowed, #lead_complete_allowed, #lead_assign_allowed').each(function () {
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
