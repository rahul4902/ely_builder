@extends('layouts.master')
@section('page-title', 'Integrations')
@section('main-class', 'p-4')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <h4 class="mb-0 text-slate-800 font-semibold text-base">{{ __('Integrations') }}</h4>
        <p class="text-xs text-slate-500 mb-0 mt-1">{{ __('Manage your third-party billing and accounting integrations.') }}</p>
    </div>

    {{-- Dinero Integration --}}
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="crm-card h-100 d-flex flex-column">
            <div class="text-center py-3 border-bottom mb-3">
                <img src="{{ asset('imagesIntegration/dinero-logo.png') }}" class="img-fluid" style="max-height: 48px;" alt="Dinero">
            </div>

            <form method="POST" action="{{ route('integrations.store') }}" class="flex-grow-1 d-flex flex-column justify-content-between">
                @csrf
                <div>
                    <x-form.input
                        layout="standard"
                        field-class="crm-form-group"
                        class="crm-form-control"
                        name="api_key"
                        id="dinero_api_key"
                        label="{{ __('Api key') }}"
                        type="text"
                    />

                    <x-form.input
                        layout="standard"
                        field-class="crm-form-group"
                        class="crm-form-control"
                        name="org_id"
                        id="dinero_org_id"
                        label="{{ __('Organization id') }}"
                        type="text"
                    />

                    <input type="hidden" name="name" value="Dinero">
                    <input type="hidden" name="api_type" value="billing">
                </div>

                <div class="pt-3 border-top mt-3">
                    <button type="submit" class="crm-btn crm-btn-primary w-100">
                        <i class="fas fa-check"></i> {{ __('Update Dinero') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Billy Integration --}}
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="crm-card h-100 d-flex flex-column">
            <div class="text-center py-3 border-bottom mb-3">
                <img src="{{ asset('imagesIntegration/billy-logo-final_blue.png') }}" class="img-fluid" style="max-height: 48px;" alt="Billy">
            </div>

            <form method="POST" action="{{ route('integrations.store') }}" class="flex-grow-1 d-flex flex-column justify-content-between">
                @csrf
                <div>
                    <x-form.input
                        layout="standard"
                        field-class="crm-form-group"
                        class="crm-form-control"
                        name="api_key"
                        id="billy_api_key"
                        label="{{ __('Api key') }}"
                        type="text"
                    />

                    <input type="hidden" name="name" value="Billy">
                    <input type="hidden" name="api_type" value="billing">
                </div>

                <div class="pt-3 border-top mt-3">
                    <button type="submit" class="crm-btn crm-btn-primary w-100">
                        <i class="fas fa-check"></i> {{ __('Update Billy') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection