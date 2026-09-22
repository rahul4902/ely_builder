@extends('layouts.master')

@section('page-title', 'New Role')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')

@section('content')
<div id="role-create-page" class="crm-form-page">
    <div class="crm-form-page-header">
        <a href="{{ route('roles.index') }}" class="crm-back-link">
            <i class="fa fa-arrow-left"></i> Roles
        </a>
        <strong>Create Role</strong>
    </div>

    <div class="crm-form-body" style="max-width:640px;">
        <div class="crm-card">
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf

                <x-form.input
                    layout="standard"
                    field-class="crm-form-group"
                    class="crm-form-control"
                    name="name"
                    id="role-name"
                    label="Role name"
                    type="text"
                    :value="old('name')"
                    required
                />

                <x-form.input
                    layout="standard"
                    field-class="crm-form-group"
                    class="crm-form-control"
                    name="description"
                    id="role-description"
                    label="Description"
                    type="textarea"
                    :value="old('description')"
                />

                <div style="padding-top:4px;">
                    <button type="submit" class="crm-btn crm-btn-primary">
                        <i class="fa fa-check"></i> Create role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
