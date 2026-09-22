@extends('layouts.master')
@section('page-title', 'Client View')
@section('main-class', 'p-4')

@section('content')
<div id="clients-show-page">
    <div class="row mb-3">
        @if(isset($lead))
        <div class="col-12 col-md-6 mb-3">
            @include('partials.clientheader')
        </div>
        @endif
        <div class="col-12 col-md-{{ isset($lead) ? '6' : '12' }} mb-3">
            <div class="crm-card p-0 overflow-hidden">
                @include('partials.userheader')
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Main tab section --}}
        <div class="col-lg-8 mb-3">
            <div class="crm-card p-0 overflow-hidden">
                <ul class="nav crm-tab-nav" id="clientTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="task-tab" data-bs-toggle="tab" data-bs-target="#task" type="button" role="tab" aria-controls="task" aria-selected="true">
                            <i class="fas fa-tasks"></i> {{ __('Tasks') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="lead-tab" data-bs-toggle="tab" data-bs-target="#lead" type="button" role="tab" aria-controls="lead" aria-selected="false">
                            <i class="fas fa-code-branch"></i> {{ __('Leads') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="document-tab" data-bs-toggle="tab" data-bs-target="#document" type="button" role="tab" aria-controls="document" aria-selected="false">
                            <i class="fab fa-dochub"></i> {{ __('Documents') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="invoice-tab" data-bs-toggle="tab" data-bs-target="#invoice" type="button" role="tab" aria-controls="invoice" aria-selected="false">
                            <i class="far fa-file-alt"></i> {{ __('Invoices') }}
                        </button>
                    </li>
                </ul>
                <div class="tab-content p-3" id="clientTabsContent">
                    <div class="tab-pane fade show active" id="task" role="tabpanel" aria-labelledby="task-tab">
                        @include('clients.tabs.tasktab')
                    </div>
                    <div class="tab-pane fade" id="lead" role="tabpanel" aria-labelledby="lead-tab">
                        @include('clients.tabs.leadtab')
                    </div>
                    <div class="tab-pane fade" id="document" role="tabpanel" aria-labelledby="document-tab">
                        @include('clients.tabs.documenttab')
                    </div>
                    <div class="tab-pane fade" id="invoice" role="tabpanel" aria-labelledby="invoice-tab">
                        @include('clients.tabs.invoicetab')
                    </div>
                </div>
            </div>
        </div>

        {{-- Right sidebar --}}
        <div class="col-lg-4 mb-3">
            <div class="crm-info-panel">
                <div class="crm-info-panel-header">
                    {{ __('Assign User') }}
                </div>
                <div class="crm-info-panel-body">
                    <form method="POST" action="{{ url('clients/updateassign', $client->id) }}">
                        @csrf
                        @method('PATCH')
                        <div class="crm-form-group">
                            <label class="crm-form-label" for="client-assigned-user">{{ __('Assigned Team Member') }}</label>
                            <select name="user_assigned_id" id="client-assigned-user" class="crm-form-control">
                                @foreach($users as $uId => $uName)
                                    <option value="{{ $uId }}" @selected(optional($client->user)->id == $uId)>{{ $uName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="crm-btn crm-btn-primary w-100">
                            <i class="fas fa-user-check"></i> {{ __('Assign new user') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    if (typeof $.fn.select2 === 'function') {
        $('#client-assigned-user').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }
});
</script>
@endpush

