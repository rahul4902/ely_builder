@extends('layouts.master')
@section('page-title', 'Edit Client')
@section('main-class', 'p-0 bg-white min-h-[calc(100vh-40px)]')

@section('content')
<div id="clients-page" class="crm-form-page">

    {{-- Page Header --}}
    <div class="crm-form-page-header">
        <a href="{{ route('clients.index') }}" class="crm-back-link">
            <i class="fas fa-arrow-left"></i>
        </a>
        <strong>{{ __('Edit Client') }} &mdash; {{ $client->name }}</strong>
    </div>

    {{-- Edit Form --}}
    <div class="crm-card" style="border:0;border-radius:0;">
        <form action="{{ route('clients.update', $client->id) }}" method="POST">
            @csrf
            @method('PATCH')
            @include('clients.form', ['submitButtonText' => __('Update Client')])
        </form>
    </div>

</div>
@endsection
