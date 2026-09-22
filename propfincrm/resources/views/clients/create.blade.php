@extends('layouts.master')
@section('page-title', 'Create Client')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')

@section('content')
@php
    $data = Session::get('data');
@endphp

<div id="clients-page" class="crm-form-page">

    {{-- Page Header --}}
    <div class="crm-form-page-header">
        <a href="{{ route('clients.index') }}" class="crm-back-link">
            <i class="fas fa-arrow-left"></i>
        </a>
        <strong>{{ __('Create Client') }}</strong>
    </div>

    {{-- VAT / CVR Lookup --}}
    <div class="crm-form-body" style="border-bottom:1px solid #e2e8f0; padding-bottom:16px; margin-bottom:0;">
        <p class="mb-2" style="font-size:12px;font-weight:600;color:#475569;">{{ __('VAT Lookup (DK)') }}</p>
        <form action="{{ url('/clients/create/cvrapi') }}" method="POST">
            @csrf
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <input
                    type="text"
                    name="vat"
                    class="crm-form-control"
                    style="max-width:240px;"
                    placeholder="{{ __('Insert company VAT number') }}"
                    value="{{ old('vat') }}"
                />
                <button type="submit" class="crm-btn crm-btn-primary">
                    <i class="fas fa-search"></i>
                    {{ __('Get client info') }}
                </button>
                <span style="font-size:11px;color:#94a3b8;" title="{{ __('Only for DK, at the moment.') }}">
                    <i class="fas fa-info-circle"></i> {{ __('DK only') }}
                </span>
            </div>
        </form>
    </div>

    {{-- Main Create Form --}}
    <div class="crm-card" style="border:0;border-radius:0;">
        <form action="{{ route('clients.store') }}" method="POST">
            @csrf
            @include('clients.form', ['submitButtonText' => __('Create New Client')])
        </form>
    </div>

</div>
@endsection
