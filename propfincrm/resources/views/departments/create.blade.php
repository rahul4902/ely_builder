@section('page-title', 'Create Department')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')
@extends('layouts.master')

@section('content')
<div id="department-create-page" class="crm-form-page">

    {{-- Page header --}}
    <div class="crm-form-page-header">
        <a href="{{ route('departments.index') }}" class="crm-back-link">
            <i class="fa fa-arrow-left"></i> Departments
        </a>
        <strong>Create Department</strong>
    </div>

    {{-- Form card --}}
    <div class="crm-form-body" style="max-width:640px;">
        <div class="crm-card">
            <form method="POST" action="{{ route('departments.store') }}">
                @csrf

                <x-form.input
                    layout="standard"
                    field-class="crm-form-group"
                    class="crm-form-control"
                    name="name"
                    id="department-name"
                    label="Department name"
                    type="text"
                    :value="old('name')"
                    required
                />

                <x-form.input
                    layout="standard"
                    field-class="crm-form-group"
                    class="crm-form-control"
                    name="description"
                    id="department-description"
                    label="Description"
                    type="textarea"
                    :value="old('description')"
                />

                <div style="padding-top:4px;">
                    <button type="submit" class="crm-btn crm-btn-primary">
                        <i class="fa fa-check"></i> Create Department
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
