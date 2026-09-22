@section('page-title', 'Invoice Details')
@extends('layouts.master')

@section('content')
<div class="row">
    @include('partials.clientheader')

    <div class="col-md-8">
        <div class="crm-card mb-3">
            <div class="crm-card-header text-center">
                <h3 class="mb-0"><strong>{{ __('Order summary') }}</strong></h3>
            </div>
            <div class="p-3">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <td><strong>{{ __('Item name') }}</strong></td>
                                <td class="text-center"><strong>{{ __('Item price') }}</strong></td>
                                <td class="text-center"><strong>{{ __('Hours used') }}</strong></td>
                                <td class="text-end"><strong>{{ __('Total') }}</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                            @php $finalPrice = 0; @endphp
                            @foreach($invoice->taskTime as $item)
                                @php $totalPrice = $item->time * $item->value @endphp
                                <tr>
                                    <td>{{ $item->title }}</td>
                                    <td class="text-center">{{ $item->value }},-</td>
                                    <td class="text-center">{{ $item->time }}</td>
                                    <td class="text-end">{{ $totalPrice }},-</td>
                                </tr>
                                @php $finalPrice += $totalPrice; @endphp
                            @endforeach

                            <tr>
                                <td class="emptyrow"></td>
                                <td class="emptyrow"></td>
                                <td class="emptyrow text-center"><strong>{{ __('Total') }}</strong></td>
                                <td class="emptyrow text-end">{{ $finalPrice }},-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(!$invoice->sent)
            <button type="button" class="crm-btn crm-btn-primary w-100 mb-3"
                    data-bs-toggle="modal"
                    data-bs-target="#ModalTimer">
                {{ __('Insert new item') }}
            </button>
        @endif
    </div>

    <div class="col-md-4">
        <div class="crm-info-panel">
            <div class="crm-info-panel-header">
                <p>{{ __('Invoice information') }}</p>
            </div>
            <div class="crm-info-panel-body p-3">
                <p class="mb-1">{{ __('Invoice sent') }}: {{ $invoice->sent ? __('yes') : __('no') }}</p>
                <p class="mb-1">{{ __('Payment Received') }}: {{ $invoice->received ? __('yes') : __('no') }}</p>

                @if($invoice->received)
                    <p class="mb-2">{{ date('d-m-Y', strtotime($invoice->payment_date)) }}</p>
                @endif

                @if(!$invoice->sent)
                    <form method="POST" action="{{ route('invoice.sent', $invoice->id) }}" class="mb-3">
                        @csrf
                        <button type="submit" class="crm-btn crm-btn-primary w-100">
                            {{ __('Set invoice as sent') }}
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('invoice.sent.reopen', $invoice->id) }}" class="mb-3">
                        @csrf
                        <button type="submit" class="crm-btn crm-btn-danger w-100">
                            {{ __('Set invoice as not sent') }}
                        </button>
                    </form>
                @endif
            </div>

            @if(!$invoice->received)
                <div class="crm-info-panel-header">
                    <p>{{ __('Invoice paid date') }}</p>
                </div>
                <div class="crm-info-panel-body p-3">
                    <form method="POST" action="{{ route('invoice.payment.date', $invoice->id) }}">
                        @csrf
                        <div class="mb-3">
                            <input type="date" name="payment_date"
                                   value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                   class="crm-form-control">
                        </div>
                        <button type="submit" class="crm-btn crm-btn-primary w-100">
                            {{ __('Set invoice as paid') }}
                        </button>
                    </form>
                </div>
            @else
                <div class="crm-info-panel-body p-3">
                    <form method="POST" action="{{ route('invoice.payment.reopen', $invoice->id) }}">
                        @csrf
                        <button type="submit" class="crm-btn crm-btn-danger w-100">
                            {{ __('Set invoice as not paid') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Add Invoice Item Modal --}}
<div class="modal fade" id="ModalTimer" tabindex="-1" role="dialog" aria-labelledby="ModalTimerLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalTimerLabel">{{ __('Time Management For This Invoice') }} ({{ $invoice->title }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('invoice.new.item', $invoice->id) }}">
                @csrf
                <div class="modal-body crm-form-page">
                    <x-form.input
                        layout="standard"
                        field-class="crm-form-group"
                        class="crm-form-control"
                        name="title"
                        label="{{ __('Title') }}"
                        type="text"
                        placeholder="Fx Consultation Meeting"
                    />
                    <x-form.input
                        layout="standard"
                        field-class="crm-form-group"
                        class="crm-form-control"
                        name="comment"
                        label="{{ __('Description') }}"
                        type="textarea"
                        placeholder="Short Comment about whats done (Will show on Invoice)"
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
                        label="{{ __('Time spend (Hours)') }}"
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
@endsection
